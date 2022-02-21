<?php

use yii\db\Migration;

/**
 * Class m211108_164305_link_region_table_on_city_table
 */
class m211108_164305_link_region_table_on_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('city', 'region_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('city', 'region_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211108_164305_link_region_table_on_city_table cannot be reverted.\n";

        return false;
    }
    */
}
