<?php

use yii\db\Migration;

/**
 * Class m180918_095255_profile_table
 */
class m180918_095255_profile_table extends Migration
{
    private $_table = '{{%profile}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable($this->_table, [
            'id' => $this->primaryKey()->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'email' => $this->string(100)->notNull(),
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

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m180918_095255_profile_table cannot be reverted.\n";

return false;
}
 */
}
