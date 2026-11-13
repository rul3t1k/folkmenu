<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m261107_070105_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'phone' => $this->string(20),
            'bio' => $this->text(),
            'avatar' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'password' => $this->string(255)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
