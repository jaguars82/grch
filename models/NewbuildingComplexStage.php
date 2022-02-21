<?php

namespace app\models;

/**
 * This is the model class for table "newbuilding_complex_stage".
 *
 * @property int $id
 * @property int $newbuilding_complex_id
 * @property string $name
 * @property string|null $description
 *
 * @property Furnish $furnish
 */
class NewbuildingComplexStage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newbuilding_complex_stage'; 
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 200],
            [['description'], 'string'],
            [['newbuilding_complex_id', 'name'], 'required'],
            [['newbuilding_complex_id'], 'integer'],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_complex_id' => 'NewbuildingComplex ID',
            'name' => 'Название',
            'description' => 'Описание'
        ];
    }

    /**
     * Gets query for [[Furnish]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id']);
    }
}
