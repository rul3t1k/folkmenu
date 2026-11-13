<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shopping_list}}`.
 */
class m261107_071133_create_shopping_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shopping_list}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'ingredient_name' => $this->string(150)->notNull(),
            'quantity' => $this->decimal(10, 2),
            'unit' => $this->string(50),
            'is_purchased' => $this->tinyInteger()->default(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Внешний ключ для связи с пользователем
        $this->addForeignKey(
            'fk-shopping_list-user_id',
            '{{%shopping_list}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-shopping_list-user_id', '{{%shopping_list}}');
        $this->dropTable('{{%shopping_list}}');
    }
}
