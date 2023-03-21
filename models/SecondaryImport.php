<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "secondary_import".
 *
 * @property int $id
 * @property string|null $endpoint
 * @property string|null $imported_at
 *
 * @property Agency $agency
 */
class SecondaryImport extends \yii\db\ActiveRecord
{    
    use FillAttributes;
    
    /**
     * Check that algorithm class is exists
     * 
     * @param string $algorithm
     * @return boolean
     */
    public static function isAlgorithmExists($algorithm)
    {
        $algorithmPath = \Yii::getAlias('@app') . "/components/import/secondary/{$algorithm}.php";

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
        $algorithmClassname = "\app\components\import\secondary\\{$algorithm}";
        $algorithmObject = new $algorithmClassname();
        
        if (!$algorithmObject instanceof \app\components\import\secondary\SecondaryImportServiceInterface) {
            return false;
        }
        
        return true;
    }
      
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_import';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['algorithm', 'endpoint', 'imported_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'endpoint' => 'Адрес расположения документа с данными импорта',
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
    
    /**
     * Get object of class which realize import algorithm
     * 
     * @return mixin
     */
    public function getAlgorithmAsObject()
    {
        $classname = "\app\components\import\secondary\\{$this->algorithm}";
        
        return new $classname();
    }
    
    /**
     * Get path to class which realize import algorithm
     * 
     * @return mixin
     */
    public function getAlgorithmPath()
    {
        return \Yii::getAlias('@app') . "/components/import/secondary/{$this->algorithm}.php";
    }
    
    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['import_id' => 'id']);
    }
}