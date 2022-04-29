<?php

use yii\db\Migration;

/**
 * Class m220428_102706_change_support_messages_table
 */
class m220428_102706_change_support_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('support_messages', 'author_role', $this->string(15)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'));
        $this->alterColumn('support_messages', 'text', $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220428_102706_change_support_messages_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220428_102706_change_support_messages_table cannot be reverted.\n";

        return false;
    }
    */
}
