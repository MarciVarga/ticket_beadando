<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `user`
 */
class m200818_112032_create_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ticket', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'admin_id' => $this->integer(),
            'title' => $this->string()->notNull(),
            'is_open' => $this->boolean()->notNull()->defaultValue(true),
            'create_time' => "TIMESTAMP(0) NOT NULL DEFAULT NOW()",
            'description' => $this->text()->notNull(),
        ], ' CHARACTER SET utf8 COLLATE utf8_general_ci');

        // creates index for column `user_id`
        $this->createIndex(
            'idx-ticket-user_id',
            'ticket',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ticket-user_id',
            'ticket',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `admin_id`
        $this->createIndex(
            'idx-ticket-admin_id',
            'ticket',
            'admin_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-ticket-admin_id',
            'ticket',
            'admin_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-ticket-user_id',
            'ticket'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-ticket-user_id',
            'ticket'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-ticket-admin_id',
            'ticket'
        );

        // drops index for column `admin_id`
        $this->dropIndex(
            'idx-ticket-admin_id',
            'ticket'
        );

        $this->dropTable('ticket');
    }
}
