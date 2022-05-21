<?php

namespace app\models;

use app\components\traits\FillAttributes;
use app\models\query\FlatQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use app\components\flat\SvgDom;

/**
 * This is the model class for table "flat".
 *
 * @property int $id
 * @property int $newbuilding_id
 * @property string|null $number
 * @property string $layout
 * @property string|null $detail
 * @property float $area
 * @property int $rooms
 * @property int $floor
 * @property int|null $section
 * @property float|null $unit_price
 * @property float|null $price
 * @property int $action_id
 * @property float $discount
 * @property float $azimuth
 * @property string|null $notification
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $extra_data
 * @property int $composite_flat_id
 *
 * @property News[] $actions
 * @property News[] $activeActions
 * @property News[] $assignedNews
 * @property Developer $developer
 * @property CompositeFlat compositeFlat
 * @property Flat[] compositeFlatParts
 * @property FlatImage[] $flatImages
 * @property Furnish[] $furnishes
 * @property Newbuilding $newbuilding
 * @property NewbuildingComplex $newbuildingComplex
 * @property News[] $news
 */
class Flat extends ActiveRecord
{
    use FillAttributes;

    const STATUS_SALE = 0;
    const STATUS_RESERVED = 1;
    const STATUS_SOLD = 2;

    public static $status = [
        self::STATUS_SALE => 'Продаётся',
        self::STATUS_SOLD => 'Продана',
        self::STATUS_RESERVED => 'Зарезервирована',
    ];

