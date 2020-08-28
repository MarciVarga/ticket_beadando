<?php


namespace common\models;

use yii\base\Model;

class CommentForm extends Model
{
    public $text;

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
}