<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lesson".
 *
 * @property int $id
 * @property int|null $lesson_category_id
 * @property int|null $sort_order Position of the lesson in the list
 * @property string|null $image filename of lesson's tumbnail
 * @property string|null $icon lesson's pictogram
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $description
 * @property string|null $content
 * @property int|null $videohosting_type
 * @property string|null $video_source url of the video on videohosting or the name of the file on local server
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property LessonCategory $lessonCategory
 */
class Lesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_category_id', 'sort_order', 'videohosting_type'], 'integer'],
            [['description', 'content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['image'], 'string', 'max' => 55],
            [['icon'], 'string', 'max' => 25],
            [['title', 'subtitle', 'video_source'], 'string', 'max' => 255],
            [['lesson_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => LessonCategory::className(), 'targetAttribute' => ['lesson_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_category_id' => 'Lesson Category ID',
            'sort_order' => 'Sort Order',
            'image' => 'Image',
            'icon' => 'Icon',
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'content' => 'Content',
            'videohosting_type' => 'Videohosting Type',
            'video_source' => 'Video Source',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[LessonCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLessonCategory()
    {
        return $this->hasOne(LessonCategory::className(), ['id' => 'lesson_category_id']);
    }
}