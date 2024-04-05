<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "application_document".
 *
 * @property int $id
 * @property int $application_id
 * @property int $user_id
 * @property int $category a digital index that defines a document's category (corresponding categories are listed in the model)
 * @property string $file
 * @property string $name
 * @property float|null $size
 * @property string $filetype
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Application $application
 * @property User $user
 */
class ApplicationDocument extends \yii\db\ActiveRecord
{
    const CAT_RECIEPT = 1;
    const CAT_DDU = 2;

    public static $docCategory = [
        self::CAT_RECIEPT => 'Квитанция за бронь',
        self::CAT_DDU => 'Договор долевого участия',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'user_id', 'category', 'file', 'name', 'filetype'], 'required'],
            [['application_id', 'user_id', 'category'], 'integer'],
            [['size'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['file', 'name'], 'string', 'max' => 255],
            [['filetype'], 'string', 'max' => 10],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Application::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'user_id' => 'User ID',
            'category' => 'Category',
            'file' => 'File',
            'name' => 'Name',
            'size' => 'Size',
            'filetype' => 'Filetype',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Application]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Application::className(), ['id' => 'application_id']);
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
