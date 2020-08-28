<?php

namespace common\models;

use common\models\query\ImageQuery;
use common\models\query\TicketQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $path
 *
 * @property Ticket $ticket
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'path'], 'required'],
            [['ticket_id'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket ID',
            'path' => 'Path',
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return ActiveQuery|TicketQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    /**
     * {@inheritdoc}
     * @return ImageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ImageQuery(get_called_class());
    }
}
