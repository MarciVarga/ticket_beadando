<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $tickets common\models\Ticket[] */

use yii\helpers\Html;

$this->title = 'Show Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="show_profile">
    <div class="panel panel-default">
        <div class="panel-heading" style="font-weight: bold"><?= $user->username ?></div>
        <div class="panel-body"><?= $user->email ?></div>
        <div class="panel-body"><strong>Admin Status:</strong> <?= $user->is_admin ?></div>

        <ul class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                Tickets
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php foreach ($tickets as $ticket) { ?>
                    <li>
                        <?= Html::a($ticket->title, ['site/show-ticket', 'id' => $ticket->id], ['class' => 'profile-link']) ?>
                    </li>
                <?php } ?>
            </ul>
        </ul>

        <div class="panel-body">
            <?= Html::a('Update', ['site/update-user', 'id' => $user->id], ['class' => 'btn btn-warning']) ?>
        </div>
    </div>
</div>