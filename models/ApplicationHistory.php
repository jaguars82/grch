<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "application_history".
 *
 * @property int $id
 * @property int $application_id
 * @property string $action
 * @property int $user_id
 * @property string $made_at
 * 
 * @property Application $application
 * @property User $user
 */
class ApplicationHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application_history';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['made_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'action', 'user_id'], 'required'],
            [['application_id', 'action', 'user_id'], 'integer'],
            [['made_at'], 'safe'],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Application::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

}