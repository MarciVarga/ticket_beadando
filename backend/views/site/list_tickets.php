<?php

/* @var $this yii\web\View */
/* @var $tickets common\models\Ticket[] */

use yii\helpers\Html;

$this->title = 'List Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list_tickets">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>User</th>
                <th>Title</th>
                <th>Last Comment Date</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <?php if (!$ticket->is_open): ?>
                <tr style="background-color: #ff7a72">
            <?php else: ?>
                <tr>
            <?php endif; ?>
                    <td>
                        <?= Html::a($ticket->user->username, ['site/show-profile', 'id' => $ticket->user->id], ['class' => 'profile-link']) ?>
                    </td>
                    <td>
                        <?= Html::a($ticket->title, ['site/show-ticket', 'id' => $ticket->id], ['class' => 'profile-link']) ?>
                    </td>
                    <td>
                        <?php if ($ticket->getLatestComment() != null): ?>
                            <?= $ticket->getLatestComment()->create_time ?>
                        <?php
                            else:
                            echo "There are no comments";
                            endif;
                        ?>
                    </td>
                    <td>
                        <?php if (isset($ticket->admin)) {
                                echo $ticket->admin->username;
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>