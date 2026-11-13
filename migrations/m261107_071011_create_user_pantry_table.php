<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_pantry}}`.
 */
class m261107_071011_create_user_pantry_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_pantry}}', [
            'user_id' => $this->integer()->notNull(),
            'ingredient_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 2),
            'unit' => $this->string(50),
        ]);

        // Составной первичный ключ
        $this->addPrimaryKey('pk-user_pantry', '{{%user_pantry}}', ['user_id', 'ingredient_id']);

        // Внешние ключи
        $this->addForeignKey(
            'fk-user_pantry-user_id',
            '{{%user_pantry}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_pantry-ingredient_id',
            '{{%user_pantry}}',
            'ingredient_id',
            '{{%ingredients}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_pantry-ingredient_id', '{{%user_pantry}}');
        $this->dropForeignKey('fk-user_pantry-user_id', '{{%user_pantry}}');
        $this->dropTable('{{%user_pantry}}');
    }
}
