<?php

namespace app\models;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string|null $name
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'string', 'max' => 200],
        ];
    }
}
