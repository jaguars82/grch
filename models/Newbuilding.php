<?php

namespace app\models;

use app\components\traits\FillAttributes;
use app\components\traits\FlatPrices;
use app\models\query\NewbuildingQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use \app\models\BuildingType;
use \app\models\StreetType;
use \app\models\District;
use \app\models\Region;
use \app\models\City;


/**
 * This is the model class for table "newbuilding".
 *
 * @property int $id
 * @property int $newbuilding_complex_id
 * @property int $azimuth
 * @property string|null $name
 * @property string|null $address
 * @property float|null $longitude
 * @property float|null $latitude
 * @property string|null $detail
 * @property int $total_floor
 * @property string|null $material
 * @property int $status
 * @property string $deadline
 * @property int $active
 *
 * @property News[] $actions
 * @property News[] $activeActions
 * @property Developer $developer
 * @property Flat[] $flats
 * @property FloorLayout[] $floorLayouts
 * @property NewbuildingComplex $newbuildingComplex
 * @property Entrance $Entrance
 * @property News[] $news
 */
class Newbuilding extends ActiveRecord
{
    use FillAttributes;
    use FlatPrices;
    
    const STATUS_PROCESS = 0;
    const STATUS_FINISH = 1;
    
    public static $status = [
        self::STATUS_PROCESS => 'Не сдан',
        self::STATUS_FINISH => 'Сдан',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newbuilding';
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
            [['newbuilding_complex_id', 'total_floor'], 'required'],
            [['newbuilding_complex_id', 'total_floor', 'building_type_id', 'street_type_id', 'district_id', 'city_id', 'region_id'], 'integer'],
            [['building_number'], 'string', 'max' => 20],
            [['longitude', 'latitude'], 'double'],
            [['detail'], 'string'],
            [['deadline', 'status'], 'safe'],
            [['azimuth'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'material', 'street_name'], 'string', 'max' => 200],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['street_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StreetType::className(), 'targetAttribute' => ['street_type_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_complex_id' => 'Жилой комплекс',
            'name' => 'Название',
            'azimuth' => 'Азимут',
            'address' => 'Адрес',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
            'detail' => 'Информация',
            'total_floor' => 'Количество этажей',
            'deadline' => 'Срок сдачи',
            'material' => 'Материал',
            'status' => 'Статус',
            'active' => 'Активна',
        ];
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return NewbuildingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewbuildingQuery(get_called_class());
    }

    /**
     * Gets query for [[Entrance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrances()
    {
        return $this->hasMany(Entrance::className(), ['newbuilding_id' => 'id'])
                ->inverseOf('newbuilding');
    }    
    /**
     * Get maximum flat rooms count on given section 
     * 
     * @param type $section given section number
     * @return int
     */
    public function maxRoomsOnFloor($section)
    {        
        $result = (new Query)
            ->select('max(flats_count) as max_flats_on_floor')
            ->from([
                'floor_data' => (new Query)
                    ->select('count(*) as flats_count')
                    ->from([
                        'flats' => $this->getFlats()->where(['section' => $section])
                    ])
                    ->groupBy('floor')
            ])
            ->scalar();
         
        return $result;
    }
    
    public function maxRoomsOnFloors()
    {        
        $result = (new Query)
            ->select('section, max(flats_count) as max_flats_on_floor')
            ->from([
                'floor_data' => (new Query)
                    ->select('section, floor, count(*) as flats_count')
                    ->from(['flats' => $this->getFlats()])
                    ->groupBy('section, floor')
            ])
            ->groupBy('section')
            ->all();
         
        return $result;
    }
    
    public static function maxRoomsOnFloorsForNewbuildings(array $ids)
    {        
        $result = (new Query)
            ->select('newbuilding_id, section, max(flats_count) as max_flats_on_floor')
            ->from([
                'floor_data' => (new Query)
                    ->select('newbuilding_id, section, floor, count(*) as flats_count')
                    ->from(['flats' => Flat::find()->where(['IN', 'newbuilding_id', $ids])])
                    ->groupBy('newbuilding_id, section, floor')
            ])
            ->groupBy('newbuilding_id, section')
            ->all();
         
        return $result;
    }
    
    /**
     * Get all materials in array form
     * 
     * @return array
     */
    public static function getAllMaterialsAsList()
    {
        $result = self::find()
            ->select('material')
            ->distinct()
            ->where('material IS NOT NULL')
            ->asArray()
            ->all();
        
        $materials = [];
        
        foreach ($result as $newbuilding) {
            $materials[$newbuilding['material']] = \Yii::$app->formatter->asCapitalize($newbuilding['material']);
        }
        
        return $materials;
    }
    
