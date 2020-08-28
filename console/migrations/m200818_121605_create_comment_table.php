<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `ticket`
 */
class m200818_121605_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'create_time' => "TIMESTAMP(0) NOT NULL DEFAULT NOW()",
            'text' => $this->text()->notNull(),
            'ticket_id' => $this->integer()->notNull(),
        ], ' CHARACTER SET utf8 COLLATE utf8_general_ci');

        // creates index for column `user_id`
        $this->createIndex(
            'idx-comment-user_id',
            'comment',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-comment-user_id',
            'comment',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `ticket_id`
        $this->createIndex(
            'idx-comment-ticket_id',
            'comment',
            'ticket_id'
        );

        // add foreign key for table `ticket`
        $this->addForeignKey(
            'fk-comment-ticket_id',
            'comment',
            'ticket_id',
            'ticket',
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
            'fk-comment-user_id',
            'comment'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-comment-user_id',
            'comment'
        );

        // drops foreign key for table `ticket`
        $this->dropForeignKey(
            'fk-comment-ticket_id',
            'comment'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            'idx-comment-ticket_id',
            'comment'
        );

        $this->dropTable('comment');
    }
}
