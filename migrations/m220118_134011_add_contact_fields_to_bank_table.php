<?php

use yii\db\Migration;

/**
 * Class m220118_134011_add_contact_fields_to_bank_table
 */
class m220118_134011_add_contact_fields_to_bank_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bank', 'address', $this->string());
        $this->addColumn('bank', 'longitude', $this->float());
        $this->addColumn('bank', 'latitude', $this->float());
        $this->addColumn('bank', 'phone', $this->string());
        $this->addColumn('bank', 'url', $this->string());
        $this->addColumn('bank', 'email', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('bank', 'address');
        $this->dropColumn('bank', 'longitude');
        $this->dropColumn('bank', 'latitude');
        $this->dropColumn('bank', 'phone');
        $this->dropColumn('bank', 'url');
        $this->dropColumn('bank', 'email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220118_134011_add_contact_fields_to_bank_table cannot be reverted.\n";

        return false;
    }
    */
}
