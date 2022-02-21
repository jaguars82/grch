<?php

use yii\db\Migration;

/**
 * Class m220118_134318_add_contact_fields_to_agency_table
 */
class m220118_134318_add_contact_fields_to_agency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agency', 'phone', $this->string());
        $this->addColumn('agency', 'url', $this->string());
        $this->addColumn('agency', 'email', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agency', 'phone');
        $this->dropColumn('agency', 'url');
        $this->dropColumn('agency', 'email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220118_134318_add_contact_fields_to_agency_table cannot be reverted.\n";

        return false;
    }
    */
}
