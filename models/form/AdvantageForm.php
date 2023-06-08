<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form Form for advantage's data
 */
class AdvantageForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    public $name;
    public $icon;
    public $is_icon_reset;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_icon_reset'], 'boolean'],
            [['icon'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['name'], 'string', 'max' => 200],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'icon' => 'Иконка',
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->icon = UploadedFile::getInstance($this, 'icon');

        if (is_null($this->icon)) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('icon'); 

        return true;
    }
}