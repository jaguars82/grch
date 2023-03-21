<?php

namespace app\models;

use app\models\query\FavoriteQuery;

/**
 * This is the model class for table "favorite".
 *
 * @property int $id
 * @property int $user_id
 * @property int $flat_id
 * @property string|null $comment
 * @property string|null $archived_by
 *
 * @property Flat $flat
 * @property User $user
 */
class Favorite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'flat_id'], 'required'],
            [['user_id', 'flat_id'], 'integer'],
            [['comment'], 'string'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comment' => 'Комментарий',
        ];
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FavoriteQuery(get_called_class());
    }
    
    /**
     * Check that favotite flat has comment
     * 
     * @return boolean
     */
    public function hasComment()
    {
        return !is_null($this->comment);
    }
    
    /**
     * Check that favorite flat is archived
     * 
     * @return boolean
     */
    public function isArchived()
    {
        return !is_null($this->archived_by);
    }
    
    /**
     * Move comment for favorite flat to archive state
     * 
     * @return $this
     */
    public function archive()
    {
        $this->archived_by = (new \DateTime())->format('Y-m-d H:i:s');
        
        return $this;
    }
    
    /**
     * Move comment for favorite flat to active state
     * 
     * @return $this
     */
    public function activate()
    {
        $this->archived_by = NULL;
        
        return $this;
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
