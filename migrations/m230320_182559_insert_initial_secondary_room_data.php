<?php

use yii\db\Migration;
use app\models\SecondaryCategory;

/**
 * Class m230320_182559_insert_initial_secondary_room_data
 */
class m230320_182559_insert_initial_secondary_room_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%secondary_category}}', [
            'id' => SecondaryCategory::ROOT_CATEGORY_FLAT,
            'level' => 1,
            'name' => SecondaryCategory::$root_categories[ROOT_CATEGORY_FLAT],
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => SecondaryCategory::ROOT_CATEGORY_HOUSE,
            'level' => 1,
            'name' => SecondaryCategory::$root_categories[ROOT_CATEGORY_HOUSE],
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => SecondaryCategory::ROOT_CATEGORY_PLOT,
            'level' => 1,
            'name' => SecondaryCategory::$root_categories[ROOT_CATEGORY_PLOT],
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => SecondaryCategory::ROOT_CATEGORY_COMMERCIAL,
            'level' => 1,
            'name' => SecondaryCategory::$root_categories[ROOT_CATEGORY_COMMERCIAL],
        ]);

        $this->batchInsert('{{%secondary_category}}', ['id', 'level', 'parent_id', 'name', 'alias'], [
            [5, 2, SecondaryCategory::ROOT_CATEGORY_FLAT, 'Квартира в новостройке', ''],
            [6, 2, SecondaryCategory::ROOT_CATEGORY_FLAT, 'Квартира', ''],
            [7, 2, SecondaryCategory::ROOT_CATEGORY_FLAT, 'Комната', ''],
            [8, 2, SecondaryCategory::ROOT_CATEGORY_HOUSE, 'Дом', 'дом с участком'],
            [9, 2, SecondaryCategory::ROOT_CATEGORY_HOUSE, 'Часть дома', ''],
            [10, 2, SecondaryCategory::ROOT_CATEGORY_HOUSE, 'Дача', ''],
            [11, 2, SecondaryCategory::ROOT_CATEGORY_HOUSE, 'Коттедж', ''],
            [12, 2, SecondaryCategory::ROOT_CATEGORY_HOUSE, 'Таунхаус', ''],
            [13, 2, SecondaryCategory::ROOT_CATEGORY_PLOT, 'Поселений (ИЖС)', 'участок'],
            [14, 2, SecondaryCategory::ROOT_CATEGORY_PLOT, 'Сельхозназначения (СНТ, ДНП)', ''],
            [15, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Офисное помещение', ''],
            [16, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Помещение общественного питания', ''],
            [17, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Помещение свободного назначения', ''],
            [18, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Производственное помещение', ''],
            [19, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Складское помещение', ''],
            [20, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Торговое помещение', ''],
            [21, 2, SecondaryCategory::ROOT_CATEGORY_COMMERCIAL, 'Коммерческая (без уточнения)', 'коммерческая'],
        ]);

        $this->batchInsert('{{%secondary_property_type}}', ['id', 'name', 'alias'], [
            ['1', 'жилая', ''],
            ['2', 'коммерческая', ''],
        ]);

        $this->batchInsert('{{%secondary_renovation}}', ['id', 'name', 'alias', 'detail'], [
            ['1', 'евроремонт', 'евро', ''],
            ['2', 'дизайнерский', '', ''],
            ['3', 'косметический', '', ''],
            ['4', 'требует ремонта', '', ''],
            ['5', 'черновая отделка', '', ''],
            ['6', 'чистовая отделка', 'чистовая в новостройке', ''],
            ['7', 'без отделки', '', ''],
            ['8', 'старый ремонт', '', ''],
            ['9', 'стяжка-штукатурка', '', ''],
            ['10', 'white box', '', ''],
        ]);

        $this->batchInsert('{{%secondary_building_series}}', ['id', 'name', 'alias', 'detail'], [
            ['1', 'Современной планировки', 'Современной пл.', ''],
            ['2', 'Улучшенной планировки', 'Улучшенной пл.', ''],
            ['3', 'Сталинка', '', ''],
            ['4', 'Брежневка', '', ''],
            ['5', 'Чешка', '', ''],
            ['6', 'Хрущевка', '', ''],
            ['7', 'Общежитие', '', ''],
            ['8', 'Коммуналка', '', ''],
            ['9', 'ЗГТ', '', ''],
            ['10', 'Малосемейка', '', ''],
            ['11', 'Старый фонд', '', ''],
        ]);

        $this->batchInsert('{{%building_material}}', ['id', 'name', 'alias', 'detail'], [
            ['1', 'блочный', '', ''],
            ['2', 'бревно', '', ''],
            ['3', 'брус', '', ''],
            ['4', 'деревянный', '', ''],
            ['5', 'каркасно-засыпной', 'Карк. засып.', ''],
            ['6', 'кирпично-монолитный', '', ''],
            ['7', 'кирпичный', '', ''],
            ['8', 'комбинированный', '', ''],
            ['9', 'металл', '', ''],
            ['10', 'монолит', '', ''],
            ['11', 'монолитно-блочный', '', ''],
            ['12', 'панельный', '', ''],
            ['13', 'пеноблок', '', ''],
            ['14', 'шлакоблочный', '', ''],
            ['15', 'щитовой', '', ''],
            ['16', 'газосиликатный блок', '', ''],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%secondary_category}}', ['id' => SecondaryCategory::ROOT_CATEGORY_FLAT]);
        $this->delete('{{%secondary_category}}', ['id' => SecondaryCategory::ROOT_CATEGORY_HOUSE]);
        $this->delete('{{%secondary_category}}', ['id' => SecondaryCategory::ROOT_CATEGORY_PLOT]);
        $this->delete('{{%secondary_category}}', ['id' => SecondaryCategory::ROOT_CATEGORY_COMMERCIAL]);
        $this->delete('{{%secondary_category}}', ['id' => 5]);
        $this->delete('{{%secondary_category}}', ['id' => 6]);
        $this->delete('{{%secondary_category}}', ['id' => 7]);
        $this->delete('{{%secondary_category}}', ['id' => 8]);
        $this->delete('{{%secondary_category}}', ['id' => 9]);
        $this->delete('{{%secondary_category}}', ['id' => 10]);
        $this->delete('{{%secondary_category}}', ['id' => 11]);
        $this->delete('{{%secondary_category}}', ['id' => 12]);
        $this->delete('{{%secondary_category}}', ['id' => 13]);
        $this->delete('{{%secondary_category}}', ['id' => 14]);
        $this->delete('{{%secondary_category}}', ['id' => 15]);
        $this->delete('{{%secondary_category}}', ['id' => 16]);
        $this->delete('{{%secondary_category}}', ['id' => 17]);
        $this->delete('{{%secondary_category}}', ['id' => 18]);
        $this->delete('{{%secondary_category}}', ['id' => 19]);
        $this->delete('{{%secondary_category}}', ['id' => 20]);
        $this->delete('{{%secondary_category}}', ['id' => 21]);

        $this->delete('{{%secondary_property_type}}', ['id' => 1]);
        $this->delete('{{%secondary_property_type}}', ['id' => 2]);
       
        $this->delete('{{%secondary_renovation}}', ['id' => 1]);
        $this->delete('{{%secondary_renovation}}', ['id' => 2]);
        $this->delete('{{%secondary_renovation}}', ['id' => 3]);
        $this->delete('{{%secondary_renovation}}', ['id' => 4]);
        $this->delete('{{%secondary_renovation}}', ['id' => 5]);
        $this->delete('{{%secondary_renovation}}', ['id' => 6]);
        $this->delete('{{%secondary_renovation}}', ['id' => 7]);
        $this->delete('{{%secondary_renovation}}', ['id' => 8]);
        $this->delete('{{%secondary_renovation}}', ['id' => 9]);
        $this->delete('{{%secondary_renovation}}', ['id' => 10]);

        $this->delete('{{%secondary_building_series}}', ['id' => 1]);
        $this->delete('{{%secondary_building_series}}', ['id' => 2]);
        $this->delete('{{%secondary_building_series}}', ['id' => 3]);
        $this->delete('{{%secondary_building_series}}', ['id' => 4]);
        $this->delete('{{%secondary_building_series}}', ['id' => 5]);
        $this->delete('{{%secondary_building_series}}', ['id' => 6]);
        $this->delete('{{%secondary_building_series}}', ['id' => 7]);
        $this->delete('{{%secondary_building_series}}', ['id' => 8]);
        $this->delete('{{%secondary_building_series}}', ['id' => 9]);
        $this->delete('{{%secondary_building_series}}', ['id' => 10]);
        $this->delete('{{%secondary_building_series}}', ['id' => 11]);

        $this->delete('{{%building_material}}', ['id' => 1]);
        $this->delete('{{%building_material}}', ['id' => 2]);
        $this->delete('{{%building_material}}', ['id' => 3]);
        $this->delete('{{%building_material}}', ['id' => 4]);
        $this->delete('{{%building_material}}', ['id' => 5]);
        $this->delete('{{%building_material}}', ['id' => 6]);
        $this->delete('{{%building_material}}', ['id' => 7]);
        $this->delete('{{%building_material}}', ['id' => 8]);
        $this->delete('{{%building_material}}', ['id' => 9]);
        $this->delete('{{%building_material}}', ['id' => 10]);
        $this->delete('{{%building_material}}', ['id' => 11]);
        $this->delete('{{%building_material}}', ['id' => 12]);
        $this->delete('{{%building_material}}', ['id' => 13]);
        $this->delete('{{%building_material}}', ['id' => 14]);
        $this->delete('{{%building_material}}', ['id' => 15]);
        $this->delete('{{%building_material}}', ['id' => 16]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230320_182559_insert_initial_secondary_room_data cannot be reverted.\n";

        return false;
    }
    */
}
