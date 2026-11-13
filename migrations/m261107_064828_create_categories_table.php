<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categories}}`.
 */
class m261107_064828_create_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Создаем таблицу категорий
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
        ]);

        // Добавляем поле category_id в таблицу recipes (если его там еще нет)
        // $this->addColumn('{{%recipes}}', 'category_id', $this->integer()->notNull());

        // Создаем внешний ключ для связи рецептов с категориями
        $this->addForeignKey(
            'fk-recipes-category_id',
            '{{%recipes}}',
            'category_id',
            '{{%categories}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Откат миграции: удаляем связь и саму таблицу категорий
        $this->dropForeignKey('fk-recipes-category_id', '{{%recipes}}');
        $this->dropTable('{{%categories}}');
    }
}
