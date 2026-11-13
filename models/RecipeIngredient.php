<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recipe_ingredient".
 *
 * @property int $id
 * @property int $recipe_id
 * @property int $ingredient_id
 * @property float|null $quantity
 * @property string|null $unit
 * @property string|null $note
 * @property int $position
 *
 * @property Ingredient $ingredient
 * @property Recipe $recipe
 */
class RecipeIngredient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recipe_ingredient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['recipe_id', 'ingredient_id'], 'required'],
            [['recipe_id', 'ingredient_id', 'position'], 'integer'],
            [['quantity'], 'number'],
            [['unit', 'note'], 'string', 'max' => 255],
            [['recipe_id', 'ingredient_id'], 'unique', 'targetAttribute' => ['recipe_id', 'ingredient_id']],
            [['ingredient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ingredient::className(), 'targetAttribute' => ['ingredient_id' => 'id']],
            [['recipe_id'], 'exist', 'skipOnError' => true, 'targetClass' => Recipe::className(), 'targetAttribute' => ['recipe_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recipe_id' => 'Recipe ID',
            'ingredient_id' => 'Ingredient ID',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'note' => 'Note',
            'position' => 'Position',
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
}
