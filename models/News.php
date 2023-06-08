<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;
use app\models\query\NewsQuery;
use app\models\Flat;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $detail
 * @property string|null $image
 * @property int $category
 * @property string|null $search_link
 * @property boolean $is_archived
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ActionData $actionData
 * @property NewbuildingComplex[] $newbuildingComplexes
 * @property NewsFile[] $newsFiles
 */
class News extends ActiveRecord
{
    use FillAttributes;
    
    const CATEGORY_NEWS = 0;
    const CATEGORY_ACTION = 1;
    
    public static $category = [
        self::CATEGORY_NEWS => 'Новость',
        self::CATEGORY_ACTION => 'Акция',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
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
            [['title', 'detail'], 'required'],
            [['detail'], 'string'],
            [['category'], 'integer'],
            [['is_archived'], 'boolean'],
            [['created_at', 'updated_at', 'search_link'], 'safe'],
            [['title', 'image'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Заголовок',
            'detail' => 'Описание',
            'image' => 'Изображение',
            'search_link' => 'Cсылки на страницу поиска',
            'is_archived' => 'В архиве',
            'category' => 'Категория',
            'created_at' => 'Дата размещения',
            'updated_at' => 'Дата обновления',
        ];
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if (!empty($this->image)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->image"));
        }

        $fileList = $this->newsFiles;
        foreach ($fileList as $file) {
            $file->delete();
        }

        return true;
    }
    
    /**
     * Get action's resume
     * 
     * @return string
     */
    public function getResume()
    {
        return $this->actionData->resume;
    }
    
    /**
     * Check that news is action
     * 
     * @return boolean
     */
    public function isAction()
    {
        return $this->category == self::CATEGORY_ACTION;
    }

    /**
     * Gets query for [[ActionData]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionData()
    {
        return $this->hasOne(ActionData::className(), ['news_id' => 'id']);
    }
    
    /**
     * Gets query for [[Bank]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplexes()
    {
        return $this->hasMany(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id'])
                ->viaTable('news_newbuilding_complex', ['news_id' => 'id']);
    }
    
    /**
     * Gets query for [[NewsFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewsFiles()
    {
        return $this->hasMany(NewsFile::className(), ['news_id' => 'id']);
    }

    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedFlats()
    {
        return $this->hasMany(Flat::className(), ['id' => 'flat_id'])
                ->viaTable('news_flat', ['news_id' => 'id']);
    }

    /**
     * Gets only actions
     */
    public function getActions() {
        $actions = $this->find()
        ->where(
            ['category' => 1, 'is_archived' => 0]
        )
        ->all();
        return $actions;
    }
}
