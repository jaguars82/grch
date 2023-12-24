<?php

namespace app\models;

use app\components\traits\FillAttributes;
use app\components\traits\FlatPrices;
use app\models\query\NewbuildingComplexQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use \app\models\BuildingType;
use \app\models\Entrance;
use \app\models\StreetType;
use \app\models\District;
use \app\models\Region;
use \app\models\City;

/**
 * This is the model class for table "newbuilding_complex".
 *
 * @property int $id
 * @property int $developer_id
 * @property string $name
 * @property string|null $address
 * @property floot $longitude
 * @property floot $latitude
 * @property string $logo
 * @property string $detail
 * @property string|null algorithm
 * @property int|null $offer_new_price_permit
 * @property string|null project_declaration
 * @property string|null virtual_structure
 *
 * @property News[] $actions
 * @property News[] $activeActions
 * @property Bank[] $banks
 * @property Contact[] $contacts
 * @property Developer $developer
 * @property Flat[] $flats
 * @property Furnish[] $furnishes
 * @property Newbuilding[] $newbuildings
 * @property Entrance[] $entrances
 * @property News[] $news
 * @property Image[] $images
 * @property Document[] $documents
 */
class NewbuildingComplex extends ActiveRecord
{
    use FillAttributes;
    use FlatPrices;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'newbuilding_complex';
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
            [['developer_id', 'name'], 'required'],
            [['archive_id', 'building_type_id', 'street_type_id', 'district_id', 'city_id', 'region_id', 'developer_id', 'offer_new_price_permit'], 'integer'],
            [['building_number'], 'string', 'max' => 20],
            [['longitude', 'latitude'], 'double'],
            [['detail', 'offer_info', 'algorithm'], 'string'],
            [['project_declaration', 'bank_tariffs', 'virtual_structure', 'virtualbuildings'], 'safe'],
            [['name', 'logo', 'street_name', 'master_plan'], 'string', 'max' => 200],
            [['name', 'logo'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
            [['archive_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archive::className(), 'targetAttribute' => ['archive_id' => 'id']],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['street_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StreetType::className(), 'targetAttribute' => ['street_type_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['developer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Developer::className(), 'targetAttribute' => ['developer_id' => 'id']],
            [['longitude', 'latitude', 'detail', 'algorithm', 'offer_info'], 'default', 'value' => NULL],
            [['active', 'has_active_buildings', 'use_virtual_structure'], 'boolean'],
            [['active', 'has_active_buildings', 'use_virtual_structure'], 'default', 'value' => true]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'developer_id' => 'Developer ID',
            'name' => 'Название',
            'active' => 'Активность',
            'street_name' => 'Название улицы',
            'building_number' => 'Номер здания',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
            'logo' => 'Логотип',
            'detail' => 'Информация',
            'offer_new_price_permit' => 'Offer New Price Permit',
            'advantages' => 'Преимущества',
            'algorithm' => 'Алгоритм действия',
            'project_declaration' => 'Проектная декларация',
            'address' => 'Адрес',
            'use_virtual_structure' => 'Использовать виртуальную структуру'
        ];
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) 
        {
            $this->bank_tariffs = serialize($this->bank_tariffs);

            if (!$insert) {
                $model = self::findOne($this->id);

                if ($this->logo != $model->logo) {
                    $filePath = \Yii::getAlias('@webroot/uploads/' . $model->logo);

                    if (!is_dir($filePath) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                if ($this->master_plan != $model->master_plan) {
                    $filePath = \Yii::getAlias('@webroot/uploads/' . $model->master_plan);

                    if (!is_dir($filePath) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if (!empty($this->logo)) {
            unlink(\Yii::getAlias("@webroot/uploads/$this->logo"));
        }

        if (!empty($this->master_plan)) {
            unlink(\Yii::getAlias("@webroot/uploads/$this->master_plan"));
        }

        $imageList = $this->images;
        foreach ($imageList as $image) {
            $image->delete();
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->bank_tariffs = unserialize($this->bank_tariffs);
    }

    /**
     * {@inheritdoc}
     * 
     * @return NewbuildingComplexQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewbuildingComplexQuery(get_called_class());
    }
    
    /**
     * Check that newbuilding complex has active actions
     * 
     * @return boolean
     */
    public function hasActions()
    {
        return count($this->activeActions);
    }
    
    /**
     * Check that newbuilding complex has discount
     * 
     * @return boolean
     */
    public function hasDiscount()
    {
        return count($this->flatsWithDiscount)> 0;
    }

    /**
     * Get max newbuilding complex max discount
     * 
     * @return dobule
     */
    public function getMaxDiscount()
    {
        $result = $this->getFlats()->select(
            ['max(discount) as maxDiscount']
        )->where(['!=', 'discount', 0])->one();
        
        return !is_null($result->maxDiscount) ? $result->maxDiscount : NULL;
    }

    /**
     * Get max newbuilding complex min yearly rate
     * 
     * @return double
     */
    public function getMinYearlyRate()
    {
        if(empty($this->bank_tariffs)) {
            return NULL;
        }
        
        $minYearlyRate = 0;
        foreach($this->bank_tariffs as $bank => $tariffs) {
            foreach($tariffs as $tariff) {
                if($minYearlyRate == 0 || $minYearlyRate > $tariff['yearlyRateAsPercent']) {
                    $minYearlyRate = $tariff['yearlyRateAsPercent'];
                }
            }
        }
		
		// temprory switch of minYearlyRate (ставка)
		return NULL;
		// uncomment the line below to switch on minYearlyRate (ставка)
        // return $minYearlyRate > 0 ? $minYearlyRate / 100 : NULL;
    }

    /**
     * Get all districts in array form
     * 
     * @return array
     */
    public static function getAllDistrictsAsList()
    {
        $result = self::find()
            ->select('district_id')
            ->distinct()
            ->where('district_id IS NOT NULL')
            ->all();
        
        $districts = [];
        
        foreach ($result as $newbuildingComplex) {
            $districts[$newbuildingComplex->district->id] = \Yii::$app->formatter->asCapitalize($newbuildingComplex->district->name);
        }
        
        return $districts;
    }
    
    /**
     * Get free flats count
     * 
     * @return int
     */
    public function getFreeFlats()
    {
        if(count($this->flats) == 0) {
            return 0;
        }

        $soldFlatsCount = count($this->getFlats()->where(['status' => Flat::STATUS_SALE])->all());
        return $soldFlatsCount/count($this->flats);
    }
    
    /**
     * Get array of possible count of flats rooms
     * 
     * @return array
     */
    public function getRooms()
    {        
        $rooms = array_unique(ArrayHelper::getColumn($this->flats, 'rooms'));
        asort($rooms);
                
        return $rooms;
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
     * Gets query for [[Bank]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBanks()
    {
        return $this->hasMany(Bank::className(), ['id' => 'bank_id'])
                ->viaTable('bank_newbuilding_complex', ['newbuilding_complex_id' => 'id']);
    }

    /**
     * Gets query for [[Advantage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdvantages()
    {
        return $this->hasMany(Advantage::className(), ['id' => 'advantage_id'])
                ->viaTable('newbuilding_complex_advantage', ['newbuilding_complex_id' => 'id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages() {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
                ->viaTable('newbuilding_complex_image', ['newbuilding_complex_id' => 'id']);
    }

    /**
     * Gets query for [[Document]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments() {
        return $this->hasMany(Document::className(), ['id' => 'document_id'])
                ->viaTable('newbuilding_complex_document', ['newbuilding_complex_id' => 'id']);
    }
    
    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['id' => 'contact_id'])
                ->viaTable('newbuilding_complex_contact', ['newbuilding_complex_id' => 'id']);
    }

    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id']);
    }

    /**
     * Gets query for [[Archive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArchive()
    {
        return $this->hasOne(Archive::className(), ['id' => 'archive_id']);
    }
    
    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
                ->via('newbuildings');
    }
    
    /**
     * Gets query for [[Entrance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrances()
    {
        return $this->hasMany(Entrance::className(), ['newbuilding_id' => 'id'])
                ->via('newbuildings');
    }
    
    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
            ->onlyActive();
    }
    
    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlatsWithDiscount()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
            ->where('discount IS NOT NULL AND discount != 0');
    }
    
    /**
     * Gets query for [[Furnish]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFurnishes()
    {
        return $this->hasMany(Furnish::className(), ['newbuilding_complex_id' => 'id']);
    }
   
    /**
     * Gets query for [[Newbuilding]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildings()
    {
        return $this->hasMany(Newbuilding::className(), ['newbuilding_complex_id' => 'id'])
                ->inverseOf('newbuildingComplex');
    }

    public function getActiveNewbuildings()
    {
        return $this->getNewbuildings()->onlyActive();
    }


    public function getVirtualbuildings()
    {
        if (!empty($this->virtual_structure)) {

            $structure = json_decode($this->virtual_structure);

                        
            foreach ($structure as $position) {
                $db_entrances = array();
                $activeFlats = 0;
                $reservedFlats = 0;
                
                foreach ($position->entrance_idies as $entranceID) {
                    $dbEntrance = (new Entrance())->findOne($entranceID);
                    array_push($db_entrances, $dbEntrance);
                    $activeFlats += $dbEntrance->getActiveFlats()->count();
                    $reservedFlats += $dbEntrance->getReservedFlats()->count();
                    //echo '<pre>'; var_dump($dbEntrance); echo '</pre>';
                }
                //echo '<pre>'; var_dump($position->entrance_idies); echo '</pre>'; die();
                $position->db_entrances = $db_entrances;
                $position->available_flats = $activeFlats + $reservedFlats;
                $position->active_flats = $activeFlats;
                $position->reserved_flats = $reservedFlats;
            }

            /*foreach ($structure as $position) {

            }*/

            return $structure;

        } else {
            return false;
        }
    }
	
	
    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])
                ->viaTable('news_newbuilding_complex', ['newbuilding_complex_id' => 'id'])
                ->onlyActual();
    }

    /**
     * Gets query for [[Stages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStages()
    {
        return $this->hasMany(NewbuildingComplexStage::className(), ['newbuilding_complex_id' => 'id']);
    }

    /**
     * Gets bank tariffs with calculated yearly_rate and initial_fee_rate
     *
     * @return array $bankTariffs
     */
    public function getPreparedBankTariffs()
    {
        $bankTariffs = [];

        if(!empty($this->bank_tariffs)) {
            foreach($this->bank_tariffs as $bankId => $tariffs) {
                foreach($tariffs as $key => $tariff) {
                    $bankTariffs[$bankId][$key] = $tariff;
                    $bankTariffs[$bankId][$key]['yearly_rate'] = $tariff['yearlyRateAsPercent'] / 100;
                    $bankTariffs[$bankId][$key]['initial_fee_rate'] = $tariff['initialFeeRateAsPercent'] / 100;
                }
            }
        }

        return $bankTariffs;
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
     * Gets query for [[BuildingType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingType()
    {
        return $this->hasOne(BuildingType::className(), ['id' => 'building_type_id']);
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

    public function setAddress($address)
    {

    }

    public function setDistrict($district)
    {

    }

    /**
     * Getting minimum flat area
     * 
     * @return float
     */
    public function getMinFlatArea()
    {
        $flatsData = $this->getFlats()->select([
            'min(area) as minArea'
        ])->withNonNullArea()->one();
        
        return !is_null($flatsData->minArea) ? $flatsData->minArea : null;
    }

    /**
     * Getting maximum flat area
     * 
     * @return float
     */
    public function getMaxFlatArea()
    {
        $flatsData = $this->getFlats()->select([
            'max(area) as maxArea'
        ])->withNonNullArea()->one();

        return !is_null($flatsData->maxArea) ? $flatsData->maxArea : null;
    }

    /**
     * Getting nearest newbuilding deadline
     * 
     * @return string
     */
    public function getNearestDeadline()
    {
        $result = $this
            ->getNewbuildings()
            ->select(['deadline'])
            ->orderBy(['deadline' => SORT_ASC])
            ->where(['>=', 'deadline', date('Y-m-d')])
            ->one();

        return !is_null($result) && isset($result->deadline) ? $result->deadline : null;
    }

    /**
     * Getting all materials
     * 
     * @return array
     */
    public function getMaterials()
    {
        $result = self::getNewbuildings()
            ->select('material')
            ->distinct()
            ->where('material IS NOT NULL')
            ->asArray()
            ->all();
        
        $materials = [];
    
        foreach ($result as $newbuilding) {
            $materials[] = $newbuilding['material'];
        }
        
        return $materials;
    }
}