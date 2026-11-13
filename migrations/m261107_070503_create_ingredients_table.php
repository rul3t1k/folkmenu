<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ingredients}}`.
 */
class m261107_070503_create_ingredients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ingredients}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ingredients}}');
    }
}
