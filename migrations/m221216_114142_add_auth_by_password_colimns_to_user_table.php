<?php

use yii\db\Migration;

/**
 * Class m221216_114142_add_auth_by_password_colimns_to_user_table
 */
class m221216_114142_add_auth_by_password_colimns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_hash', $this->string()->defaultValue(null)->after('email'));
        $this->addColumn('{{%user}}', 'password_reset_token', $this->string()->unique());
        $this->addColumn('{{%user}}', 'last_login', $this->timestamp()->defaultValue(null));
        $this->addColumn('{{%user}}', 'passauth_enabled', $this->integer(1)->after('password_hash')->defaultValue(0)->notNull()->comment('flag shows if authorization by login and password has been enabled for the user'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'passauth_enabled');
        $this->dropColumn('{{%user}}', 'last_login');
        $this->dropColumn('{{%user}}', 'password_reset_token');
        $this->dropColumn('{{%user}}', 'password_hash');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221216_114142_add_auth_by_password_colimns_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
