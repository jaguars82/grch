<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $route
 * @property string|null $request_params
 * @property bool $is_login (1 = login, 0 = other actions)
 * @property string $visited_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $device_type ('desktop'/'mobile')
 * @property string|null $referrer
 *
 * @property User|null $user
 */
class VisitLog extends ActiveRecord
{
    const DEVICE_DESKTOP = 'desktop';
    const DEVICE_MOBILE = 'mobile';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visit_log}}';
    }

    public function rules()
    {
        return [
            [['route', 'device_type'], 'required'],
            [['user_id'], 'integer'],
            [['is_login'], 'boolean'],
            [['visited_at'], 'safe'],
            [['user_agent', 'request_params', 'referrer'], 'string'],
            [['route'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
            [['device_type'], 'in', 'range' => [self::DEVICE_DESKTOP, self::DEVICE_MOBILE]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'route' => 'Маршрут',
            'request_params' => 'Параметры запроса',
            'is_login' => 'Вход в систему',
            'visited_at' => 'Дата визита',
            'ip_address' => 'IP-адрес',
            'user_agent' => 'User Agent',
            'device_type' => 'Тип устройства',
            'referrer' => 'Реферрер',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}