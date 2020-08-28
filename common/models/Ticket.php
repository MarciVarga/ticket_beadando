<?php

namespace common\models;

use common\models\query\TicketQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $admin_id
 * @property string $title
 * @property boolean $is_open
 * @property string $create_time
 * @property string $description
 *
 * @property Comment[] $comments
 * @property Image[] $images
 * @property User $admin
 * @property User $user
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'description'], 'required'],
            [['user_id', 'admin_id',], 'integer'],
            [['is_open'], 'boolean'],
            [['create_time'], 'safe'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['admin_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'admin_id' => 'Admin ID',
            'title' => 'Title',
            'is_open' => 'Is Open',
            'create_time' => 'Create Time',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[Admin]].
     *
     * @return ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(User::className(), ['id' => 'admin_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketQuery(get_called_class());
    }

    /**
     * @return Comment|null
     */
    public function getLatestComment()
    {
        /** @var Comment $latestComment */
        $latestComment = $this->getComments()->orderBy(['id'=>SORT_DESC])->limit(1)->one();

        if ($latestComment != null) {
            return $latestComment;
        } else {
            return null;
        }
    }
}
