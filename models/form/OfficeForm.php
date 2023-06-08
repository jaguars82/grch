<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for contact's data
 */
class OfficeForm extends Model 
{
    use FillAttributes;

    public $name;
    public $address;
    public $comment;
    public $phones = [];
    public $developer_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phones'], 'safe'],
            [['phones'], 'validatePhones', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['name', 'address'], 'required'],
            [['name', 'address'], 'string', 'max' => 200],
            [['comment'], 'string'],
            [['developer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Developer::className(), 'targetAttribute' => ['developer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'address' => 'Адрес',
            'phones' => 'Номер телефона',
            'comment' => 'Комментарий',
        ];
    }

    public function validatePhones($attribute)
    {
        if(is_array($this->phones)) {
            foreach($this->phones as $key => $phone) {
                if(empty($phone['value'])) {
                    $this->addError($attribute . "[$key][value]", "Поле 'Номер телефона' не может быть пустым");
                } elseif(!preg_match('/^\+7\s\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', $phone['value'])) {
                    $this->addError($attribute . "[$key][value]", "Введите корректный номер телефона");
                }

                if(empty($phone['owner'])) {
                    $this->addError($attribute . "[$key][owner]", "Поле 'Владелец' не может быть пустым");
                }
            }
        }
    }
}