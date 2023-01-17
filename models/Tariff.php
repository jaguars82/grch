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
 * @property json $tariff_table
 * @property string $changes
 * @property string $payterms
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 */
class Tariff extends ActiveRecord
{
 
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff';
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
            [['changes', 'payterms'], 'string'],
            [['tariff_table', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariff_table' => 'Тарифная таблица',
            'changes' => 'Изменения',
            'payterms' => 'Сроки выплаты вознаграждения ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
   
}