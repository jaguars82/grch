<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%secondary_room_fee}}".
 *
 * @property int $id
 * @property int $secondary_room_id
 * @property int $created_user_id
 * @property int|null $updated_user_id
 * @property int $fee_type
 * @property float|null $fee_percent
 * @property float|null $fee_amount
 * @property bool $has_expiration_date
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property SecondaryRoom $secondaryRoom
 * @property User $createdUser
 * @property User|null $updatedUser
 */
class SecondaryRoomFee extends ActiveRecord
{
    const TYPE_AMOUNT = 1;
    const TYPE_PERCENT = 2;

    public static $feeType = [
        self::TYPE_AMOUNT => 'сумма',
        self::TYPE_PERCENT => 'процент',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%secondary_room_fee}}';
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
            [['secondary_room_id', 'created_user_id', 'fee_type'], 'required'],
            [['secondary_room_id', 'created_user_id', 'updated_user_id', 'fee_type'], 'integer'],
            [['fee_percent', 'fee_amount'], 'number'],
            [['has_expiration_date'], 'boolean'],
            [['expires_at', 'created_at', 'updated_at'], 'safe'],
            [['secondary_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => SecondaryRoom::class, 'targetAttribute' => ['secondary_room_id' => 'id']],
            [['created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_user_id' => 'id']],
            [['updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'secondary_room_id' => 'Secondary Room ID',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'fee_type' => 'Fee Type',
            'fee_percent' => 'Fee Percent',
            'fee_amount' => 'Fee Amount',
            'has_expiration_date' => 'Has Expiration Date',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[SecondaryRoom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRoom()
    {
        return $this->hasOne(SecondaryRoom::class, ['id' => 'secondary_room_id']);
    }

    /**
     * Gets query for [[CreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_user_id']);
    }

    /**
     * Gets query for [[UpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_user_id']);
    }
}
