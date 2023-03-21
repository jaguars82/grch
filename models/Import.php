<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "import".
 *
 * @property int $id
 * @property int|null $type
 * @property string|null $endpoint
 * @property string|null $schedule
 * @property string|null $imported_at
 *
 * @property Developer $developer
 * @property NewbuildingComplex $newbuildingComplex
 */
class Import extends \yii\db\ActiveRecord
{    
    use FillAttributes;
    
    const IMPORT_CLASS_PATH = '';
    
    const IMPORT_TYPE_MANUAL = 1;
    const IMPORT_TYPE_AUTO = 2;
    
    public static $import_types = [
        self::IMPORT_TYPE_MANUAL => 'Ручной',
        self::IMPORT_TYPE_AUTO => 'Автоматический',
    ];
    
    public static $import_auto = [
        self::IMPORT_TYPE_AUTO,
    ];
    
    /**
     * Check that algorithm class is exists
     * 
     * @param string $algorithm
     * @return boolean
     */
    public static function isAlgorithmExists($algorithm)
    {
        $algorithm = str_replace('\\', '/', $algorithm);
        $algorithmPath = \Yii::getAlias('@app') . "/components/import/{$algorithm}.php";

        if (!file_exists($algorithmPath)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check that algorithm class is valid
     * 
     * @param string $algorithm
     * @return boolean
     */
    public static function isAlgorithmValid($algorithm)
    {        
        $algorithmClassname = "\app\components\import\\{$algorithm}";
        $algorithmObject = new $algorithmClassname();
        
        if (!$algorithmObject instanceof \app\components\import\ImportServiceInterface) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check that algorithm class is support given import type
     * 
     * @param string $algorithm
     * @param string $check
     * @return boolean
     */
    public static function isSupport($algorithm, $type)
    {
        $algorithmClassname = "\app\components\import\\{$algorithm}";
        $algorithmObject = new $algorithmClassname();
        
        return $algorithmObject->$type();
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'import';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['algorithm', 'endpoint', 'schedule', 'imported_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип импорта',
            'endpoint' => 'Адрес расположения документа с данными импорта',
            'schedule' => 'Расписание импорта',
            'imported_at' => 'Дата последнего импорта',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => null,
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    /*
     * Check that import is auto
     * 
     * @return boolean
     */
    public function isAuto()
    {
        return in_array($this->type, self::$import_auto);
    }
    
    /**
     * Get object of class which realize import algorithm
     * 
     * @return mixin
     */
    public function getAlgorithmAsObject()
    {
        $classname = "\app\components\import\\{$this->algorithm}";
        
        return new $classname();
    }
    
    /**
     * Get path to class which realize import algorithm
     * 
     * @return mixin
     */
    public function getAlgorithmPath()
    {
        $algorithm = str_replace('\\', '/', $this->algorithm);
        return \Yii::getAlias('@app') . "/components/import/{$algorithm}.php";
    }
    
    /**
     * Get next importing date
     * 
     * @return string
     */
    public function getNextImportedAt()
    {
        $date = new \DateTime($this->imported_at);
        $date->add(new \DateInterval("PT{$this->schedule}S"));
        
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['import_id' => 'id']);
    }
    
    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['import_id' => 'id']);
    }
}
