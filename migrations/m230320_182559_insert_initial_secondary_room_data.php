<?php

use yii\db\Migration;

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
            'id' => 1,
            'level' => 1,
            'name' => 'Жилая недвижимость',
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 2,
            'level' => 1,
            'name' => 'Дома, дачи, коттеджи',
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 3,
            'level' => 1,
            'name' => 'Земельные участки',
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 4,
            'level' => 1,
            'name' => 'Коммерческая недвижимость',
        ]);

        $this->batchInsert('{{%secondary_category}}', ['id', 'level', 'parent_id', 'name', 'alias'], [
            [5, 2, 1, 'Квартира в новостройке', ''],
            [6, 2, 1, 'Квартира', ''],
            [7, 2, 1, 'Комната', ''],
            [8, 2, 2, 'Дом', ''],
            [9, 2, 2, 'Часть дома', ''],
            [10, 2, 2, 'Дача', ''],
            [11, 2, 2, 'Коттедж', ''],
            [12, 2, 2, 'Таунхаус', ''],
            [13, 2, 3, 'Поселений (ИЖС)', 'участок'],
            [14, 2, 3, 'Сельхозназначения (СНТ, ДНП)', ''],
            [15, 2, 4, 'Офисное помещение', ''],
            [16, 2, 4, 'Помещение общественного питания', ''],
            [17, 2, 4, 'Помещение свободного назначения', ''],
            [18, 2, 4, 'Производственное помещение', ''],
            [19, 2, 4, 'Складское помещение', ''],
            [20, 2, 4, 'Торговое помещение', ''],
            [21, 2, 4, 'Коммерческая (без уточнения)', 'коммерческая'],
        ]);

        $this->batchInsert('{{%secondary_property_type}}', ['id', 'name', 'alias'], [
            ['1', 'жилая', ''],
            ['2', 'коммерческая', ''],
        ]);

        $this->batchInsert('{{%secondary_renovation}}', ['id', 'name', 'alias', 'detail'], [
            ['1', 'евроремонт', '', ''],
            ['2', 'дизайнерский', '', ''],
            ['3', 'косметический', '', ''],
            ['4', 'требует ремонта', '', ''],
            ['5', 'черновая отделка', '', ''],
            ['6', 'чистовая отделка', '', ''],
            ['7', 'без отделки', '', ''],
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
        $this->delete('{{%secondary_category}}', ['id' => 1]);
        $this->delete('{{%secondary_category}}', ['id' => 2]);
        $this->delete('{{%secondary_category}}', ['id' => 3]);
        $this->delete('{{%secondary_category}}', ['id' => 4]);
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
