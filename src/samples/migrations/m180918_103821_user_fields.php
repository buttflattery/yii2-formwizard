<?php

use yii\db\Migration;

/**
 * Class m180918_103821_user_fields
 */
class m180918_103821_user_fields extends Migration
{
    private $_table = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->_table, 'accessToken', $this->string(32)->notNull());
        $this->addColumn($this->_table, 'authKey', $this->string(32)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->_table, 'accessToken');
        $this->dropColumn($this->_table, 'authKey');
    }

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m180918_103821_user_fields cannot be reverted.\n";

return false;
}
 */
}
