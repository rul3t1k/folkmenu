<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%favorites}}`.
 */
class m261107_065701_create_favorites_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%favorites}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'recipe_id' => $this->integer()->notNull(),
        ]);

        // Внешние ключи для связи с пользователями и рецептами
        $this->addForeignKey(
            'fk-favorites-user_id',
            '{{%favorites}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-favorites-recipe_id',
            '{{%favorites}}',
            'recipe_id',
            '{{%recipes}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-favorites-recipe_id', '{{%favorites}}');
        $this->dropForeignKey('fk-favorites-user_id', '{{%favorites}}');
        $this->dropTable('{{%favorites}}');
    }
}
