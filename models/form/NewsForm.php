<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for news data
 */
class NewsForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $title;
    public $detail;
    public $category;
    public $image;
    public $is_image_reset = 0;
    public $search_link;
    public $resume;
    public $files = [];
    public $savedFiles = [];
    public $developerId;
    public $newbuildingComplexes = [];
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['title', 'detail', 'search_link', 'category', 'developerId', 'newbuildingComplexes', 'is_image_reset'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['image', 'files']),
            self::SCENARIO_UPDATE => array_merge($commonFields, ['savedFiles']),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'detail'], 'required'],
            [['category', 'developerId'], 'integer'],
            [['title'], 'string', 'max' => 200],
            [['detail', 'search_link'], 'string'],
            [['files', 'savedFiles', 'newbuildingComplexes', 'search_link'], 'safe'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['search_link'], 'default', 'value' => NULL],
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
            'category' => 'Категория',
            'image' => 'Изображение',
            'search_link' => 'Cсылки на страницу поиска',
            'files' => 'Документ',
            'newbuildingComplexes' => 'Жилой комплекс',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        $this->search_link = preg_replace('/^http[s]?:\/\//', '', $this->search_link);
        
        return $result;
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        if (!empty($this->search_link) && !preg_match('/^http[s]?:\/\//', $this->search_link)) {
            $this->search_link = "http://{$this->search_link}";
        }
        
        $this->image = UploadedFile::getInstance($this, 'image');
        $this->files = UploadedFile::getInstances($this, 'files');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('image');
        $this->processFiles('files', true);

        return true;
    }
}
