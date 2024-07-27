<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lesson_category".
 *
 * @property int $id
 * @property int|null $parent_category_id The id of parent category, NULL - for categories in the root
 * @property int|null $sort_order Position of the category in the list
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $description
 * @property string|null $image filename of categorie's tumbnail
 * @property string|null $icon categorie's pictogram
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Lesson[] $lessons
 */
class LessonCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_category_id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'subtitle'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 55],
            [['icon'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_category_id' => 'Parent Category ID',
            'sort_order' => 'Sort Order',
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'image' => 'Image',
            'icon' => 'Icon',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Lessons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['lesson_category_id' => 'id']);
    }
}