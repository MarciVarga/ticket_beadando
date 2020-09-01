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

    public function fillFrom(Ticket $ticket): TicketForm
    {
        $this->title = $ticket->title;
        $this->description = $ticket->description;

        return $this;
    }

    public function fillTo(Ticket $ticket): Ticket
    {
        $ticket->title = $this->title;
        $ticket->description = $this->description;

        return $ticket;
    }

    public function fillImages($ticket_id)
    {
        /** @var Image[] $imageFiles */
        $imageFiles = [];

        foreach ($this->imageFiles as $imageFile) {
            $imageFile->saveAs('uploads/' . $imageFile->baseName . '.' . $imageFile->extension);

            $image = new Image();
            $image->ticket_id = $ticket_id;
            $image->path = 'uploads/' . $imageFile->baseName . '.' . $imageFile->extension;

            array_push($imageFiles, $image);
        }

        return $imageFiles;
    }
}