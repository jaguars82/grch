<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news_file".
 *
 * @property int $id
 * @property int $news_id
 * @property string|null $name
 * @property string|null $saved_name
 *
 * @property News $news
 */
class NewsFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['news_id'], 'required'],
            [['news_id'], 'integer'],
            [['name', 'saved_name'], 'string', 'max' => 200],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'news_id' => 'News ID',
            'name' => 'Название',
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

        if (!empty($this->saved_name)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->saved_name"));
        }

        return true;
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }
}
