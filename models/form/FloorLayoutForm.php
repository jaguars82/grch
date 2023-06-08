<?php

namespace app\models\form;

use app\models\Newbuilding;
use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for floor layout's data
 */
class FloorLayoutForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $floorLayoutId = NULL;
    
    public $newbuilding_id;
    public $floor;
    public $section;
    public $map;
    public $image;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['floor', 'section', 'map'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['newbuilding_id', 'image']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {        
        return [
            [['newbuilding_id', 'floor', 'section', 'image'], 'required'],
            [['newbuilding_id'], 'integer'],
            [['map'], 'string'],
            [['floor', 'section'], 'string', 'max' => 200],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
            [['floor', 'section'], \app\components\validators\FloorAndSectionValidator::className(), 'skipOnError' => false],
            [['map'], 'default', 'value' => NULL],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'floor' => 'Этаж',
            'section' => 'Подъезд',
            'image' => 'Изображение',
            'map' => 'Позиции квартир',
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->image = UploadedFile::getInstance($this, 'image');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('image');

        return true;
    }
}
