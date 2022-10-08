<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "support_tickets".
 *
 * @property int $id
 * @property int $uewr_id
 *
 * @property User[] $admins
 */
class AuthAssignment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_assignment';
    }

    /**
     * returns array of admins
     */
    public function getAdmins()
    {
        $admins = [];

        $idies = $this->find()
            ->where(['item_name' => 'admin'])
            ->asArray()
            ->all();
       
        foreach ($idies as $admin) {
            $user = (new User())->findOne($admin['user_id']);
            array_push($admins, $user);
        }

        return $admins;
    }

}