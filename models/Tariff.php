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
 * @property json $developers_in_statistics
 * @property string $changes
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 */
class Tariff extends ActiveRecord
{
 
    use FillAttributes;

    const TYPE_PERCENT = 'percent';
    const TYPE_CURRENCY = 'currency';
    const TYPE_CUSTOM = 'custom';
  
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
            [['changes'], 'string'],
            [['tariff_table', 'developers_in_statistics', 'created_at', 'updated_at'], 'safe'],
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
            'developers_in_statistics' => 'Показывать статистику для застройщиков',
            'changes' => 'Изменения',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
   
}