<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "secondary_room".
 *
 * @property int $id
 * @property int $advertisement_id
 * @property int $category_id
 * @property string $category_string
 * @property int|null $property_type_id
 * @property string $property_type_string
 * @property int|null $building_series_id
 * @property string $building_series_string
 * @property int|null $newbuilding_complex_id
 * @property string $newbuilding_complex_string
 * @property int|null $newbuilding_id
 * @property int|null $entrance_id
 * @property int|null $entrance_id
 * @property int|null $flat_id
 * @property int $number
 * @property string $number_suffix
 * @property float $price
 * @property float $unit_price
 * @property boolean $mortgage
 * @property string $layout
 * @property string $detail
 * @property string $special_conditions
 * @property float $area
 * @property float $kitchen_area
 * @property float $living_area
 * @property float $ceiling_height
 * @property int $rooms
 * @property string $balcony_info
 * @property int $balcony_amount
 * @property int $loggia_amount
 * @property string $windowview_info
 * @property boolean $windowview_street
 * @property boolean $windowview_yard
 * @property boolean $dressing_room
 * @property boolean $panoramic_windows
 * @property boolean $is_studio
 * @property boolean $is_euro
 * @property int $built_year
 * @property int $renovation_id
 * @property string $renovation_string
 * @property int|null $quality_index
 * @property string $quality_string
 * @property int $floor
 * @property int $section
 * @property string $address
 * @property int $total_floors
 * @property string $material
 * @property boolean $elevator
 * @property string $elevator_info
 * @property int $elevator_passenger_amount
 * @property int $elevator_freight_amount
 * @property boolean $rubbish_chute
 * @property boolean $gas_pipe
 * @property boolean $phone_line
 * @property boolean $internet
 * @property int|null $bathroom_index
 * @property string $bathroom_unit
 * @property boolean $concierge
 * @property boolean $closed_territory
 * @property boolean $playground
 * @property boolean $underground_parking
 * @property boolean $ground_parking
 * @property boolean $multilevel_parking
 * @property boolean $open_parking
 * @property string $parking_info
 * @property boolean $barrier
 * @property float $longitude
 * @property float $latitude
 * @property int $azimuth
 * @property int|null $region_id
 * @property int|null $region_district_id
 * @property int|null $city_id
 * @property int|null $district_id
 * @property int|null $street_type_id
 * @property string $street_name
 * @property string $building_number
 * @property string $location_info
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property SecondaryAdvertisement $secondaryAdvertisement
 * @property SecondaryCategory $secondaryCtegory
 * @property SecondaryPropertyType $secondaryPropertyType
 * @property SecondaryBuildingSeries $secondaryBuildingSeries
 * @property NewbuildingComplex $newbuildingComplex
 * @property Newbuilding $newbuilding
 * @property Entrance $entrance
 * @property Flat $flat
 * @property SecondaryRoomImage[] $images
 * @property Region $region
 * @property RegionDistrict $regionDistrict
 * @property City $city
 * @property District $district
 * @property StreetType $streetType
 *
 */
class SecondaryRoom extends ActiveRecord
{
    use FillAttributes;

    const BATHROOM_SEPARATE = 1;
    const BATHROOM_COMBINED = 2;
    const BATHROOM_TOILET = 3;
    const BATHROOM_MULTIPLE = 4;

    const QUALITY_EXCELLENT = 1;
    const QUALITY_GOOD = 2;
    const QUALITY_NORMAL = 3;
    const QUALITY_SATISFACTORY = 4;
    const QUALITY_BAD = 5;

    public static $bathroom = [
        self::BATHROOM_SEPARATE => 'раздельный',
        self::BATHROOM_COMBINED => 'совмещенный',
        self::BATHROOM_TOILET => 'туалет',
        self::BATHROOM_MULTIPLE => 'несколько',
    ];

