<?php

use yii\db\Migration;

/**
 * Class m180918_160300_add_profile_fields
 */
class m180918_160300_add_profile_fields extends Migration
{
    private $_table = '{{%profile}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->_table, 'user_type', 'ENUM("admin","user") default "user" NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->_table, 'user_type');
    }
}
