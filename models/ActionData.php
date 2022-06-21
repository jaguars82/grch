<?php

namespace app\models;

use app\components\traits\FillAttributes;

/**
 * This is the model class for table "action_data".
 *
 * @property int $id
 * @property int $news_id
 * @property string|null $resume
 * @property string|null $expired_at
 *
 * @property News $news
 */
class ActionData extends \yii\db\ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['news_id'], 'required'],
            [['news_id', 'discount', 'discount_type'], 'integer'],
            [['discount_amount', 'discount_price'], 'double'],
            [['expired_at'], 'safe'],
            [['resume'], 'string', 'max' => 200],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
            ['flat_filter', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'news_id' => 'News ID',
            'resume' => 'Суть акции',
            'expired_at' => 'Дата окончания',
            'flat_filter' => 'Фильтр квартир',
            'discount' => 'Размер скидки',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }
}
