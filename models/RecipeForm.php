<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

/**
 * Создание / редактирование рецепта вместе с ингредиентами, шагами и фото.
 * Работает с таблицами recipe, ingredient, recipe_ingredient, recipe_step.
 */
class RecipeForm extends Model
{
    const STATUS_DRAFT = 'Черновик';
    const STATUS_PUBLISHED = 'Опубликовано';
    const DIFFICULTIES = ['Простая', 'Средняя', 'Для терпеливых'];
    const UPLOAD_DIR = 'uploads/recipes';
    const STEP_UPLOAD_DIR = 'uploads/steps';

    public $title;
    public $category_id;
    public $description;
    public $difficulty = 'Средняя';
    public $servings = 4;
    public $prep_time_min;
    public $cook_time_min;
    public $equipment;
    public $calories;
    public $protein;
    public $fat;
    public $carbs;
    public $secret;
    public $status;          // "draft" | "publish"
    public $remove_photo;
    public $ingredients = [];
    public $steps = [];

    /** @var Recipe */
    private $_recipe;
    private $_userId;
    private $_messages = [];
    /** @var string[] фото шагов, которые уже есть у редактируемого рецепта */
    private $_existingStepPhotos = [];
    /** @var string[] файлы, сохранённые в рамках текущего запроса (для отката) */
    private $_savedFiles = [];

