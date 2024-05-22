<?php

use yii\db\Migration;

/**
 * Class m240521_160224_add_number_appendix_and_number_string_fields_to_flat_table
 */
class m240521_160224_add_number_appendix_and_number_string_fields_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'number_string', $this->string(155)->after('number')->defaultValue(NULL)->comment('flat number if it consists not only digits'));
        $this->addColumn('{{%flat}}', 'number_appendix', $this->string(155)->after('number_string')->defaultValue(NULL)->comment('the non-digital part of flat\'s number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flat}}', 'number_string');
        $this->dropColumn('{{%flat}}', 'number_appendix');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240521_160224_add_number_appendix_and_number_string_fields_to_flat_table cannot be reverted.\n";

        return false;
    }
    */
}
