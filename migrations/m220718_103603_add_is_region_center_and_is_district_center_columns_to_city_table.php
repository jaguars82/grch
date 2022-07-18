<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%city}}`.
 */
class m220718_103603_add_is_region_center_and_is_district_center_columns_to_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'is_region_center', $this->smallInteger(2)->after('region_district_id')->defaultValue(0)->notNull());
        $this->addColumn('{{%city}}', 'is_district_center', $this->smallInteger(2)->after('is_region_center')->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%city}}', 'is_district_center');
        $this->dropColumn('{{%city}}', 'is_region_center');
    }
}
