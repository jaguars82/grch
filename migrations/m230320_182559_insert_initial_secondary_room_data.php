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
            'name' => iconv('ASCII', 'UTF-8//IGNORE', 'Жилая недвижимость'),
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 2,
            'level' => 1,
            'name' => iconv('ASCII', 'UTF-8//IGNORE', 'Дома, дачи, коттеджи'),
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 3,
            'level' => 1,
            'name' => iconv('ASCII', 'UTF-8//IGNORE', 'Земельные участки'),
        ]);
        $this->insert('{{%secondary_category}}', [
            'id' => 4,
            'level' => 1,
            'name' => iconv('ASCII', 'UTF-8//IGNORE', 'Коммерческая недвижимость'),
        ]);

        $this->batchInsert('{{%secondary_category}}', ['id', 'level', 'parent_id', 'name', 'alias'], [
            [5, 2, 1, iconv('ASCII', 'UTF-8//IGNORE', 'Квартира в новостройке'), ''],
            [6, 2, 1, iconv('ASCII', 'UTF-8//IGNORE', 'Квартира'), ''],
            [7, 2, 1, iconv('ASCII', 'UTF-8//IGNORE', 'Комната'), ''],
            [8, 2, 2, iconv('ASCII', 'UTF-8//IGNORE', 'Дом'), ''],
            [9, 2, 2, iconv('ASCII', 'UTF-8//IGNORE', 'Дача'), ''],
            [10, 2, 2, iconv('ASCII', 'UTF-8//IGNORE', 'Коттедж'), ''],
            [11, 2, 2, iconv('ASCII', 'UTF-8//IGNORE', 'Таунхаус'), ''],
            [12, 2, 3, iconv('ASCII', 'UTF-8//IGNORE', 'Поселений (ИЖС)'), ''],
            [13, 2, 3, iconv('ASCII', 'UTF-8//IGNORE', 'Сельхозназначения (СНТ, ДНП)'), ''],
            [14, 2, 3, iconv('ASCII', 'UTF-8//IGNORE', 'Офисное помещение'), ''],
            [15, 2, 4, iconv('ASCII', 'UTF-8//IGNORE', 'Помещение общественного питания'), ''],
            [16, 2, 4, iconv('ASCII', 'UTF-8//IGNORE', 'Помещение свободного назначения'), ''],
            [17, 2, 4, iconv('ASCII', 'UTF-8//IGNORE', 'Производственное помещение'), ''],
            [18, 2, 4, iconv('ASCII', 'UTF-8//IGNORE', 'Складское помещение'), ''],
            [19, 2, 4, iconv('ASCII', 'UTF-8//IGNORE', 'Торговое помещение'), ''],
        ]);

        $this->batchInsert('{{%secondary_property_type}}', ['id', 'name', 'alias'], [
            ['1', iconv('ASCII', 'UTF-8//IGNORE', 'жилая'), ''],
        ]);

        $this->batchInsert('{{%secondary_renovation}}', ['id', 'name', 'alias', 'detail'], [
            ['1', iconv('ASCII', 'UTF-8//IGNORE', 'евроремонт'), '', ''],
            ['2', iconv('ASCII', 'UTF-8//IGNORE', 'дизайнерский'), '', ''],
            ['3', iconv('ASCII', 'UTF-8//IGNORE', 'косметический'), '', ''],
            ['4', iconv('ASCII', 'UTF-8//IGNORE', 'требует ремонта'), '', ''],
        ]);

        $this->batchInsert('{{%secondary_building_series}}', ['id', 'name', 'alias', 'detail'], [
            ['1', iconv('ASCII', 'UTF-8//IGNORE', 'Современной планировки'), iconv('ASCII', 'UTF-8//IGNORE', 'Современной пл.'), ''],
            ['2', iconv('ASCII', 'UTF-8//IGNORE', 'Улучшенной планировки'), iconv('ASCII', 'UTF-8//IGNORE', 'Улучшенной пл.'), ''],
            ['3', iconv('ASCII', 'UTF-8//IGNORE', 'Сталинка'), '', ''],
            ['4', iconv('ASCII', 'UTF-8//IGNORE', 'Брежневка'), '', ''],
            ['5', iconv('ASCII', 'UTF-8//IGNORE', 'Чешка'), '', ''],
            ['6', iconv('ASCII', 'UTF-8//IGNORE', 'Хрущевка'), '', ''],
            ['7', iconv('ASCII', 'UTF-8//IGNORE', 'Общежитие'), '', ''],
            ['8', iconv('ASCII', 'UTF-8//IGNORE', 'Коммуналка'), '', ''],
            ['9', iconv('ASCII', 'UTF-8//IGNORE', 'ЗГТ'), '', ''],
            ['10', iconv('ASCII', 'UTF-8//IGNORE', 'Малосемейка'), '', ''],
            ['11', iconv('ASCII', 'UTF-8//IGNORE', 'Старый фонд'), '', ''],
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

        $this->delete('{{%secondary_property_type}}', ['id' => 1]);
       
        $this->delete('{{%secondary_renovation}}', ['id' => 1]);
        $this->delete('{{%secondary_renovation}}', ['id' => 2]);
        $this->delete('{{%secondary_renovation}}', ['id' => 3]);
        $this->delete('{{%secondary_renovation}}', ['id' => 4]);

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
