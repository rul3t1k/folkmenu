<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%recipes}}`.
 */
class m261107_070644_create_recipes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%recipes}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'instructions' => $this->text(),
            'prep_time' => $this->integer(),
            'cook_time' => $this->integer(),
            'servings' => $this->integer(),
            'calories' => $this->integer(),
            'image' => $this->string(255),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Внешние ключи
        $this->addForeignKey(
            'fk-recipes-category_id',
            '{{%recipes}}',
            'category_id',
            '{{%categories}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-recipes-user_id',
            '{{%recipes}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-recipes-category_id', '{{%recipes}}');
        $this->dropForeignKey('fk-recipes-user_id', '{{%recipes}}');
        $this->dropTable('{{%recipes}}');
    }
}