    public static $quality = [
        self::QUALITY_EXCELLENT => 'отличное',
        self::QUALITY_GOOD => 'хорошее',
        self::QUALITY_NORMAL => 'нормальное',
        self::QUALITY_SATISFACTORY => 'удовлетворительное',
        self::QUALITY_BAD => 'плохое',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_room';
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
            [['advertisement_id'], 'required'],
            [['building_number'], 'string', 'max' => 20],
            [['built_year'], 'string', 'max' => 4],
            [['advertisement_id', 'category_id', 'property_type_id', 'building_series_id', 'newbuilding_complex_id', 'newbuilding_id', 'entrance_id', 'flat_id', 'city_id', 'region_id', 'district_id', 'region_district_id', 'street_type_id', 'rooms', 'balcony_amount', 'loggia_amount', 'renovation_id', 'floor', 'section', 'total_floors', 'elevator_passenger_amount', 'elevator_freight_amount', 'bathroom_index', 'quality_index'], 'integer'],
            [['number', 'azimuth'], 'number'],
            [['price', 'unit_price', 'area', 'kitchen_area', 'living_area', 'ceiling_height', 'longitude', 'latitude'], 'double'],
            [['category_string', 'property_type_string', 'building_series_string', 'newbuilding_complex_string', 'number_suffix', 'detail', 'special_conditions', 'balcony_info', 'windowview_info', 'renovation_string', 'quality_string', 'address', 'material', 'elevator_info', 'bathroom_unit', 'parking_info', 'street_name'], 'string'],
            [['mortgage', 'windowview_street', 'windowview_yard', 'dressing_room', 'panoramic_windows', 'is_studio', 'is_euro', 'elevator', 'rubbish_chute', 'gas_pipe', 'phone_line', 'internet', 'concierge', 'closed_territory', 'playground', 'underground_parking', 'ground_parking', 'open_parking', 'multilevel_parking', 'barrier'], 'boolean'],
            [['location_info', 'created_at', 'updated_at'], 'safe'],
            [['layout'], 'string', 'max' => 200],
            [['category_id', 'property_type_id', 'building_series_id', 'newbuilding_complex_id', 'newbuilding_id', 'entrance_id', 'flat_id', 'renovation_id', 'region_id', 'region_district_id', 'city_id', 'district_id', 'street_type_id', 'bathroom_index', 'quality_index', 'created_at', 'updated_at'], 'default', 'value' => NULL],
            [['mortgage', 'is_studio', 'is_euro'], 'default', 'value' => false],
            [['advertisement_id'], 'exist', 'skipOnError' => true, 'targetClass' => SecondaryAdvertisement::className(), 'targetAttribute' => ['advertisement_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SecondaryCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['property_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SecondaryPropertyType::className(), 'targetAttribute' => ['property_type_id' => 'id']],
            [['building_series_id'], 'exist', 'skipOnError' => true, 'targetClass' => SecondaryBuildingSeries::className(), 'targetAttribute' => ['building_series_id' => 'id']],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
            [['entrance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entrance::className(), 'targetAttribute' => ['entrance_id' => 'id']],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['region_district_id'], 'exist', 'skipOnError' => true, 'targetClass' => RegionDistrict::className(), 'targetAttribute' => ['region_district_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['street_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StreetType::className(), 'targetAttribute' => ['street_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertisement_id' => 'Объявление',
            'category_id' => 'Категория',
            'category_string' => 'Категория', 
            'property_type_id' => 'Тип собственности',
            'property_type_string' => 'Тип собственности', 
            'building_series_id' => 'Тип здания',
            'building_series_string' => 'Тип здания', 
            'newbuilding_complex_id' => 'Жилой комплекс',
            'newbuilding_complex_string' => 'Жилой комплекс', 
            'newbuilding_id' => 'Здание',
            'entrance_id' => 'Подъезд',
            'entrance_id' => 'Подъезд',
            'flat_id' => 'Квартира',
            'number' => 'Номе квартиры',
            'number_suffix' => 'Суффикс номера квартиры', 
            'price' => 'Стоимость',
            'unit_price' => 'Стоимость квадратного метра',
            'mortgage' => 'Ипотека',
            'layout' => 'Планировка',
            'detail' => 'Описание',
            'special_conditions' => 'Специальные условия',
            'area' => 'Общая площадь',
            'kitchen_area' => 'Площадь кухни',
            'living_area' => 'Жилая площадь',
            'ceiling_height' => 'Высота потолков',
            'rooms' => 'Количество комнат',
            'balcony_info' => 'Информация о балконе',
            'balcony_amount' => 'Количество балконов',
            'loggia_amount' => 'Количество лоджий',
            'windowview_info' => 'Вид из окна',
            'windowview_street' => 'Вид на улицу',
            'windowview_yard' => 'Вид во двор',
            'dressing_room' => 'Гардеробная',
            'panoramic_windows' => 'Панорамные окна',
            'is_studio' => 'Студия',
            'is_euro' => 'Европланировка',
            'built_year' => 'Год постройки',
            'renovation_id' => 'Ремонт',
            'renovation_string' => 'Ремонт',
            'quality_string' => 'Состояние',
            'floor' => 'Этаж',
            'section' => 'Номер подъезда',
            'address' => 'Адрес',
            'total_floors' => 'Этажей в здании',
            'material' => 'Материал',
            'elevator' => 'Наличие лифта',
            'elevator_info' => 'Лифт',
            'elevator_passenger_amount' => 'Количество пассажирских лифтов',
            'elevator_freight_amount' => 'Количество грузовых лифтов',
            'rubbish_chute' => 'Мусоропровод',
            'gas_pipe' => 'Газоснабжение',
            'phone_line' => 'Телефон',
            'internet' => 'Интернет',
            'bathroom_unit' => 'Санузел', 
            'concierge' => 'Консьерж',
            'closed_territory' => 'Закрытая территория', 
            'playground' => 'Детская площадка',
            'underground_parking' => 'Подземная парковка',
            'ground_parking' => 'Наземная парковка',
            'open_parking' => 'Открытая парковка',
            'multilevel_parking' => 'Многоуровневая парковка',
            'parking_info' => 'Информация о парковке',
            'barrier' => 'Шлагбаум',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
            'azimuth' => 'Азимут',
            'region_id' => 'Область',
            'region_district_id' => 'Район (региона)',
            'city_id' => 'Населенный пункт',
            'district_id' => 'Район (города)',
            'street_type_id' => 'Тип улицы',
            'street_name' => 'Название улицы',
            'building_number' => 'Номер дома',
            'location_info' => 'Информация о местоположении',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[SecondaryAdvertisement]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryAdvertisement()
    {
        return $this->hasOne(SecondaryAdvertisement::className(), ['id' => 'advertisement_id']);
    }
        
    /**
     * Gets query for [[SecondaryCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryCategory()
    {
        return $this->hasOne(SecondaryCategory::className(), ['id' => 'category_id']);
    }
        
    /**
     * Gets query for [[SecondaryPropertyType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryPropertyType()
    {
        return $this->hasOne(SecondaryPropertyType::className(), ['id' => 'property_type_id']);
    }
        
    /**
     * Gets query for [[SecondaryBuildingSeries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryBuildingSeries()
    {
        return $this->hasOne(SecondaryBuildingSeries::className(), ['id' => 'building_series_id']);
    }
        
    /**
     * Gets query for [[SecondaryRenovation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRenovation()
    {
        return $this->hasOne(SecondaryRenovation::className(), ['id' => 'renovation_id']);
    }
        
    /**
     * Gets query for [[BuildingMaterial]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingMaterial()
    {
        return $this->hasOne(BuildingMaterial::className(), ['id' => 'material_id']);
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
     * Gets query for [[Newbuilding]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuilding()
    {
        return $this->hasOne(Newbuilding::className(), ['id' => 'newbuilding_id']);
    }
        
    /**
     * Gets query for [[Entrance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrance()
    {
        return $this->hasOne(Entrance::className(), ['id' => 'entrance_id']);
    }
        
    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * Gets query for [[SecondaryRoomImage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(SecondaryRoomImage::className(), ['secondary_room_id' => 'id']);
    }
    
    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
    
    /**
     * Gets query for [[RegionDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegionDistrict()
    {
        return $this->hasOne(RegionDistrict::className(), ['id' => 'region_district_id']);
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

    /**
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    /**
     * Gets query for [[StreetType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreetType()
    {
        return $this->hasOne(StreetType::className(), ['id' => 'street_type_id']);
    }

    /**
     * Get address from references
     *
     * @return string
     */
   /* public function getAddress()
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
    } */

}