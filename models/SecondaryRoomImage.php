<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "secondary_room_image".
 *
 * @property int $id
 * @property int|null $secondary_room_id
 * @property string|null $location_type "local" - if the image is on local server, "remote" - if the image is on remote server
 * @property string|null $url image url for remote files
 * @property string|null $filename image filename for local files
 */
class SecondaryRoomImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_room_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['secondary_room_id'], 'integer'],
            [['location_type'], 'string', 'max' => 10],
            [['url'], 'string', 'max' => 2000],
            [['filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'secondary_room_id' => 'Объект недвижимости',
            'location_type' => 'Тип расположения',
            'url' => 'Url',
            'filename' => 'Название файла',
        ];
    }
}
