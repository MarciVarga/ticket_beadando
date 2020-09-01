<?php


namespace common\models;

use yii\base\Model;

class CommentForm extends Model
{
    public $text;
    public $user_id;
    public $ticket_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['text', 'trim'],
            ['text', 'required'],
            ['text', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Comment',
        ];
    }

    public function fillFrom(Comment $comment): CommentForm
    {
        $this->user_id = $comment->user_id;
        $this->ticket_id = $comment->ticket_id;
        $this->text = $comment->text;

        return $this;
    }

    public function fillTo(Comment $comment): Comment
    {
        $comment->user_id = $this->user_id;
        $comment->ticket_id = $this->ticket_id;
        $comment->text = $this->text;

        return $comment;
    }
}