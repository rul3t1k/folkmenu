<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%recipe_ingredients}}`.
 */
class m261107_070817_create_recipe_ingredients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%recipe_ingredients}}', [
            'recipe_id' => $this->integer()->notNull(),
            'ingredient_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 2),
            'unit' => $this->string(50),
        ]);

        // Составной первичный ключ (чтобы связка рецепта и ингредиента была уникальной)
        $this->addPrimaryKey('pk-recipe_ingredients', '{{%recipe_ingredients}}', ['recipe_id', 'ingredient_id']);

        // Внешние ключи
        $this->addForeignKey(
            'fk-recipe_ingredients-recipe_id',
            '{{%recipe_ingredients}}',
            'recipe_id',
            '{{%recipes}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-recipe_ingredients-ingredient_id',
            '{{%recipe_ingredients}}',
            'ingredient_id',
            '{{%ingredients}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-recipe_ingredients-ingredient_id', '{{%recipe_ingredients}}');
        $this->dropForeignKey('fk-recipe_ingredients-recipe_id', '{{%recipe_ingredients}}');
        $this->dropTable('{{%recipe_ingredients}}');
    }
}
