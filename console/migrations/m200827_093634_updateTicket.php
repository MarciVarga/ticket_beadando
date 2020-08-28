<?php

use yii\db\Migration;

/**
 * Class m200827_093634_updateTicket
 */
class m200827_093634_updateTicket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('ticket', [
            'title' => $this->string()->notNull()->append('character set utf8 collate utf8_hungarian_ci'),
            'description' => $this->text()->notNull()->append('character set utf8 collate utf8_hungarian_ci'),
        ]);

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
        echo "m200827_093634_updateTicket cannot be reverted.\n";

        return false;
    }
    */
}
