<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\components\exceptions\AppException;

class Archive extends \yii\db\ActiveRecord
{
    use FillAttributes;

    const SCENARIO_UPDATE = 'update';
    const FILE_PATH = '@webroot/uploads/archive/';
    const TEMP_PATH = '@runtime/archive/';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive';
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['file', 'checked', 'created_at', 'updated_at'];
        
        return [
            self::SCENARIO_DEFAULT => $commonFields,
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }

    public function beforeSave($insert) 
    {
        if(!$insert && !empty($this->file)) {
            $model = self::findOne($this->id);

            if($this->file != $model->file) {
                $filePath = \Yii::getAlias(self::FILE_PATH . $model->file);
                
                if(!is_dir($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        return parent::beforeSave($insert);
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
            [['file'], 'required'],
            [['file'], 'string', 'max' => 200],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Файл',
            'updated_at' => 'Дата загрузки'
        ];
    }
}