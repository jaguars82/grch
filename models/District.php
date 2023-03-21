<?php

namespace app\models;

use Yii;
use app\models\query\DistrictQuery;

/**
 * This is the model class for table "district".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $city_id
 *
 * @property City $city
 */
class District extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Район',
            'city_id' => 'Город'
        ];
    }

    /**
     * {@inheritdoc}
     * 
     * @return NewbuildingComplexQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DistrictQuery(get_called_class());
    }

    public static function getAllForLocationAsList()
    {       
        $cookie = \Yii::$app->request->cookies;
        $queryParams = \Yii::$app->request->queryParams;
        $defaultCityId = ($cookie->has('selected-city-' . \Yii::$app->user->id)) ? 
            $cookie->get('selected-city-' . \Yii::$app->user->id) : 1;
        $queryCityId = isset($queryParams['city']) && !empty($queryParams['city']) ? $queryParams['city'] : NULL;
        $selectedCity = !is_null($queryCityId) ? $queryCityId : $defaultCityId;

        $result = self::find()->forCity($selectedCity)->all();

        $districts = [];
        foreach($result as $item) {
            $districts[$item->id] = $item->name;
        }

        return $districts;
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
