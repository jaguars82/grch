<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "office".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $comment
 *
 */
class Office extends \yii\db\ActiveRecord
{
    use FillAttributes;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'office';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phones'], 'safe'],
            [['comment'], 'string'],
            [['name', 'address'], 'string', 'max' => 200],
            [['name', 'address'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'address' => 'Адрес',
            'phones' => 'Телефоны',
            'comment' => 'Комментарий',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) 
        {
            $this->phones = serialize($this->phones);
            return true;
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->phones = unserialize($this->phones);
    }

    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id'])
                ->viaTable('developer_office', ['office_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\OfficeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\OfficeQuery(get_called_class());
    }
}
