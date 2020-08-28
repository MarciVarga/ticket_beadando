<?php

use yii\db\Migration;

/**
 * Class m200827_150028_insertAdmin
 */
class m200827_150028_insertAdmin extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->insert('user', [
            'username' => 'admin',
            'is_admin' => true,
            'email' => 'admin@ticket.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'status' => 10,
            'auth_key' => Yii::$app->security->generateRandomString(),
            'created_at' => idate("U"),
            'updated_at' => idate("U"),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200827_150028_insertAdmin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_150028_insertAdmin cannot be reverted.\n";

        return false;
    }
    */
}
