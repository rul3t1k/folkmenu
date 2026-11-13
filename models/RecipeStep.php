<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recipe_step".
 *
 * @property int $id
 * @property int $recipe_id
 * @property int $step_number
 * @property string|null $title
 * @property string $description
 * @property int|null $duration_min
 * @property string|null $photo
 *
 * @property Recipe $recipe
 */
class RecipeStep extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recipe_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['recipe_id', 'step_number', 'description'], 'required'],
            [['recipe_id', 'step_number', 'duration_min'], 'integer'],
            [['description'], 'string'],
            [['title', 'photo'], 'string', 'max' => 255],
            [['recipe_id', 'step_number'], 'unique', 'targetAttribute' => ['recipe_id', 'step_number']],
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
            'step_number' => 'Step Number',
            'title' => 'Title',
            'description' => 'Description',
            'duration_min' => 'Duration Min',
            'photo' => 'Photo',
        ];
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
