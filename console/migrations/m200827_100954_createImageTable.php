<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image}}`.
 */
class m200827_100954_createImageTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('image', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'path' => $this->string()->notNull(),
        ]);

        // creates index for column `ticket_id`
        $this->createIndex(
            'idx-image-ticket_id',
            'image',
            'ticket_id'
        );

        // add foreign key for table `ticket`
        $this->addForeignKey(
            'fk-image-ticket_id',
            'image',
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
        // drops foreign key for table `image`
        $this->dropForeignKey(
            'fk-image-ticket_id',
            'image'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            'idx-image-ticket_id',
            'image'
        );

        $this->dropTable('image');
    }
}
