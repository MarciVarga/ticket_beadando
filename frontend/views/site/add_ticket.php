<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $ticketForm frontend\models\TicketForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Add Ticket';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add_ticket">

    <?php if (Yii::$app->user->isGuest) { ?>

        <p class="text-danger" style="font-weight: bold; font-size: large;" >You have to log in!</p>

    <?php
        } else {
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    ?>

        <?= $form->field($ticketForm, 'title') ?>
        <?= $form->field($ticketForm, 'description')->textarea(['rows' => 6]) ?>
        <?= $form->field($ticketForm, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Add Ticket', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php
        ActiveForm::end();
        }
    ?>

</div><!-- add_ticket -->
