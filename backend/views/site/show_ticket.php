<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $comment_form common\models\CommentForm */
/* @var $ticket common\models\Ticket */
/* @var $comments common\models\Comment[] */
/* @var $images common\models\Image[] */
/* @var $imagePath backend\controllers\SiteController */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Show Ticket';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_ticket">
    <h4>
        Ticket Owner:
        <strong>
            <?= Html::a($ticket->user->username, ['site/show-profile', 'id' => $ticket->user->id], ['class' => 'profile-link']) ?>
        </strong>
    </h4>

    <div class="jumbotron" style="background-color: #e1e1e1;">
        <h1>
            <?= $ticket->title ?>
            <?php
            if ($ticket->is_open) {
                echo "(opened)";
            } else {
                echo "(closed)";
            }
            ?>
        </h1>
        <p><?= $ticket->description ?></p>

        <div class="container">
            <div class="row">
                <?php foreach ($images as $image) { ?>
                    <div class="col-md-4">
                        <div class="thumbnail">
                            <a href="/<?= $image->path ?>" target="_blank">
                                <img src="/<?= $image->path ?>" class="img-rounded" alt="" style="width: 100%">
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

    <?php if ($ticket->admin_id == null && $ticket->is_open) { ?>
        <?php $form1 = ActiveForm::begin(['action' => ['/site/assign-ticket', 'id'=>$ticket->id], 'id'=>'asd']); ?>

        <div class="form-group">
            <?= Html::submitButton('Assign Ticket', ['class' => 'btn btn-primary btn-block', 'style' => 'font-weight: bold; font-size: medium;']) ?>
        </div>

    <?php
        ActiveForm::end();
        }
    ?>

    <?php if ($ticket->is_open && $ticket->admin_id==Yii::$app->user->identity->getId()) { ?>
        <?php $form1 = ActiveForm::begin(['action' => ['/site/close-ticket', 'id'=>$ticket->id], 'id'=>'asd']); ?>

        <div class="form-group">
            <?= Html::submitButton('Close', ['class' => 'btn btn-warning btn-block', 'style' => 'color: #0f0f0f; font-weight: bold; font-size: medium;']) ?>
        </div>

    <?php
        ActiveForm::end();
        }
    ?>

    <ul class="list-group">
        <?php foreach ($comments as $comment) { ?>
            <li class="list-group-item">
                <?= $comment->text ?>
                <span class="badge" style="background-color: #286090">
                    <?= $comment->user->username ?> [<?= $comment->create_time ?>]
                </span>
            </li>
        <?php } ?>
    </ul>

    <?php if ($ticket->is_open && $ticket->admin_id==Yii::$app->user->identity->getId()) { ?>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($comment_form, 'text')->textInput(); ?>

        <div class="form-group">
            <?= Html::submitButton('Add Comment', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php
        ActiveForm::end();
        }
    ?>

    <h4 style="font-weight: bolder; color: #159d1b;">
        Admin:
        <?php if (isset($ticket->admin)) { ?>
            <?= $ticket->admin->username ?>
        <?php } ?>
    </h4>
</div>