    public function __construct(Recipe $recipe, $userId, $config = [])
    {
        $this->_recipe = $recipe;
        $this->_userId = $userId;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['title', 'description', 'equipment', 'secret'], 'trim'],
            [['title'], 'required', 'message' => 'Укажите название блюда'],
            ['title', 'string', 'max' => 200, 'tooLong' => 'Название не длиннее 200 символов'],
            ['category_id', 'required', 'message' => 'Выберите категорию'],
            ['category_id', 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id', 'message' => 'Такой категории нет'],
            ['difficulty', 'in', 'range' => self::DIFFICULTIES, 'message' => 'Неверная сложность'],
            ['servings', 'default', 'value' => 4],
            ['servings', 'integer', 'min' => 1, 'max' => 100, 'message' => 'Количество порций — целое число', 'tooSmall' => 'Порций должно быть не меньше 1', 'tooBig' => 'Порций не больше 100'],
            [['prep_time_min', 'cook_time_min', 'calories'], 'default', 'value' => null],
            [['prep_time_min', 'cook_time_min', 'calories'], 'integer', 'min' => 0, 'max' => 100000, 'message' => 'Время и калории — целые числа', 'tooSmall' => 'Значение не может быть отрицательным'],
            [['protein', 'fat', 'carbs'], 'filter', 'filter' => function ($v) { return is_string($v) ? str_replace(',', '.', $v) : $v; }],
            [['protein', 'fat', 'carbs'], 'default', 'value' => null],
            [['protein', 'fat', 'carbs'], 'number', 'min' => 0, 'max' => 9999.9, 'message' => 'Белки, жиры и углеводы — числа', 'tooSmall' => 'Значение не может быть отрицательным'],
            ['equipment', 'string', 'max' => 255, 'tooLong' => 'Оборудование — не длиннее 255 символов'],
            [['description', 'secret'], 'string'],
            ['status', 'in', 'range' => ['draft', 'publish']],
            ['remove_photo', 'boolean'],
            [['ingredients', 'steps'], 'safe'],
        ];
    }

    /** Пересобираем сообщения об ошибках в один плоский список. */
    public function getErrorMessages()
    {
        $out = $this->_messages;
        foreach ($this->getFirstErrors() as $e) {
            $out[] = $e;
        }
        return array_values(array_unique($out));
    }

    private function cleanIngredients()
    {
        $rows = [];
        $seen = [];
        $n = 0;
        foreach ((array)$this->ingredients as $row) {
            $name = trim(preg_replace('/\s+/u', ' ', (string)($row['name'] ?? '')));
            $qty = trim(str_replace(',', '.', (string)($row['quantity'] ?? '')));
            $unit = trim((string)($row['unit'] ?? ''));
            if ($name === '' && $qty === '' && $unit === '') {
                continue;
            }
            $n++;
            if ($name === '') {
                $this->_messages[] = "Ингредиент №$n: укажите название";
                continue;
            }
            if (mb_strlen($name) > 255) {
                $this->_messages[] = "Ингредиент №$n: слишком длинное название";
                continue;
            }
            if ($qty !== '' && (!is_numeric($qty) || (float)$qty < 0 || (float)$qty > 99999999)) {
                $this->_messages[] = "Ингредиент «{$name}»: количество должно быть числом";
                continue;
            }
            if (mb_strlen($unit) > 255) {
                $this->_messages[] = "Ингредиент «{$name}»: слишком длинная единица измерения";
                continue;
            }
            $key = mb_strtolower($name);
            if (isset($seen[$key])) {
                $this->_messages[] = "Ингредиент «{$name}» указан дважды";
                continue;
            }
            $seen[$key] = true;
            $rows[] = ['name' => $name, 'quantity' => $qty === '' ? null : (float)$qty, 'unit' => $unit === '' ? null : $unit];
        }
        return $rows;
    }

    private function imageValidator()
    {
        return new FileValidator([
            'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
            'checkExtensionByMimeType' => true,
            'maxSize' => 10 * 1024 * 1024,
            'tooBig' => 'Фото больше 10 МБ',
            'wrongExtension' => 'Фото: допустимы JPG, PNG, WEBP, GIF',
            'uploadRequired' => 'Не удалось загрузить фото',
        ]);
    }

    private function cleanSteps()
    {
        $rows = [];
        $n = 0;
        foreach ((array)$this->steps as $key => $row) {
            $title = trim((string)($row['title'] ?? ''));
            $desc = trim((string)($row['description'] ?? ''));
            $dur = trim((string)($row['duration_min'] ?? ''));
            $keep = trim((string)($row['keep_photo'] ?? ''));
            $file = UploadedFile::getInstanceByName('step_photo_' . $key);
            if ($file !== null && $file->error === UPLOAD_ERR_NO_FILE) {
                $file = null;
            }
            if ($title === '' && $desc === '' && $dur === '' && $file === null) {
                continue;
            }
            $n++;
            if ($desc === '') {
                $this->_messages[] = "Шаг №$n: опишите этап";
                continue;
            }
            if (mb_strlen($title) > 255) {
                $this->_messages[] = "Шаг №$n: слишком длинное название";
                continue;
            }
            if ($dur !== '' && (!ctype_digit($dur) || (int)$dur > 100000)) {
                $this->_messages[] = "Шаг №$n: длительность — целое число минут";
                continue;
            }
            if ($file !== null) {
                $error = null;
                if (!$this->imageValidator()->validate($file, $error)) {
                    $this->_messages[] = "Шаг №$n: $error";
                    continue;
                }
            }
            // оставить можно только фото, которое уже принадлежит этому рецепту
            $keepPhoto = ($file === null && $keep !== '' && in_array($keep, $this->_existingStepPhotos, true)) ? $keep : null;
            $rows[] = [
                'title' => $title === '' ? null : $title,
                'description' => $desc,
                'duration_min' => $dur === '' ? null : (int)$dur,
                'file' => $file,
                'keep_photo' => $keepPhoto,
            ];
        }
        return $rows;
    }

    private function storeImage(UploadedFile $file, $subdir)
    {
        $dir = Yii::getAlias('@webroot') . '/' . $subdir;
        FileHelper::createDirectory($dir);
        $name = Yii::$app->security->generateRandomString(16) . '.' . strtolower($file->extension);
        if (!$file->saveAs($dir . '/' . $name)) {
            return null;
        }
        $path = $subdir . '/' . $name;
        $this->_savedFiles[] = $path;
        return $path;
    }

    private function deleteUploaded($path, $subdir)
    {
        if ($path && strpos($path, $subdir . '/') === 0) {
            @unlink(Yii::getAlias('@webroot') . '/' . $path);
        }
    }

    public function save()
    {
        $this->_messages = [];
        $this->_savedFiles = [];
        $this->_existingStepPhotos = [];
        if (!$this->_recipe->isNewRecord) {
            $this->_existingStepPhotos = array_values(array_filter(
                RecipeStep::find()->select('photo')->where(['recipe_id' => $this->_recipe->id])->column()
            ));
        }
        $ok = $this->validate();
        $ingredients = $this->cleanIngredients();
        $steps = $this->cleanSteps();
        $publish = $this->status === 'publish';

        if ($publish && !$this->_messages && $ok) {
            if (!$ingredients) {
                $this->_messages[] = 'Для публикации добавьте хотя бы один ингредиент';
            }
            if (!$steps) {
                $this->_messages[] = 'Для публикации добавьте хотя бы один шаг приготовления';
            }
        }

        // фото
        $file = UploadedFile::getInstanceByName('photo');
        if ($file !== null && $file->error !== UPLOAD_ERR_NO_FILE) {
            $validator = new FileValidator([
                'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                'checkExtensionByMimeType' => true,
                'maxSize' => 15 * 1024 * 1024,
                'tooBig' => 'Фото больше 15 МБ',
                'wrongExtension' => 'Фото: допустимы JPG, PNG, WEBP, GIF',
                'uploadRequired' => 'Не удалось загрузить фото',
            ]);
            $error = null;
            if (!$validator->validate($file, $error)) {
                $this->_messages[] = $error;
            }
        } else {
            $file = null;
        }

        if (!$ok || $this->_messages) {
            return false;
        }

        $recipe = $this->_recipe;
        $isNew = $recipe->isNewRecord;
        $oldPhoto = $recipe->photo;
        $now = date('Y-m-d H:i:s');

        $recipe->user_id = $this->_userId;
        $recipe->title = $this->title;
        $recipe->category_id = (int)$this->category_id;
        $recipe->description = $this->description === '' ? null : $this->description;
        $recipe->difficulty = $this->difficulty;
        $recipe->servings = (int)$this->servings;
        $recipe->prep_time_min = $this->prep_time_min;
        $recipe->cook_time_min = $this->cook_time_min;
        $recipe->equipment = $this->equipment === '' ? null : $this->equipment;
        $recipe->secret = $this->secret === '' ? null : $this->secret;
        $recipe->calories = $this->calories;
        $recipe->protein = $this->protein;
        $recipe->fat = $this->fat;
        $recipe->carbs = $this->carbs;
        $recipe->status = $publish ? self::STATUS_PUBLISHED : self::STATUS_DRAFT;
        $recipe->updated_at = $now;
        if ($isNew) {
            $recipe->created_at = $now;
        }

        $newPhoto = null;
        if ($file !== null) {
            $dir = Yii::getAlias('@webroot') . '/' . self::UPLOAD_DIR;
            FileHelper::createDirectory($dir);
            $name = Yii::$app->security->generateRandomString(16) . '.' . strtolower($file->extension);
            if (!$file->saveAs($dir . '/' . $name)) {
                $this->_messages[] = 'Не удалось сохранить фото на сервере';
                return false;
            }
            $newPhoto = self::UPLOAD_DIR . '/' . $name;
            $recipe->photo = $newPhoto;
        } elseif ($this->remove_photo) {
            $recipe->photo = null;
        }

        $tx = Yii::$app->db->beginTransaction();
        try {
            if (!$recipe->save()) {
                throw new \RuntimeException(implode('; ', $recipe->getFirstErrors()));
            }

            RecipeIngredient::deleteAll(['recipe_id' => $recipe->id]);
            RecipeStep::deleteAll(['recipe_id' => $recipe->id]);

            foreach ($ingredients as $pos => $row) {
                // найти по названию (без учёта регистра) или создать
                $ing = Ingredient::find()->where(['name' => $row['name']])->one();
                if ($ing === null) {
                    $ing = new Ingredient(['name' => $row['name']]);
                    if (!$ing->save()) {
                        throw new \RuntimeException(implode('; ', $ing->getFirstErrors()));
                    }
                }
                $ri = new RecipeIngredient([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ing->id,
                    'quantity' => $row['quantity'],
                    'unit' => $row['unit'],
                    'position' => $pos + 1,
                ]);
                if (!$ri->save()) {
                    throw new \RuntimeException(implode('; ', $ri->getFirstErrors()));
                }
            }

            $usedStepPhotos = [];
            foreach ($steps as $i => $row) {
                $photo = $row['keep_photo'];
                if ($row['file'] !== null) {
                    $photo = $this->storeImage($row['file'], self::STEP_UPLOAD_DIR);
                    if ($photo === null) {
                        throw new \RuntimeException('Не удалось сохранить фото шага №' . ($i + 1));
                    }
                }
                if ($photo) {
                    $usedStepPhotos[] = $photo;
                }
                $st = new RecipeStep([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i + 1,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'duration_min' => $row['duration_min'],
                    'photo' => $photo,
                ]);
                if (!$st->save()) {
                    throw new \RuntimeException(implode('; ', $st->getFirstErrors()));
                }
            }

            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            if ($newPhoto) {
                @unlink(Yii::getAlias('@webroot') . '/' . $newPhoto);
            }
            foreach ($this->_savedFiles as $f) {
                @unlink(Yii::getAlias('@webroot') . '/' . $f);
            }
            Yii::error($e->getMessage(), __METHOD__);
            $this->_messages[] = 'Не удалось сохранить рецепт: ' . $e->getMessage();
            return false;
        }

        // фото шагов, которые больше не используются
        foreach (array_diff($this->_existingStepPhotos, $usedStepPhotos) as $old) {
            $this->deleteUploaded($old, self::STEP_UPLOAD_DIR);
        }

        // старый загруженный файл больше не нужен
        if ($oldPhoto && $oldPhoto !== $recipe->photo && strpos($oldPhoto, self::UPLOAD_DIR . '/') === 0) {
            @unlink(Yii::getAlias('@webroot') . '/' . $oldPhoto);
        }
        return true;
    }
}
