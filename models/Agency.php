<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property int $import_id
 * @property string $name
 * @property string $address
 * @property float|null $longitude
 * @property float|null $latitude
 * @property int $logo
 * @property string $detail
 * @property int $user_limit
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User[] $agents
 * @property Contact[] $contacts
 * @property User[] $managers
 * @property User[] $users
 * @property Import $import
 */
class Agency extends \yii\db\ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'detail', 'address'], 'required'],
            [['user_limit'], 'integer'],
            [['longitude', 'latitude'], 'double'],
            [['detail', 'offer_info', 'email', 'phone', 'url'], 'string'],
            [['email'], 'email'],
            [['logo', 'name', 'address'], 'string', 'max' => 200],
            [['import_id'], 'safe'],
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
            'user_limit' => 'Лимит пользователей',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'url' => 'Сайт'
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

        if (!empty($this->logo)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->logo"));
        }

        return true;
    }

    /**
     * Check that current user is agency's user
     * 
     * @return boolean
     */
    public function hasCurrentUser()
    {
        return $this->getUsers()->where(['id' => \Yii::$app->user->id])->exists() == 1;
    }
    
    /**
     * Gets query for agency's agent.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgents()
    {
        return $this->getUsers()
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = id')
            ->where(['auth_assignment.item_name' => 'agent']);
    }

    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['id' => 'contact_id'])
                ->viaTable('agency_contact', ['agency_id' => 'id']);
    }
    
    /**
     * Gets query for agency's managers.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManagers()
    {
        return $this->getUsers()
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = id')
            ->where(['auth_assignment.item_name' => 'manager']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['agency_id' => 'id'])
                ->where(['=', 'status', User::STATUS_ACTIVE]);
    }
        
    /**
     * Check if agency has import
     * 
     * @return boolean
     */
    public function hasImport()
    {
        return !is_null($this->import);
    }

    /**
     * Gets query for [[SecondaryImport]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImport()
    {
        return $this->hasOne(SecondaryImport::className(), ['id' => 'import_id']);
    }

    /**
     * Gets query for [[SecondaryAdvertisment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryAdvertisements()
    {
        return $this->hasMany(SecondaryAdvertisement::className(), ['agency_id' => 'id'])
                ->inverseOf('agency');
    }

}
