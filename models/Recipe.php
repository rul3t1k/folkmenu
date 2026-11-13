<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recipe".
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $servings
 * @property string $title
 * @property string|null $description
 * @property string $difficulty
 * @property int|null $prep_time_min
 * @property int|null $cook_time_min
 * @property string|null $equipment
 * @property string|null $secret
 * @property string|null $photo
 * @property int|null $calories
 * @property float|null $protein
 * @property float|null $fat
 * @property float|null $carbs
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property Favorite[] $favorites
 * @property Ingredient[] $ingredients
 * @property RecipeIngredient[] $recipeIngredients
 * @property RecipeStep[] $recipeSteps
 * @property ShoppingListItem[] $shoppingListItems
 * @property User $user
 * @property User[] $users
 */
class Recipe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recipe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'title'], 'required'],
            [['user_id', 'category_id', 'prep_time_min', 'cook_time_min', 'calories', 'servings'], 'integer'],
            [['servings'], 'default', 'value' => 4],
            [['description', 'difficulty', 'secret', 'status'], 'string'],
            [['protein', 'fat', 'carbs'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['equipment', 'photo'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
            'title' => 'Title',
            'description' => 'Description',
            'difficulty' => 'Difficulty',
            'prep_time_min' => 'Prep Time Min',
            'cook_time_min' => 'Cook Time Min',
            'equipment' => 'Equipment',
            'secret' => 'Secret',
            'photo' => 'Photo',
            'calories' => 'Calories',
            'protein' => 'Protein',
            'fat' => 'Fat',
            'carbs' => 'Carbs',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Favorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::className(), ['recipe_id' => 'id']);
    }

    /**
     * Gets query for [[Ingredients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIngredients()
    {
        return $this->hasMany(Ingredient::className(), ['id' => 'ingredient_id'])->viaTable('recipe_ingredient', ['recipe_id' => 'id']);
    }

    /**
     * Gets query for [[RecipeIngredients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::className(), ['recipe_id' => 'id']);
    }

    /**
     * Gets query for [[RecipeSteps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipeSteps()
    {
        return $this->hasMany(RecipeStep::className(), ['recipe_id' => 'id']);
    }

    /**
     * Gets query for [[ShoppingListItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::className(), ['recipe_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('favorite', ['recipe_id' => 'id']);
    }

    /** Адрес фото для вывода на странице. */
    public static function photoUrl($photo)
    {
        if (!$photo) {
            return null;
        }
        if (preg_match('~^(https?:)?//|^/~', $photo)) {
            return $photo;
        }
        $path = (strpos($photo, '/') === false ? 'images/' : '') . $photo;
        return \Yii::getAlias('@web') . '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /** Удаляет рецепт вместе со связанными данными и загруженными файлами. */
    public function deleteCascade()
    {
        $photo = $this->photo;
        $stepPhotos = RecipeStep::find()->select('photo')->where(['recipe_id' => $this->id])->column();
        $tx = static::getDb()->beginTransaction();
        try {
            RecipeIngredient::deleteAll(['recipe_id' => $this->id]);
            RecipeStep::deleteAll(['recipe_id' => $this->id]);
            Favorite::deleteAll(['recipe_id' => $this->id]);
            ShoppingListItem::updateAll(['recipe_id' => null], ['recipe_id' => $this->id]);
            $this->delete();
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            \Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        foreach (array_merge([$photo], $stepPhotos) as $f) {
            if ($f && (strpos($f, 'uploads/recipes/') === 0 || strpos($f, 'uploads/steps/') === 0)) {
                @unlink(\Yii::getAlias('@webroot') . '/' . $f);
            }
        }
        return true;
    }
}
