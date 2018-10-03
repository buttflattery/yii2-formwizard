<?php

use yii\db\Migration;

/**
 * Class m180918_094732_user_table
 */
class m180918_094732_user_table extends Migration
{
    private $_table = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->_table, [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'password' => $this->string(32)->notNUll(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()')->notNull(),
            'updated_at' => $this->dateTime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_table);
    }

}
