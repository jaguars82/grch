<?php

use yii\db\Migration;

/**
 * Class m220121_112659_add_contacts_fields_to_developer
 */
class m220121_112659_add_contacts_fields_to_developer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('developer', 'phone', $this->string());
        $this->addColumn('developer', 'url', $this->string());
        $this->addColumn('developer', 'email', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('developer', 'phone');
        $this->dropColumn('developer', 'url');
        $this->dropColumn('developer', 'email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_112659_add_contacts_fields_to_developer cannot be reverted.\n";

        return false;
    }
    */
}
