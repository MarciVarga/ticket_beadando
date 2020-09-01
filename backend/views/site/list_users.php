<?php

/* @var $this yii\web\View */
/* @var $users common\models\User[] */

use yii\helpers\Html;

$this->title = 'List Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list_users">
    <h1>List Users:</h1>

    <table class="table table-condensed">
        <thead>
        <tr>
            <th>Username</th>
            <th>Admin Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) { ?>
        <tr>
            <td>
                <?= Html::a($user->username, ['site/show-profile', 'id' => $user->id], ['class' => 'profile-link']) ?>
            </td>
            <td>
                <?php if ($user->is_admin) { ?>
                    <span class="glyphicon glyphicon-text-background">
                <?php } ?>
            </td>
            <td>
                <?= Html::a('Delete', ['site/delete-user', 'id' => $user->id], ['class' => 'btn btn-danger']) ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>