    public $minPriceCash;
    public $minPriceCredit;
    public $maxPriceCash;
    public $maxPriceCredit;
    public $minArea;
    public $maxArea;
    public $maxDiscount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flat';
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
    public function beforeSave($insert) 
    {
        if(!$insert && !empty($this->layout)) {
            $model = self::findOne($this->id);

            if($this->layout != $model->layout) {
                $filePath = \Yii::getAlias('@webroot/uploads/' . $model->layout);
                
                if(!is_dir($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_id', 'number', 'area', 'rooms', 'floor', 'rooms'], 'required'],
            [['newbuilding_id', 'number', 'floor', 'status'], 'integer'],
            [['detail', 'notification', 'extra_data', 'floor_layout', 'layout_coords'], 'string'],
            [['discount', 'unit_price_cash', 'price_cash', 'unit_price_credit', 'price_credit'], 'double'],
            [['area', 'azimuth', 'section', 'floor_position'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
            [['is_euro', 'is_studio'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_id' => 'Newbuilding ID',
            'Number' => 'Номер',
            'layout' => 'Изображение планировки',
            'detail' => 'Информация',
            'notification' => 'Уведомление',
            'area' => 'Площадь',
            'rooms' => 'Количество комнат',
            'floor' => 'Этаж',
            'section'=> 'Подъезд',
            'unit_price_cash' => 'Цена(Нал.) за кв.м.',
            'price_cash' => 'Цена(Нал.)',
            'unit_price_credit' => 'Цена(Ипотека) за кв.м.',
            'price_credit' => 'Цена(Ипотека)',
            'discount' => 'Скидка',
            'azimuth' => 'Азимут',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_euro' => 'Европланировка',
            'is_studio' => 'Студия'
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return FlatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FlatQuery(get_called_class());
    }

    /**
     * Check that flat is favorite flat of current user
     *
     * @return boolean
     */
    public function isFavorite()
    {
        return count($this->favorites);
    }

    /**
     * Check that flat is part of complex flat
     *
     * @return boolean
     */
    public function isPartOfCompositeFlat()
    {
        return !is_null($this->compositeFlat);
    }

    /**
     * Check that flat is sold
     *
     * @return boolean
     */
    public function isSold()
    {
        return $this->status == self::STATUS_SOLD;
    }

    /**
     * Check that flat has discount
     *
     * @return boolean
     */
    public function hasDiscount()
    {
        return $this->discount > 0;
    }

    /**
     * Check that flat has active actions
     *
     * @return boolean
     */
    public function hasActions()
    {
        return count($this->activeActions) !== 0;
    }

    /**
     * Check that flat has extra data
     *
     * @return boolean
     */
    public function hasExtraData()
    {
        return !is_null($this->extra_data);
    }

    /**
     * Check that flat has notification
     *
     * @return boolean
     */
    public function hasNotification()
    {
        return !is_null($this->notification);
    }

    /**
     * Getting flat address
     *
     * @return mixed
     */
    public function getAddress()
    {
        if (is_null($this->newbuildingComplex->address)) {
            return NULL;
        } else {
            return "{$this->newbuildingComplex->address}, №{$this->number}";
        }
    }

    public function getAzimuth() {
        return $this->newbuilding->azimuth;
    }

    /**
     * Get flat's cost
     *
     * @return float
     */
    public function getCost()
    {
        if (!is_null($this->price)) {
            return $this->price;
        } else if(!is_null($this->unit_price)) {
            return $this->unit_price * $this->area;
        } else {
            return NULL;
        }
    }

    /**
     * Get flat's cash price with discount
     *
     * @return float
     */
    public function getCashPriceWithDiscount()
    {
        return $this->price_cash * (1 - $this->discount);
    }

    /**
     * Get flat's credit price with discount
     *
     * @return float
     */
    public function getCreditPriceWithDiscount()
    {
        return $this->price_credit * (1 - $this->discount);
    }
 
    /**
     * Get flat's price per area
     *
     * @return float
     */
    public function getPricePerArea()
    {
        if ($this->area == 0) {
            return 0;
        }
        return round($this->price_cash / $this->area);
    }

	/**
	 *	Get flat's rooms title as string
	 *
	 * @return string
	 */
	public function getRoomsTitle()
	{
		if ($this->rooms < 2 && $this->is_studio) {
            return 'Cтудия';
		}
        
        if($this->rooms === 0) {
			return 'Квартира свободной планировки';
        }

        if($this->is_euro) {
            $roomsTitle = \Yii::$app->formatter->asNumberStringEuro($this->rooms);

            if($this->rooms > 3) {
                $roomsTitle .= 'комнатная евро квартира';
            }
        } else {
		    $roomsTitle = \Yii::$app->formatter->asNumberString($this->rooms) . 'комнатная квартира';
        }

        return $roomsTitle;
	}

    /**
     * Get extra data as array
     *
     * @return array
     */
    public function getExtraData()
    {
        return json_decode($this->extra_data, true);
    }

    /**
     * Get floor layout
     *
     * @return mixed
     */
    public function getFloorLayout()
    {
        $result = NULL;
        $singleFloorItem = NULL;
        $singleSectionItem = NULL;
        $item = NULL;

        foreach ($this->newbuilding->floorLayouts as $floorLayout) {
            if ($this->isAttributeInStringRange($this->floor, $floorLayout->floor)
                && $this->isAttributeInStringRange($this->section, $floorLayout->section)
            ) {
                $isSingleFloor = count(explode('-', $floorLayout->floor)) < 2;
                $isSingleSection = count(explode('-', $floorLayout->section)) < 2;

                if ($isSingleFloor && $isSingleSection) {
                    return $floorLayout;
                }

                if ($isSingleFloor) {
                    $singleFloorItem = $floorLayout;
                } else if ($isSingleSection) {
                    $singleSectionItem = $floorLayout;
                } else {
                    $item = $floorLayout;
                }
            }
        }

        if (!is_null($singleFloorItem)) {
            return $singleFloorItem;
        }

        if (!is_null($singleSectionItem)) {
            return $singleSectionItem;
        }

        return $item;
    }

    /**
     * Gets actions
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->getAssignedNews()->onlyActions();
    }

    /**
     * Gets active actions
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveActions()
    {
        return $this->getAssignedNews()->onlyActions(true);
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])
                ->viaTable('news_flat', ['flat_id' => 'id']);
    }

    /**
     * Gets query for [[CompositeFlat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompositeFlat()
    {
        return $this->hasOne(CompositeFlat::className(), ['id' => 'composite_flat_id']);
    }

    /**
     * Get other flats of complex object to which the flat belongs
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompositeFlatParts()
    {
        return $this->compositeFlat->getFlats()->where(['<>', 'id', $this->id])->all();
    }

    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id'])
                ->via('newbuildingComplex');
    }

    /**
     * Gets query for [[Favorite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::className(), ['flat_id' => 'id'])->forCurrentUser();
    }

    /**
     * Gets query for [[Furnish]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFurnishes()
    {
        return $this->hasMany(Furnish::className(), ['newbuilding_complex_id' => 'id'])
                ->via('newbuildingComplex');
    }

    /**
     * Gets query for [[FlatImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlatImages()
    {
        return $this->hasMany(FlatImage::className(), ['flat_id' => 'id']);
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
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id'])
                ->via('newbuilding');
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return News::find()
                ->join('INNER JOIN', 'news_newbuilding_complex', 'news.id = news_newbuilding_complex.news_id')
                ->join('INNER JOIN', 'newbuilding_complex', 'newbuilding_complex.id = news_newbuilding_complex.newbuilding_complex_id')
                ->join('INNER JOIN', 'newbuilding', 'newbuilding_complex.id = newbuilding.newbuilding_complex_id')
                ->join('INNER JOIN', 'flat', 'newbuilding.id = flat.newbuilding_id')
                ->where(['flat.id' => $this->id])
                ->groupBy('news.id');
    }

    /**
     * Gets query for neighboring flats [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNeighboringFlats()
    {
        if(!empty($this->section) && !empty($this->floor)) {
            return self::find()
                        ->where([
                            'floor' => $this->floor, 
                            'section' => $this->section,
                            'newbuilding_id' => $this->newbuilding->id,
                        ])
                        ->andWhere(['!=', 'id', $this->id]);
        }

        return null;
    }

    /**
     * Check that attribute in string range
     *
     * return boolean
     */
    private function isAttributeInStringRange($value, $stringRange)
    {
        $floors = explode('-', $stringRange);

        if (count($floors) > 1) {
            return ($value >= (int)$floors[0]) && ($value <= (int)$floors[1]);
        } else {
            return (int)$stringRange == $value;
        }
    }

    /**
     * Return floor layout with displayed nearest flats
     *
     * @return string svg
     */
    public function getFloorLayoutSvg()
    {
        $floorLayoutImage = null;
        if(!is_null($this->layout_coords) && !is_null($this->floorLayout) && !is_null($this->floorLayout->image)) {
            $floorLayoutPath = \Yii::getAlias("@webroot/uploads/{$this->floorLayout->image}");
            
            if(file_exists($floorLayoutPath) && SvgDom::isNameSvg($this->floorLayout->image)) {
                $floorLayoutDom = new SvgDom($floorLayoutPath);
                $floorLayoutDom->appendNode('polygon', [
                    'class' => 'flat-polygon-main',
                    'points' => $this->layout_coords
                ]);
                
                $neighboringFlats = $this->getNeighboringFlats()->all();

                if(!is_null($neighboringFlats ) && !empty($neighboringFlats )) {
                    $nodeList = [];
                    foreach($neighboringFlats as $flat) {
                        if(empty($flat->layout_coords)) {
                            continue;
                        }
                        $nodeList[] = [
                            'name' => 'a',
                            'attributes' => [
                                'xlink:href' => Url::to(['view', 'id' => $flat->id]),
                                'style' => 'cursor: pointer',
                            ],
                            'children' => [
                                [
                                    'name' => 'polygon',
                                    'attributes' => [
                                        'class' => 'flat-polygon',
                                        'points' => $flat->layout_coords,
                                    ]
                                ]
                            ]
                        ];
                    }

                    $floorLayoutDom->appendNodes($nodeList);
                }
                
                $floorLayoutImage = $floorLayoutDom->getFileContent();
            }
        }

        return $floorLayoutImage;
    }

    /**
     * Gets query for flats with rooms count [[Flat]].
     *
     * @param int $roomsCount
     * @return \yii\db\ActiveQuery
     */
    public static function getWithRooms($roomsCount) 
    {
        return self::find()
            ->select('count(*) as count')
            ->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id')
            ->where('nc2.active = true')
            ->andWhere('flat.rooms = :roomsCount', [
                ':roomsCount' => $roomsCount
            ]);
    }

    /**
     * Gets query for flats with room count [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getStudio()
    {
        return Flat::find()
            ->select('count(*) as count')
            ->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id')
            ->where('nc2.active = true')
            ->andWhere('flat.is_studio = true');
    }

    /**
     * Gets query for surrendered flats [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getSurrendered()
    {
        return Flat::find()
            ->select('count(*) as count')
            ->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id')
            ->where('n1.status = :status', [
                ':status' => \app\models\Newbuilding::STATUS_FINISH
            ])
            ->andWhere('nc2.active = true');
    }
    
    /**
     * Get query for flats with deadline in the end of the year  [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getWithEndYearDeadline()
    {
        return Flat::find()
            ->select('count(*) as count')
            ->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id')
            ->andWhere('nc2.active = true')
            ->andWhere(
                'EXTRACT(YEAR FROM n1.deadline) = :deadlineYear',
                [
                    ':deadlineYear' => date('Y')
                ]
            );
    }

    /**
     * Get query for flats with deadline after the specified number of years  [[Flat]].
     *
     * @param int $years
     * @return \yii\db\ActiveQuery
     */
    public static function getAfterYearsDeadline($years)
    {
        return Flat::find()
            ->select('count(*) as count')
            ->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id')
            ->andWhere('nc2.active = true')
            ->andWhere(
                'EXTRACT(YEAR FROM n1.deadline) = :deadlineYear',
                [
                    ':deadlineYear' => date('Y', strtotime("+$years year"))
                ]
            );
    }
}