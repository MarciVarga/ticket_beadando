<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $comment_form common\models\CommentForm */
/* @var $ticket common\models\Ticket */
/* @var $comments common\models\Comment[] */
/* @var $images common\models\Image[] */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Show Ticket';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_ticket">
    <?php $connection = Yii::$app->getDb(); ?>

    <div class="jumbotron" style="background-color: #e1e1e1;">
        <h1>
            <?= $ticket->title ?>
                (
                    <?php if ($ticket->is_open): ?>
                        opened
                    <?php else: ?>
                        closed
                    <?php endif; ?>
                )
        </h1>
        <p><?= $ticket->description ?></p>

        <div class="container">
            <div class="row">
                <?php foreach ($images as $image): ?>
                    <div class="col-md-4">
                        <div class="thumbnail">
                            <a href="/<?= $image->path ?>" target="_blank">
                                <img src="/<?= $image->path ?>" class="img-rounded" alt="" style="width: 100%">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <?php if ($ticket->is_open): ?>
        <?php $form1 = ActiveForm::begin(['action' => ['/site/close-ticket', 'id'=>$ticket->id], 'id'=>'asd']); ?>

        <div class="form-group">
            <?= Html::submitButton('Close', ['class' => 'btn btn-warning btn-block', 'style' => 'color: #0f0f0f; font-weight: bold; font-size: medium;']) ?>
        </div>

    <?php
        ActiveForm::end();
        endif;
    ?>
    <ul class="list-group">
        <?php foreach ($comments as $comment): ?>
            <li class="list-group-item">
                <?= $comment->text ?>
                <span class="badge" style="background-color: #286090">
                    <?= $comment->user->username ?> [<?= $comment->create_time ?>]
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($comment_form, 'text')->textInput(); ?>

        <div class="form-group">
            <?= Html::submitButton('Add Comment', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>