<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "secondary_advertisement".
 *
 * @property int $id
 * @property string $external_id
 * @property int $deal_type
 * @property string $deal_status_string
 * @property int $agency_id
 * @property int $creation_type
 * @property int|null $author_id
 * @property string $author_info
 * @property boolean $is_active
 * @property string|null $expiration_date
 * @property boolean $is_expired
 * @property boolean $is_prolongated
 * @property string|null $last_prolongation_date
 * @property string|null $creation_date
 * @property string|null $last_update_date
 * @property string|null $created_at
 * @property string|null $updated_at
 * 
 * @property Agency $agency
 * @property User $author
 * 
 */

class SecondaryRoom extends ActiveRecord
{
    use FillAttributes;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_advertisement';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
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
            [['agency_id'], 'required'],
            [['external_id', 'deal_status_string', 'expiration_date', 'last_prolongation_date', 'creation_date', 'last_update_date'], 'string'],
            [['deal_type', 'agency_id', 'creation_type', 'author_id'], 'integer'],
            [['is_active', 'is_expired', 'is_prolongated'], 'boolean'],
            [['author_info', 'created_at', 'updated_at'], 'safe'],
            [['author_id', 'expiration_date', 'last_prolongation_date', 'creation_date', 'last_update_date'], 'default', 'value' => NULL],
            [['is_active'], 'default', 'value' => true],
            [['is_expired', 'is_prolongated'], 'default', 'value' => false],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_id' => 'Внешний идентификатор',
            'deal_type' => 'Тип сделки',
            'deal_status_string' => 'Статус сделки',
            'agency_id' => 'Агентство',
            'creation_type' => 'Способ создания',
            'author_id' => 'Автор',
            'author_info' => 'Информация об авторе',
            'is_active' => 'Активно',
            'expiration_date' => 'Дата истечения',
            'is_expired' => 'Истекло',
            'is_prolongated' => 'Продлено',
            'last_prolongation_date' => 'Дата последнего продления',
            'creation_date' => 'Дата создания',
            'last_update_date' => 'Дата обновления',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }


}
