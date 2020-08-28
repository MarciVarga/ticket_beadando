<?php

namespace console\controllers;

use common\models\Ticket;

class TicketController extends \yii\console\Controller
{
    public function actionCloseTicket()
    {
        $tickets = Ticket::find()
            ->ofIsOpen()
            ->all();

        foreach ($tickets as $ticket) {
            $latestComment = $ticket->getLatestComment();

            $createTime = new \DateTime($latestComment->create_time);
            $now = new \DateTime();
            $expireDate = $now->sub(new \DateInterval('P2W'));

            if ($latestComment->user->is_admin && $createTime < $expireDate) {
                $ticket->is_open = false;
                $ticket->save();
            }
        }
    }
}