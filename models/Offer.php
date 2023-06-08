<?php

namespace app\models;

use app\components\traits\FillAttributes;
use app\models\query\OfferQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offer".
 *
 * @property int $id
 * @property int $flat_id
 * @property int $user_id
 * @property float|null $new_price
 * @property string|null $url
 * @property string|null $settings
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Flat $flat
 * @property User $user
 */
class Offer extends ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at', 'visited_at'],
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
            [['flat_id', 'user_id'], 'required'],
            [['flat_id', 'user_id'], 'integer'],
            [['new_price_cash', 'new_price_credit'], 'number'],
            [['settings'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['url'], 'string', 'max' => 200],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flat_id' => 'Квартира',
            'new_price' => 'Новая цена',
            'url' => 'Короткая ссылка',
            'settings' => 'Настройки',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return OfferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OfferQuery(get_called_class());
    }

    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
