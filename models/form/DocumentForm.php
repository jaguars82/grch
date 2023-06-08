<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for documents's data
 */
class DocumentForm extends Model
{
    use FillAttributes;
    use ProcessFile;

    const SCENARIO_UPDATE = 'update';

    public $name;
    public $file;
    public $size;
    public $newbuilding_complex_id;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['name', 'file'];

        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['file', 'size']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['size', 'double'],
            [['name'], 'required'], //'file',
            ['newbuilding_complex_id', 'integer'],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'file' => 'Документ',
            'size' => 'Размер'
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->file = UploadedFile::getInstance($this, 'file');
        if (is_object($this->file)) {
            $this->size = $this->file->size;
        }

        if (!$this->validate()) {
            return false;
        }

        $this->processFile('file');

        return true;
    }
}
