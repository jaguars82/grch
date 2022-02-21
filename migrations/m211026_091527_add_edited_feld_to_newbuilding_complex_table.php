<?php

use yii\db\Migration;

/**
 * Class m211026_091527_add_edited_feld_to_newbuilding_complex_table
 */
class m211026_091527_add_edited_feld_to_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('developer', 'edited_fields', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('developer', 'edited_fields');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }
_
    public function down()
    {
        echo "m211026_091527_add_edited_feld_to_newbuilding_complex_table cannot be reverted.\n";

        return false;
    }
    */
}
