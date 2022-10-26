<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "commercial".
 *
 * @property int $id
 * @property int $initiator_id
 * @property string $number
 * @property string $name
 * @property boolean $active
 * @property boolean $is_formed
 * @property string $settings
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Initiator User
 */
class Commercial extends ActiveRecord
{

    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commercial';
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
            [['initiator_id'], 'required'],
            [['initiator_id'], 'integer'],
            [['active', 'is_formed'], 'boolean'],
            [['settings', 'name'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['initiator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['initiator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'initiator_id' => 'Initiator ID',
            'number' => 'Номер',
            'active' => 'Активна',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * if Commercial Proposal for Single object or Multiple objects
     */
    public function objectsMode() {

    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInitiator()
    {
        return $this->hasOne(User::className(), ['id' => 'initiator_id']);
    }

    /** Gets query for [[Flat]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getFlats()
   {
       return $this->hasMany(Flat::className(), ['id' => 'flat_id'])
               ->viaTable('commercial_flat', ['commercial_id' => 'id']);
   }
   
}