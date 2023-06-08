<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "secondary_category".
 *
 * @property int $id
 * @property int|null $level 1 - root level of category tree, 2 - second level
 * @property int|null $parent_id id of a parent (level 1) category for level 2 items
 * @property string|null $name
 * @property string|null $alias alternative name (e.g. in latin or from outer source (feed))
 *
 * @property SecondaryRoom[] $secondaryRooms
 */
class SecondaryCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'parent_id'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Уровень категории',
            'parent_id' => 'Родитель',
            'name' => 'Название',
            'alias' => 'Альтернативное название',
        ];
    }

    /**
     * Gets query for [[SecondaryRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::className(), ['category_id' => 'id']);
    }

    /**
     * Gets Secondary category by given name
     */
    public static function getCategoryByName($name)
    {
        return static::find()
            ->where(['name' => $name])
            ->orWhere(['alias' => $name])
            ->one();
    }

    /**
     * Gets array represented category tree
     */
    public static function getCategoryTree()
    {
        $categories = self::find()->all();

        $tree = [];

        foreach ($categories as $category) {
            if ($category->level === 1) {
                $parentItem = array('id' => $category->id, 'name' => $category->name, 'subcats' => array());
                $tree[$category->id] = $parentItem;
            }
        }

        foreach ($categories as $subcat) {
            if ($subcat->parent_id !== NULL) {
                $childItem = array('id' => $subcat->id, 'name' => $subcat->name);
                array_push($tree[$subcat->parent_id]['subcats'], $childItem);
            }
        }
        return $tree;
    }

}