<?php

use yii\db\Migration;

/**
 * Class m221012_184017_alter_column_author_role_in_support_message_table
 */
class m221012_184017_alter_column_author_role_in_support_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('support_messages', 'author_role', $this->string(25));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221012_184017_alter_column_author_role_in_support_message_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221012_184017_alter_column_author_role_in_support_message_table cannot be reverted.\n";

        return false;
    }
    */
}
