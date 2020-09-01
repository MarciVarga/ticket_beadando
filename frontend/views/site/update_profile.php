<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $userForm frontend\models\UserForm */
/* @var $user frontend\controllers\SiteController */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Update Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="update_profile">

    <?php if (Yii::$app->user->isGuest) { ?>

        <p class="text-danger" style="font-weight: bold; font-size: large;" >You have to login!</p>

    <?php
        } else {
            $form = ActiveForm::begin();
    ?>

        <?= $form->field($userForm, 'username')->textInput(['value' => Html::encode($user->username)]) ?>
        <?= $form->field($userForm, 'email')->textInput(['value' => Html::encode($user->email)]) ?>
        <?= $form->field($userForm, 'old_password')->passwordInput() ?>
        <?= $form->field($userForm, 'password_hash')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Update Profile', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php
        ActiveForm::end();
    }
    ?>

</div>