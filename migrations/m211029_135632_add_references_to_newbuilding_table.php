<?php

use yii\db\Migration;

/**
 * Class m211029_135632_add_references_to_newbuilding_table
 */
class m211029_135632_add_references_to_newbuilding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newbuilding', 'region_id', $this->integer());
        $this->addColumn('newbuilding', 'city_id', $this->integer());
        $this->addColumn('newbuilding', 'district_id', $this->integer());
        $this->addColumn('newbuilding', 'street_type_id', $this->integer());
        $this->addColumn('newbuilding', 'street_name', $this->string(200));
        $this->addColumn('newbuilding', 'building_number', $this->integer(20));
        $this->addColumn('newbuilding', 'building_type_id', $this->integer());

        $this->createTable('newbuilding_advantage', [
            'newbuilding_id' => $this->integer(),
            'advantage_id' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newbuilding', 'region_id');
        $this->dropColumn('newbuilding', 'city_id');
        $this->dropColumn('newbuilding', 'district_id');
        $this->dropColumn('newbuilding', 'street_type_id');
        $this->dropColumn('newbuilding', 'street_name');
        $this->dropColumn('newbuilding', 'building_number');
        $this->dropColumn('newbuilding', 'building_type_id');

        $this->dropTable('newbuilding_advantage');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211029_135632_add_references_to_newbuilding_table cannot be reverted.\n";

        return false;
    }
    */
}
