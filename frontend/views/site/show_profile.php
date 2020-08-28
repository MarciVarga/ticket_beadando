<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $data frontend\controllers\SiteController */

use yii\helpers\Html;

$this->title = 'Show Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_profile">

    <div class="card text-center">
        <div class="card-header" style="font-weight: bold; font-size: large;">
            Your Profile
        </div>
        <div class="card-body">

            <?php foreach ($data as $d): ?>
                <p class="card-text"> <?= $d ?> </p>
            <?php endforeach; ?>

            <a href="../site/update-profile/" class="btn btn-primary">Update Profile</a>
        </div>
    </div>

</div>