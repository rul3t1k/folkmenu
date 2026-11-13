<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

/**
 * Форма редактирования профиля текущего пользователя.
 */
class ProfileForm extends Model
{
    public $fullname;
    public $mail;
    public $phone;
    public $bio;
    public $newPassword;
    public $remove_avatar;

    const UPLOAD_DIR = 'uploads/avatars';

    /** @var User */
    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        $this->fullname = $user->fullname;
        $this->mail = $user->mail;
        $this->phone = $user->phone;
        $this->bio = $user->bio;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['fullname', 'mail', 'phone', 'bio', 'newPassword'], 'trim'],
            [['fullname', 'mail', 'phone'], 'required', 'message' => 'Заполните поле'],

            ['fullname', 'match', 'pattern' => '/^[\p{L}\s\-\.]+$/u', 'message' => 'ФИО может содержать только буквы, пробелы и дефис'],
            ['fullname', 'string', 'max' => 255],

            ['mail', 'email', 'message' => 'Неверный формат почты'],
            ['mail', 'string', 'max' => 255],
            ['mail', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'mail',
                'filter' => ['<>', 'id', $this->_user->id], 'message' => 'Эта почта уже используется'],

            ['phone', 'filter', 'filter' => [User::class, 'normalizePhone']],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Телефон: +7 (999) 123-45-67'],

            ['bio', 'string', 'max' => 255, 'tooLong' => 'Не длиннее 255 символов'],
            ['remove_avatar', 'boolean'],
            ['newPassword', 'string', 'min' => 8, 'max' => 255, 'skipOnEmpty' => true, 'tooShort' => 'Пароль не короче 8 символов'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullname' => 'Имя и Фамилия',
            'mail' => 'Эл. почта',
            'phone' => 'Телефон',
            'bio' => 'О себе',
            'newPassword' => 'Новый пароль',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        // аватар
        $file = UploadedFile::getInstanceByName('avatar');
        if ($file !== null && $file->error !== UPLOAD_ERR_NO_FILE) {
            $validator = new FileValidator([
                'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                'checkExtensionByMimeType' => true,
                'maxSize' => 10 * 1024 * 1024,
                'tooBig' => 'Аватар больше 10 МБ',
                'wrongExtension' => 'Аватар: допустимы JPG, PNG, WEBP, GIF',
                'uploadRequired' => 'Не удалось загрузить аватар',
            ]);
            $error = null;
            if (!$validator->validate($file, $error)) {
                $this->addError('avatar', $error);
                return false;
            }
        } else {
            $file = null;
        }

        $user = $this->_user;
        $oldAvatar = $user->avatar;
        $newAvatar = null;

        if ($file !== null) {
            $dir = Yii::getAlias('@webroot') . '/' . self::UPLOAD_DIR;
            FileHelper::createDirectory($dir);
            $name = Yii::$app->security->generateRandomString(16) . '.' . strtolower($file->extension);
            if (!$file->saveAs($dir . '/' . $name)) {
                $this->addError('avatar', 'Не удалось сохранить аватар на сервере');
                return false;
            }
            $newAvatar = self::UPLOAD_DIR . '/' . $name;
            $user->avatar = $newAvatar;
        } elseif ($this->remove_avatar) {
            $user->avatar = null;
        }

        $user->fullname = $this->fullname;
        $user->mail = $this->mail;
        $user->phone = $this->phone;
        $user->bio = $this->bio === '' ? null : $this->bio;
        if ($this->newPassword !== '' && $this->newPassword !== null) {
            $user->password = $this->newPassword; // захэшируется в beforeSave
        }

        if (!$user->save()) {
            if ($newAvatar) {
                @unlink(Yii::getAlias('@webroot') . '/' . $newAvatar);
            }
            $user->avatar = $oldAvatar;
            foreach ($user->getFirstErrors() as $attr => $msg) {
                $this->addError($attr, $msg);
            }
            return false;
        }

        // старый загруженный файл больше не нужен
        if ($oldAvatar && $oldAvatar !== $user->avatar && strpos($oldAvatar, self::UPLOAD_DIR . '/') === 0) {
            @unlink(Yii::getAlias('@webroot') . '/' . $oldAvatar);
        }
        return true;
    }
}
