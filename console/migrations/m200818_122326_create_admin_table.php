<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m200818_122326_create_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", 'is_admin', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
