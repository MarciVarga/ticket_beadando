<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Ticket]].
 *
 * @see \common\models\Ticket
 */
class TicketQuery extends \yii\db\ActiveQuery
{
    public function ofId(int $id): TicketQuery
    {
        return $this->andWhere(['id'=>$id]);
    }

    public function ofUserId(int $id): TicketQuery
    {
        return $this->andWhere(['user_id'=>$id]);
    }

    public function ofAdminId(int $id): TicketQuery
    {
        return $this->andWhere(['admin_id'=>$id]);
    }

    public function ofIsOpen(): TicketQuery
    {
        return $this->andWhere(['is_open'=>true]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Ticket[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Ticket|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
