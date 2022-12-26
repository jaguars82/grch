<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m221223_131935_add_last_ip_and_current_auth_token_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'last_ip', $this->string(20)->defaultValue(null)->after('last_login'));
        $this->addColumn('{{%user}}', 'current_auth_token', $this->string(32)->defaultValue(null)->after('auth_key')->comment('random string generated while user login'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'current_auth_token');
        $this->dropColumn('{{%user}}', 'last_ip');
    }
}
