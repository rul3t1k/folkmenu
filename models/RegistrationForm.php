<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Форма регистрации нового пользователя.
 */
class RegistrationForm extends Model
{
    public $fullname;
    public $username;
    public $password;
    public $mail;
    public $phone;

    public function rules()
    {
        return [
            [['fullname', 'username', 'password', 'mail', 'phone'], 'trim'],
            [['fullname', 'username', 'password', 'mail', 'phone'], 'required', 'message' => 'Заполните поле'],

            ['fullname', 'match', 'pattern' => '/^[\p{L}\s\-\.]+$/u', 'message' => 'ФИО может содержать только буквы, пробелы и дефис'],
            ['fullname', 'string', 'max' => 255],

            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Логин: только латинские буквы, цифры и «_»'],
            ['username', 'string', 'min' => 3, 'max' => 50, 'tooShort' => 'Логин не короче 3 символов', 'tooLong' => 'Логин не длиннее 50 символов'],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username', 'message' => 'Такой логин уже занят'],

            ['password', 'string', 'min' => 8, 'max' => 255, 'tooShort' => 'Пароль не короче 8 символов'],

            ['mail', 'email', 'message' => 'Неверный формат почты'],
            ['mail', 'string', 'max' => 255],
            ['mail', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'mail', 'message' => 'Эта почта уже зарегистрирована'],

            ['phone', 'filter', 'filter' => [User::class, 'normalizePhone']],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Телефон: +7 (999) 123-45-67'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullname' => 'ФИО',
            'username' => 'Логин',
            'password' => 'Пароль',
            'mail' => 'Электронная почта',
            'phone' => 'Номер телефона',
        ];
    }

    /**
     * Создаёт пользователя. Пароль хэшируется в User::beforeSave().
     * @return User|null
     */
    public function register()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->fullname = $this->fullname;
        $user->username = $this->username;
        $user->password = $this->password;
        $user->mail = $this->mail;
        $user->phone = $this->phone;
        $user->role = User::ROLE_USER;

        return $user->save() ? $user : null;
    }
}
