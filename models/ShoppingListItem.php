<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shopping_list_item".
 *
 * @property int $id
 * @property int $user_id
 * @property int $ingredient_id
 * @property int|null $recipe_id
 * @property float|null $quantity
 * @property string|null $unit
 * @property int $is_bought
 * @property string $created_at
 *
 * @property Ingredient $ingredient
 * @property Recipe $recipe
 * @property User $user
 */
class ShoppingListItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shopping_list_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'ingredient_id'], 'required'],
            [['user_id', 'ingredient_id', 'recipe_id', 'is_bought'], 'integer'],
            [['quantity'], 'number'],
            [['created_at'], 'safe'],
            [['unit'], 'string', 'max' => 255],
            [['ingredient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ingredient::className(), 'targetAttribute' => ['ingredient_id' => 'id']],
            [['recipe_id'], 'exist', 'skipOnError' => true, 'targetClass' => Recipe::className(), 'targetAttribute' => ['recipe_id' => 'id']],
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
            'ingredient_id' => 'Ingredient ID',
            'recipe_id' => 'Recipe ID',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'is_bought' => 'Is Bought',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Ingredient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIngredient()
    {
        return $this->hasOne(Ingredient::className(), ['id' => 'ingredient_id']);
    }

    /**
     * Gets query for [[Recipe]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipe()
    {
        return $this->hasOne(Recipe::className(), ['id' => 'recipe_id']);
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
}
