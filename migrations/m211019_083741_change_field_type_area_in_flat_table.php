<?php

use yii\db\Migration;

/**
 * Class m211019_083741_change_field_type_area_in_flat_table
 */
class m211019_083741_change_field_type_area_in_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE flat MODIFY area float(10, 2) NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('flat', 'area', $this->float()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_083741_change_field_type_area_in_flat_table cannot be reverted.\n";

        return false;
    }
    */
}
