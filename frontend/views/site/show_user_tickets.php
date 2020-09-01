<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $tickets common\models\Ticket[] */

use yii\helpers\Html;

$this->title = 'Show User Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_user_tickets">
    <h1>Your Tickets:</h1>

    <ul class="list-group">
        <?php foreach ($tickets as $ticket) { ?>

            <?php if ($ticket->is_open) { ?>
            <li class="list-group-item">
            <?php } else { ?>
                <li class="list-group-item" style="background-color: #ff7a72;">
            <?php } ?>
                <?= Html::a($ticket->title, ['site/show-ticket', 'id' => $ticket->id], ['class' => 'profile-link']) ?>
                <span class="badge">
                    <?= count($ticket->comments) ?>
                </span>
            </li>
        <?php } ?>
    </ul>

    <?= Html::a('Add Ticket', ['site/add-ticket'], ['class' => 'btn btn-primary btn-block']) ?>
</div>