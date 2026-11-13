<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingredient".
 *
 * @property int $id
 * @property string $name
 *
 * @property RecipeIngredient[] $recipeIngredients
 * @property Recipe[] $recipes
 * @property ShoppingListItem[] $shoppingListItems
 */
class Ingredient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingredient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[RecipeIngredients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::className(), ['ingredient_id' => 'id']);
    }

    /**
     * Gets query for [[Recipes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipes()
    {
        return $this->hasMany(Recipe::className(), ['id' => 'recipe_id'])->viaTable('recipe_ingredient', ['ingredient_id' => 'id']);
    }

    /**
     * Gets query for [[ShoppingListItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::className(), ['ingredient_id' => 'id']);
    }
}
