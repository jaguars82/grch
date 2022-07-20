<?php

use yii\db\Migration;

/**
 * Class m220720_073943_alter_column_name_in_region_district_table
 */
class m220720_073943_alter_column_name_in_region_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%region_district}}', 'name', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220720_073943_alter_column_name_in_region_district_table cannot be reverted.\n";

        return false;
    }
}
