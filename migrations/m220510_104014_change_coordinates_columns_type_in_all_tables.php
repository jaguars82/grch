<?php

use yii\db\Migration;

/**
 * Class m220510_104014_change_coordinates_columns_type_in_all_tables
 */
class m220510_104014_change_coordinates_columns_type_in_all_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('agency', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('agency', 'latitude', $this->decimal(8, 6));
        $this->alterColumn('bank', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('bank', 'latitude', $this->decimal(8, 6));
        $this->alterColumn('city', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('city', 'latitude', $this->decimal(8, 6));
        $this->alterColumn('developer', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('developer', 'latitude', $this->decimal(8, 6));
        $this->alterColumn('newbuilding', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('newbuilding', 'latitude', $this->decimal(8, 6));
        $this->alterColumn('newbuilding_complex', 'longitude', $this->decimal(8, 6));
        $this->alterColumn('newbuilding_complex', 'latitude', $this->decimal(8, 6));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('agency', 'longitude', $this->float(6, 4));
        $this->alterColumn('agency', 'latitude', $this->float(6, 4));
        $this->alterColumn('bank', 'longitude', $this->float(6, 4));
        $this->alterColumn('bank', 'latitude', $this->float(6, 4));
        $this->alterColumn('city', 'longitude', $this->double(8, 6));
        $this->alterColumn('city', 'latitude', $this->double(8, 6));
        $this->alterColumn('developer', 'longitude', $this->float(6, 4));
        $this->alterColumn('developer', 'latitude', $this->float(6, 4));
        $this->alterColumn('newbuilding', 'longitude', $this->float(6, 4));
        $this->alterColumn('newbuilding', 'latitude', $this->float(6, 4));
        $this->alterColumn('newbuilding_complex', 'longitude', $this->float(6, 4));
        $this->alterColumn('newbuilding_complex', 'latitude', $this->float(6, 4));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_104014_change_coordinates_columns_type_in_all_tables cannot be reverted.\n";

        return false;
    }
    */
}
