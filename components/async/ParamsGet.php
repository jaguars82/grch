<?php

namespace app\components\async;

use app\models\SupportTicket;
use app\models\Notification;

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

        // check notifications
        $notifications_amount = $this->getNotificationsAmount();
        if($notifications_amount > 0) {
            $amount = $amount + $notifications_amount;
            $status = true;
        }

        $result = array();

        $result['status'] = $status;
        $result['amount'] = $amount;

        return $result;
    }

    /**
     * Get amount of unread user support messages (if there are any)
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

    /**
     * Get amount of unread notifications (if therer are any)
     */
    public function getNotificationsAmount()
    {
        $notification = new Notification();

        if(\Yii::$app->user->can('admin')) {
            $result = $notification->getUnreadNotificationsAmountForAdmin();
        } else {
            $result = $notification->getUnreadNotificationsAmountForUser(\Yii::$app->user->id);
        }
        return $result;
    }
}