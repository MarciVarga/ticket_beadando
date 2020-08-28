<?php

/* @var $this yii\web\View */
/* @var $user backend\controllers\SiteController */

use yii\helpers\Html;

$this->title = 'Delete User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delete_user">

    <h1>You have deleted <?= $user['username'] ?>'s account.</h1>
    <?= Html::a('Go Back', ['/site/list-users'], ['class'=>'btn btn-primary']) ?>

</div>