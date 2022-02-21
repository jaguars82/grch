<?php

use yii\db\Migration;

/**
 * Class m211115_115109_add_is_euro_and_is_studio_fields_to_flat_table
 */
class m211115_115109_add_is_euro_and_is_studio_fields_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'is_euro', $this->boolean()->defaultValue(false));
        $this->addColumn('flat', 'is_studio', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'is_euro');
        $this->dropColumn('flat', 'is_studio');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_115109_add_is_euro_and_is_stduio_flags_to_newbuilding_table cannot be reverted.\n";

        return false;
    }
    */
}
