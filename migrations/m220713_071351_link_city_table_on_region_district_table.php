<?php

use yii\db\Migration;

/**
 * Class m220713_071351_link_city_table_on_region_district_table
 */
class m220713_071351_link_city_table_on_region_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'region_district_id', $this->integer()->after('region_id'));

        $this->createIndex(
            'idx-city-region_district_id',
            '{{%city}}',
            'region_district_id'
        );

        $this->addForeignKey(
            '{{%fk-city-region_district_id}}',
            '{{%city}}',
            'region_district_id',
            '{{%region_district}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-city-region_district_id}}',
            '{{%city}}',
        );

        $this->dropIndex(
            'idx-city-region_district_id',
            '{{%city}}',
        );

        $this->dropColumn('{{%city}}', 'region_district_id');
    }

}
