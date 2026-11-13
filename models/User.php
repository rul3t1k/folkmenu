<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $fullname
 * @property string $mail
 * @property string $phone
 * @property string|null $bio
 * @property string|null $avatar
 * @property int $role
 *
 * @property Favorite[] $favorites
 * @property Recipe[] $recipes
 * @property Recipe[] $recipes0
 * @property ShoppingListItem[] $shoppingListItems
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'mail', 'password', 'fullname', 'phone'], 'required'],
            [['bio'], 'safe'],
            ['avatar', 'string', 'max' => 255],
            [['role'], 'integer'],
            [['role'], 'default', 'value' => self::ROLE_USER],
            ['mail', 'email', 'message' => 'Неверный формат почты'],
            ['username', 'unique', 'message' => 'Такой логин уже зарегистрирован'],
            ['mail', 'unique', 'message' => 'Эта почта уже зарегистрирована'],
            [['username', 'mail', 'password', 'fullname', 'phone', 'bio'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'fullname' => 'ФИО',
            'mail' => 'Электронная почта',
            'phone' => 'Телефон',
            'bio' => 'О себе',
            'avatar' => 'Аватар',
            'role' => 'Роль',
        ];
    }

    /**
     * Хэшируем пароль перед сохранением (если он ещё не захэширован).
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (($insert || $this->isAttributeChanged('password')) && !self::isHash($this->password)) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        return true;
    }

    private static function isHash($value)
    {
        return is_string($value) && preg_match('/^\$2[aby]\$\d{2}\$.{53}$/', $value) === 1;
    }

    public function getIsAdmin()
    {
        return (int)$this->role === self::ROLE_ADMIN;
    }

    /** Приводит телефон к виду +7 (999) 123-45-67, если в нём 11 цифр. */
    public static function normalizePhone($value)
    {
        $digits = preg_replace('/\D+/', '', (string)$value);
        if (strlen($digits) === 11 && ($digits[0] === '7' || $digits[0] === '8')) {
            return sprintf('+7 (%s) %s-%s-%s', substr($digits, 1, 3), substr($digits, 4, 3), substr($digits, 7, 2), substr($digits, 9, 2));
        }
        return $value;
    }

    // ---------- IdentityInterface ----------

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // токены доступа не используются
    }

    /**
     * Поиск по логину или по почте.
     * @param string $login
     * @return static|null
     */
    public static function findByUsername($login)
    {
        return static::find()
            ->where(['username' => $login])
            ->orWhere(['mail' => $login])
            ->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * В таблице нет колонки auth_key, поэтому ключ вычисляется из хэша пароля.
     * При смене пароля все "запомненные" входы автоматически становятся недействительными.
     */
    public function getAuthKey()
    {
        return hash_hmac('sha256', $this->id . '|' . $this->password, Yii::$app->request->cookieValidationKey);
    }

    public function validateAuthKey($authKey)
    {
        return hash_equals($this->getAuthKey(), (string)$authKey);
    }

    /**
     * Проверка пароля. Для старых записей с паролем в открытом виде
     * сравниваем напрямую и перехэшируем при успешном входе.
     */
    public function validatePassword($password)
    {
        if (self::isHash($this->password)) {
            return Yii::$app->security->validatePassword($password, $this->password);
        }
        if (hash_equals((string)$this->password, (string)$password)) {
            $this->password = $password;
            $this->save(false, ['password']); // beforeSave захэширует
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Favorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Recipes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipes()
    {
        return $this->hasMany(Recipe::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Recipes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipes0()
    {
        return $this->hasMany(Recipe::className(), ['id' => 'recipe_id'])->viaTable('favorite', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ShoppingListItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::className(), ['user_id' => 'id']);
    }
}
