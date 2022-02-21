<?php

use yii\db\Migration;

/**
 * Class m211109_135511_change_field_type_building_number_column_in_newbuilding_complex_and_newbuilding_tables
 */
class m211109_135511_change_field_type_building_number_column_in_newbuilding_complex_and_newbuilding_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('newbuilding_complex', 'building_number', $this->string(20));
        $this->alterColumn('newbuilding', 'building_number', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('newbuilding_complex', 'building_number', $this->integer(20));
        $this->alterColumn('newbuilding', 'building_number', $this->integer(20));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211109_135511_change_field_type_building_number_column_in_newbuilding_complex_and_newbuilding_tables cannot be reverted.\n";

        return false;
    }
    */
}