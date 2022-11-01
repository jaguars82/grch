<?php

namespace app\models;

use app\components\traits\FillAttributes;
use app\components\traits\FlatPrices;
use app\models\query\DeveloperQuery;
use app\models\News;

/**
 * This is the model class for table "developer".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property floot|null $longitude
 * @property floot|null $latitude
 * @property string $logo
 * @property string $detail
 * @property string|null free_reservation
 * @property string|null paid_reservation
 *
 * @property Contact[] $contacts
 * @property Flat[] $flats
 * @property Import $import
 * @property NewbuildingComplex[] $newbuildingComplexes
 * @property Newbuilding[] $newbuildings
 * @property News[] $news
 */
class Developer extends \yii\db\ActiveRecord
{
    use FillAttributes;
    use FlatPrices;
       
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'developer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'logo', 'detail', 'sort'], 'required'],
            [['longitude', 'latitude'], 'double'],
            [['detail', 'offer_info', 'free_reservation', 'paid_reservation', 'phone', 'url', 'email'], 'string'],
            [['name', 'address', 'logo'], 'string', 'max' => 200],
            [['sort'], 'filter', 'filter' => function($value) {
                return preg_replace('/[^\d]/', "", $value);
            }],
            [['sort'], 'integer'],
            [['name', 'logo'], 'unique'],
            [['import_type', 'import_endpoint', 'import_schedule', 'import_id', 'edited_fields'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'address' => 'Адрес',
            'logo' => 'Логотип',
            'detail' => 'Информация',
            'free_reservation' => 'Условия бесплатной брони',
            'paid_reservation' => 'Условия платной брони',
            'sort' => 'Сортировка',
            'phone' => 'Номер телефона',
            'url' => 'Адрес сайта',
            'email' => 'Email'
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) 
        {
            $this->edited_fields = serialize($this->edited_fields);
            return true;
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->edited_fields = unserialize($this->edited_fields);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return DeveloperQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DeveloperQuery(get_called_class());
    }
    
    /**
     * Check that developer has import
     * 
     * @return boolean
     */
    public function hasImport()
    {
        return !is_null($this->import);
    }
    
    /**
     * Get all developers in array form
     * 
     * @return array
     */
    public static function getAllAsList()
    {
        $result = self::find()
            ->whereNewbuildingComplexesExist()
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $developers = [];
        
        foreach ($result as $key => $developer) {
            $developers[$key] = $developer['name'];
        }
        
        return $developers;
    }

    /**
     * Get the number of actions for developer
     *
     * @return integer
     */
    public function getActionNumber()
    {
        $newsList = News::find()
            ->join('INNER JOIN', 'action_data', 'news.id = action_data.news_id')
            ->where(['>=', 'expired_at', date('Y-m-d')])->all();

        $number = 0;
        foreach ($newsList as $news) {
            $flatFilter = json_decode($news->actionData->flat_filter);
            if ($flatFilter->developer == $this->id) {
                $number++;
            }
        }

        return $number;
    }
    
    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['id' => 'contact_id'])
                ->viaTable('developer_contact', ['developer_id' => 'id']);
    }

    /**
     * Gets query for [[Offices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffices() 
    {
        return $this->hasMany(Office::className(), ['id' => 'office_id'])
                ->viaTable('developer_office', ['developer_id' => 'id']);
    }
    
    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['newbuilding_id' => 'id'])
                ->via('newbuildings')->inverseOf('developer');
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
     * Gets query for [[Import]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImport()
    {
        return $this->hasOne(Import::className(), ['id' => 'import_id']);
    }
     
    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplexes()
    {
        return $this->hasMany(NewbuildingComplex::className(), ['developer_id' => 'id'])
                ->inverseOf('developer');
    }
    
    /**
     * Gets query for [[Newbuilding]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildings()
    {
        return $this->hasMany(Newbuilding::className(), ['newbuilding_complex_id' => 'id'])
                ->via('newbuildingComplexes');
    }
 
    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return News::find()
                ->where(['is_archived' => 0])
                ->join('INNER JOIN', 'news_newbuilding_complex', 'news.id = news_newbuilding_complex.news_id')
                ->join('INNER JOIN', 'newbuilding_complex', 'newbuilding_complex.id = news_newbuilding_complex.newbuilding_complex_id')
                ->andWhere(['developer_id' => $this->id])
                ->groupBy('news.id');
    } 
    
    /**
     * if developer has representative in User table
     */
    public function hasRepresentative()
    {
        $result = false;
        $repres_amount = User::find()
            ->where(['developer_id' => $this->id])
            ->count();
        if ($repres_amount > 0) { $result = true; }
        return $result;
    }
}
