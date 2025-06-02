<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%changes_log}}".
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $user_id
 * @property string $action_at
 * @property string $action
 * @property array|null $changes
 * @property string|null $ip_address
 * @property string|null $user_agent
 */
class ChangesLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%changes_log}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['entity_type', 'entity_id', 'user_id', 'action_at', 'action'], 'required'],
            [['entity_id', 'user_id'], 'integer'],
            [['entity_type', 'action'], 'string', 'max' => 20],
            [['entity_type'], 'in', 'range' => ['message', 'thread', 'chat']],
            [['action'], 'in', 'range' => ['create', 'edit', 'archive', 'unarchive', 'delete', 'restore']],
            [['changes'], 'safe'],
            [['ip_address'], 'string', 'max' => 255],
            [['user_agent'], 'string'],
            [['action_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_type' => 'Entity Type',
            'entity_id' => 'Entity ID',
            'user_id' => 'User ID',
            'action_at' => 'Action At',
            'action' => 'Action',
            'changes' => 'Changes',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
        ];
    }
}
