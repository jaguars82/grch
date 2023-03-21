<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form Form for furnish's data
 */
class FurnishForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $newbuilding_complex_id;
    public $name;
    public $detail;
    public $images = [];
    public $savedImages = [];
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['name', 'detail', 'images'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['newbuilding_complex_id']),
            self::SCENARIO_UPDATE => array_merge($commonFields, ['savedImages']),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_complex_id', 'name'], 'required'],
            [['newbuilding_complex_id'], 'integer'],
            [['detail'], 'string'],
            [['name'], 'string', 'max' => 200],
            [['images', 'savedImages'], 'safe'],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
            [['detail'], 'default', 'value' => NULL],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'detail' => 'Отделка',
            'images' => 'Изображение отделки',
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->images = UploadedFile::getInstances($this, 'images');
        
        if (!$this->validate()) {
            return false;
        }
          
        $this->processFiles('images'); 

        return true;
    }
}
