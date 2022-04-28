<?php

use yii\db\Migration;

/**
 * Class m220428_101918_change_support_tickets_table
 */
class m220428_101918_change_support_tickets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('support_tickets', 'ticket_number', $this->string(30)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'));
        $this->alterColumn('support_tickets', 'title', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220428_101918_change_support_tickets_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220428_101918_change_support_tickets_table cannot be reverted.\n";

        return false;
    }
    */
}