    /**
     * Get floor positions for given floor and section
     * 
     * @param type $section section
     * @param type $floor floor
     * @param type $exceptFlatId flat ID which excepted from result
     * @return array
     */
    public function getFloorPositionsForSectionAndFloor($section, $floor, $exceptFlatId = NULL)
    {
        $query = $this->getFlats()
                ->select('floor_position')
                ->andWhere(['section' => $section])
                ->andWhere(['floor' => $floor])
                ->andWhere('floor_position IS NOT NULL');
        
        if (!is_null($exceptFlatId)) {
            $query->andWhere(['!=', 'id', $exceptFlatId]);
        }
        
        return ArrayHelper::getColumn($query->asArray()->all(), 'floor_position');
    }
    
    /**
     * Get newbuilding sections count
     * 
     * @return int
     */
    public function getSectionsCount()
    {        
        return $this->getFlats()->select('section')->distinct()->count();
    }
    
    /**
     * Get newbuilding sections data
     * 
     * @return array
     */
    public function getSectionsData()
    {
        $sectionsData = $this->getFlats()->select('section')->distinct()->asArray()->all();;
        
        return ArrayHelper::getColumn($sectionsData, 'section');
    }
    
    public static function getSectionsDataForNewbuildings(array $ids)
    {
        $sectionsData = Flat::find()
                ->select('newbuilding_id, section')
                ->distinct()
                ->where(['IN', 'newbuilding_id', $ids])
                ->asArray()
                ->all();
        
        return $sectionsData;
    }
    
    /**
     * Check that newbuilding has active actions
     * 
     * @return boolean
     */
    public function hasActions()
    {
        return count($this->activeActions) !== 0;
    }
    
    /**
     * Gets actions
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->getNews()->onlyActions();
    }
    
    /**
     * Gets active actions
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getActiveActions()
    {
        return $this->getNews()->onlyActions(true);
    }
    
    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id'])
                ->via('newbuildingComplex');
    }
    
    /**
     * Gets query for [[Flats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
                ->inverseOf('newbuilding');
    }
    
   
    /**
     * Gets query for [[Flats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
                ->onlyActive();
    }
    
    /**
     * Gets query for [[Flats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
                ->onlyReserved();
    }

    /**
     * Get free flats count
     * 
     * @return int
     */
    public function getFreeFlats()
    {
        $soldFlatsCount = count($this->getFlats()->where(['status' => Flat::STATUS_SALE])->all());
        
        return $soldFlatsCount/count($this->flats);
    }
    
    /**
     * Gets query for [[Flats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderedFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])->orderBy(['floor' => SORT_DESC, 'number' => SORT_DESC]);
    }
    
    /**
     * Gets query for [[FloorLayout]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFloorLayouts()
    {
        return $this->hasMany(FloorLayout::className(), ['newbuilding_id' => 'id'])
                ->inverseOf('newbuilding');
    }

    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id']);
    }   
    
    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])
                ->viaTable('news_newbuilding_complex', ['newbuilding_complex_id' => 'newbuilding_complex_id']);
    }

    /**
     * Gets query for [[Advantage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdvantages()
    {
        return $this->hasMany(Advantage::className(), ['id' => 'advantage_id'])
                ->viaTable('newbuilding_advantage', ['newbuilding_id' => 'id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    public function getBuildingType()
    {
        return $this->hasOne(BuildingType::className(), ['id' => 'building_type_id']);
    }

    public function getStreetType()
    {
        return $this->hasOne(StreetType::className(), ['id' => 'street_type_id']);
    }

    public function getAddress()
    {
        $address = '';
        
        if(!empty($this->region)) {
            $address .= $this->region->name;
        }

        if(!empty($this->city)) {
            $address .= " {$this->city->name}";
        }

        if(!empty($this->streetType) && !empty($this->street_name)) {
            $address .= " {$this->streetType->short_name} {$this->street_name}";
        }
        
        if(!empty($this->buildingType) && !empty($this->building_number)) {
            $address .= " {$this->buildingType->short_name} {$this->building_number}";
        }

        return $address;
    }

    public static function getAllDeadlineYears()
    {
        $result = self::find()
            ->select(['YEAR(deadline) as deadline_year', 'deadline'])
            ->where('deadline IS NOT NULL')
            ->orderBy(['deadline' => SORT_ASC])
            ->distinct()
            ->asArray()
            ->all();

        $deadlineYears = [];

        foreach($result as $item) {
            $deadlineYears[$item['deadline_year']] = $item['deadline_year'];
        }

        return $deadlineYears;
    }

    /**
     * Get max newbuilding discount
     * 
     * @return boolean
     */
    public function getMaxDiscount()
    {
        $result = $this->getFlats()->select(
            ['max(discount) as maxDiscount']
        )->where(['!=', 'discount', 0])->one();
        
        return !is_null($result->maxDiscount) ? $result->maxDiscount : NULL;
    }
}
