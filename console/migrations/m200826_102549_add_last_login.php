<?php

use yii\db\Migration;

/**
 * Class m200826_102549_add_last_login
 */
class m200826_102549_add_last_login extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", 'last_login', "TIMESTAMP(0) NOT NULL DEFAULT NOW()");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200826_102549_add_last_login cannot be reverted.\n";

        return false;
    }
    */
}
