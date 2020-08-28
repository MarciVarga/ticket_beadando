<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $user backend\controllers\SiteController */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Update User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="update_user">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'is_admin')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Update Profile', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>