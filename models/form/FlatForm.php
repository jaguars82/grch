<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use app\models\Flat;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for flat's data
 */
class FlatForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $newbuilding_id;
    public $entrance_id;
    public $number;
    public $layout;
    public $is_layout_reset = 0;
    public $detail;
    public $notification;
    public $area;
    public $rooms;
    public $floor;
    public $section;
    public $unit_price_cash;
    public $price_cash;
    public $unit_price_credit;
    public $price_credit;
    public $discount = 0;
    public $discount_type = 0;
    public $status = Flat::STATUS_SALE;
    public $azimuth;
    public $actions = [];
    public $savedActions = [];
    public $images = [];
    public $savedImages = [];
    public $floor_position;
    public $layout_type;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'entrance_id', 'number', 'detail', 'notification', 'area', 'rooms', 'floor', 'section', 'discountAsPercent',
            'unit_price_cash', 'price_cash', 'unit_price_credit', 'price_credit', 'status', 'azimuth', 
            'actions', 'images', 'discount', 'discount_type', 'discount_amount', 'discount_price', 'floor_position', 'is_layout_reset', 'layout_type'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['newbuilding_id', 'layout']),
            self::SCENARIO_UPDATE => array_merge($commonFields, ['savedActions', 'savedImages']),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_id', 'number', 'section', 'floor', 'area', 'rooms', 'floor', 'status'], 'required'],
            [['newbuilding_id', 'number', 'rooms', 'floor', 'azimuth', 'section', 'status', 'floor_position', 'discount_type'], 'integer'],
            [['detail', 'notification'], 'string'],
            [['area', 'discount', 'discount_amount', 'discount_price', 'unit_price_cash', 'price_cash', 'unit_price_credit', 'price_credit', 'discountAsPercent'], 'double'],
            [['actions', 'savedActions', 'images', 'savedImages'], 'safe'],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
            [['layout'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['detail', 'unit_price_cash', 'price_cash', 'unit_price_credit', 'price_credit', 'azimuth', 'notification', 'floor_position'], 'default', 'value' => NULL],
            [['layout_type'], 'string'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'number' => 'Номер',
            'layout' => 'Изображение планировки',
            'detail' => 'Информация',
            'notification' => 'Уведомление',
            'area' => 'Площадь',
            'rooms' => 'Количество комнат',
            'floor' => 'Этаж',
            'section'=> 'Подъезд',
            'unit_price_cash' => 'Цена(Нал.) за кв.м.',
            'price_cash' => 'Цена(Нал.)',
            'unit_price_credit' => 'Цена(Ипотека) за кв.м.',
            'price_credit' => 'Цена(Ипотека)',
            'discountAsPercent' => 'Скидка',
            'status' => 'Статус',
            'azimuth' => 'Азимут',
            'actions' => 'Акции применяемые к квартире',
            'images' => 'Изображение квартиры',
            'floor_position' => 'Позиция на планировке этажа',
            'layout_type' => 'Планировка',
        ];
    }
    
    /**
     * Getter for discountAsPercent attribute
     * 
     * @return float
     */
    public function getDiscountAsPercent()
    {
        return $this->discount * 100;
    }
    
    /**
     * Setter for discountAsPercent attribute
     * 
     * @param float $value
     */
    public function setDiscountAsPercent($value)
    {
        $this->discount = $value / 100;
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        if (isset($data['rooms']) && $data['rooms'] !== '') {
            $this->rooms = $data['rooms'];
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
        $this->layout = UploadedFile::getInstance($this, 'layout');
        $this->images = UploadedFile::getInstances($this, 'images');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('layout');        
        $this->processFiles('images');

        return true;
    }
}
