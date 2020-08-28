<?php

namespace frontend\models;

use common\models\Image;
use common\models\Ticket;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class TicketForm
 * @package frontend\models
 */
class TicketForm extends Model
{
    public $title;
    public $description;
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            ['title', 'trim'],
            ['title', 'required'],
            ['title', 'string'],

            ['description', 'trim'],
            ['description', 'required'],
            ['description', 'string'],

            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 6],
        ];
    }

    /**
     * Add a Ticket
     *
     * @return bool|null
     */
    public function addTicket()
    {
        $ticket = new Ticket();
        $ticket->user_id = Yii::$app->user->identity->getId();
        $ticket->title = $this->title;
        $ticket->description = $this->description;
        $ticket->save();

        foreach ($this->imageFiles as $file) {
            $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);

            $image = new Image();
            $image->ticket_id = $ticket->id;
            $image->path = 'uploads/' . $file->baseName . '.' . $file->extension;
            $image->save();
        }

        return true;
    }

    /*public function upload()
    {
            foreach ($this->imageFiles as $file) {
                $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
            }

        return true;
    }*/
}