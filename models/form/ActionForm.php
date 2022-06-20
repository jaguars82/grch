<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use yii\base\Model;

/**
 * Form for action's data
 */
class ActionForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    
    public $is_enabled = false;
    public $resume;
    public $expired_at;
    public $discount_type;
    public $discount;
    public $discount_amount;
    public $discount_price;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['resume', 'expired_at'], 'required', 'when' => function($model) {
                    return $model->is_enabled;
                }, 'whenClient' => "function (attribute, value) {
                    return $('.action-is-enabled').prop('checked');
                }",
                'skipOnError' => false,
            ],
            [['resume'], 'string', 'max' => 200],
            [['expired_at'], 'string'],
            [['discount', 'discount_type'], 'integer'],
            [['discount_amount', 'discount_price'], 'double'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resume' => 'Суть акции',
            'expired_at' => 'Дата окончания',
            'discount' => 'Скидка в процентах',
            'discount_amount' => 'Скидка в рублях',
            'discount_price' => 'Цена со скидкой',

        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        if (!is_null($this->expired_at)) {
            $this->expired_at = \Yii::$app->formatter->asDate(strtotime($this->expired_at. ' + 1 seconds'), 'php:d.m.Y');
        }
        
        return $result;
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }
        
        if (!empty($this->expired_at)) {
            $this->expired_at = \Yii::$app->formatter->asDate($this->expired_at, 'php:Y-m-d H:i:s');
        }
        
        return true;
    }
}
