<?php

use yii\db\Migration;

/**
 * Class m211019_083646_add_references_to_newbuilding_complex
 */
class m211019_083646_add_references_to_newbuilding_complex extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newbuilding_complex', 'active', $this->boolean());
        $this->addColumn('newbuilding_complex', 'region_id', $this->integer());
        $this->addColumn('newbuilding_complex', 'city_id', $this->integer());
        $this->addColumn('newbuilding_complex', 'district_id', $this->integer());
        $this->addColumn('newbuilding_complex', 'street_type_id', $this->integer());
        $this->addColumn('newbuilding_complex', 'street_name', $this->string(200));
        $this->addColumn('newbuilding_complex', 'building_type_id', $this->integer());
        $this->addColumn('newbuilding_complex', 'building_number', $this->integer());
        $this->addColumn('newbuilding_complex', 'master_plan', $this->string(200)->unique());

        $this->createTable('newbuilding_complex_advantage', [
            'newbuilding_complex_id' => $this->integer(),
            'advantage_id' => $this->integer()
        ]);

        $this->createTable('newbuilding_complex_image', [
            'image_id' => $this->primaryKey(),
            'newbuilding_complex_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newbuilding_complex', 'active');
        $this->dropColumn('newbuilding_complex', 'region_id');
        $this->dropColumn('newbuilding_complex', 'city_id');
        $this->dropColumn('newbuilding_complex', 'district_id');
        $this->dropColumn('newbuilding_complex', 'street_type_id');
        $this->dropColumn('newbuilding_complex', 'street_name');
        $this->dropColumn('newbuilding_complex', 'building_type_id');
        $this->dropColumn('newbuilding_complex', 'building_number');
        $this->dropColumn('newbuilding_complex', 'master_plan');

        $this->dropTable('newbuilding_complex_advantage');

        $this->dropTable('newbuilding_complex_image');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_083646_add_references_to_newbuilding_complex cannot be reverted.\n";

        return false;
    }
    */
}
