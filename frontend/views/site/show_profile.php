<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $user common\models\User */

$this->title = 'Show Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_profile">

    <div class="card text-center">
        <div class="card-header" style="font-weight: bold; font-size: large;">
            Your Profile
        </div>
        <div class="card-body">

            <p class="card-text">
                <?= $user->username ?>
            </p>
            <p class="card-text">
                <?= $user->email ?>
            </p>
            <p class="card-text">
                Created At:
                <?= date("Y-m-d H:i:s", $user->created_at) ?>
            </p>
            <p class="card-text">
                Last Login:
                <?= $user->last_login ?>
            </p>

            <a href="../site/update-profile/" class="btn btn-primary">Update Profile</a>
        </div>
    </div>

</div>