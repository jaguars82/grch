<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status_label_type".
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 *
 */
class StatusLabelType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_label_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'short_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название статуса',
            'short_name' => 'Короткое название статуса',
        ];
    }

}