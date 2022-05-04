<?php

namespace app\components\async;

use app\models\SupportTicket;

class ParamsGet
{
    public function getAllEventsParams() {
        $status = false;
        $amount = 0;

        // check support messages
        $support_messages_amount = $this->getSupportMessagesAmount();
        if($support_messages_amount > 0) { 
            $amount = $amount + $support_messages_amount;
            $status = true;
        }

        $result = array();

        $result['status'] = $status;
        $result['amount'] = $amount;

        return $result;
    }

    /**
     * Get if there are uread support messages
     */
    public function getSupportMessagesAmount() {
        $ticket_model = new SupportTicket();

        if(\Yii::$app->user->can('admin')) {
            $result = $ticket_model->getTicketsWithUnreadFromAuthor();
        } else {
            $result = $ticket_model->getTicketsWithUnreadFromAdmin(\Yii::$app->user->id);
        }
        return $result;
    }
}