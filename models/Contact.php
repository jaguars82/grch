<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;
use app\models\query\ContactQuery;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property int $type
 * @property string $person
 * @property string $phone
 * @property string|null $photo
 * @property string|null $worktime
 *
 * @property Agency[] $agencies
 * @property Developer[] $developers
 * @property NewbuildingComplex[] $newbuildingComplexes
 */
class Contact extends \yii\db\ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'person', 'phone'], 'required'],
            [['type', 'person', 'phone', 'photo', 'worktime'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип',
            'person' => 'Сотрудник',
            'phone' => 'Телефон',
            'photo' => 'Фотография',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if (!empty($this->photo)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->photo"));
        }

        return true;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContactQuery(get_called_class());
    }

    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id'])
                ->viaTable('agency_contact', ['contact_id' => 'id']);
    }
    
    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id'])
                ->viaTable('developer_contact', ['contact_id' => 'id']);
    }
    
    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id'])
                ->viaTable('newbuilding_complex_contact', ['contact_id' => 'id']);
    }
}
