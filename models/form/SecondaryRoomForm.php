<?php

namespace app\models\form;

use yii\base\Model;
use yii\web\UploadedFile;
use app\components\traits\ProcessFile;

class SecondaryRoomForm extends Model
{
    use ProcessFile;

    public $advertisement_id;
    public $category_id;
    public $category_string;
    public $property_type_id;
    public $property_type_string;
    public $building_series_id;
    public $building_series_string;
    public $newbuilding_complex_id;
    public $newbuilding_complex_string;
    public $newbuilding_id;
    public $newbuilding_string;
    public $entrance_id;
    public $entrance_string;
    public $flat_id;
    public $number;
    public $number_suffix;
    public $price;
    public $unit_price;
    public $mortgage;
    public $layout;
    public $detail;
    public $special_conditions;
    public $area;
    public $kitchen_area;
    public $living_area;
    public $ceiling_height;
    public $rooms;
    public $balcony_info;
    public $balcony_amount;
    public $loggia_amount;
    public $windowview_info;
    public $windowview_street;
    public $windowview_yard;
    public $dressing_room;
    public $panoramic_windows;
    public $is_studio;
    public $is_euro;
    public $built_year;
    public $renovation_id;
    public $renovation_string;
    public $quality_index;
    public $quality_string;
    public $floor;
    public $section;
    public $address;
    public $total_floors;
    public $material_id;
    public $material;
    public $elevator;
    public $elevator_info;
    public $elevator_passenger_amount;
    public $elevator_freight_amount;
    public $rubbish_chute;
    public $gas_pipe;
    public $phone_line;
    public $internet;
    public $bathroom_index;
    public $bathroom_unit;
    public $concierge;
    public $closed_territory;
    public $playground;
    public $underground_parking;
    public $ground_parking;
    public $multilevel_parking;
    public $open_parking;
    public $parking_info;
    public $barrier;
    public $images = [];
    public $longitude;
    public $latitude;
    public $azimuth;
    public $region_id;
    public $region_district_id;
    public $city_id;
    public $district_id;
    public $street_type_id;
    public $street_name;
    public $building_number;
    public $location_info;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advertisement_id'], 'required'],
            [['advertisement_id', 'category_id', 'property_type_id', 'building_series_id', 'newbuilding_complex_id', 'newbuilding_id', 'entrance_id', 'flat_id', 'number', 'rooms', 'balcony_amount', 'loggia_amount', 'built_year', 'renovation_id', 'quality_index', 'floor', 'section', 'total_floors', 'material_id', 'elevator_passenger_amount', 'elevator_freight_amount', 'bathroom_index', 'azimuth', 'region_id', 'region_district_id', 'city_id', 'district_id', 'street_type_id'], 'integer'],
            [['price', 'unit_price', 'area', 'kitchen_area', 'living_area', 'ceiling_height', 'longitude', 'latitude'], 'double'],
            [['category_string', 'property_type_string', 'building_series_string', 'newbuilding_complex_string', 'newbuilding_string', 'entrance_string', 'number_suffix', 'layout', 'detail', 'special_conditions', 'balcony_info', 'windowview_info', 'renovation_string', 'quality_string', 'address', 'material', 'elevator_info', 'bathroom_unit', 'parking_info', 'street_name', 'building_number'], 'string'],
            [['mortgage', 'windowview_street', 'windowview_yard', 'dressing_room', 'panoramic_windows', 'is_studio', 'is_euro', 'elevator', 'rubbish_chute', 'gas_pipe', 'phone_line', 'internet', 'concierge', 'closed_territory', 'playground', 'underground_parking', 'ground_parking', 'open_parking', 'multilevel_parking', 'barrier'], 'boolean'],
            [['images'],  'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg', 'maxFiles' => 100],
        ];
    }

    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->images = UploadedFile::getInstancesByName('images');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFiles('images');

        return true;
    }
}