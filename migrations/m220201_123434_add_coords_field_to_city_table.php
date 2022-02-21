<?php

use yii\db\Migration;

/**
 * Class m220201_123434_add_coords_field_to_city_table
 */
class m220201_123434_add_coords_field_to_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('city', 'longitude', $this->double());
        $this->addColumn('city', 'latitude', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('city', 'longitude');
        $this->dropColumn('city', 'latitude');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_123434_add_coords_field_to_city_table cannot be reverted.\n";

        return false;
    }
    */
}
