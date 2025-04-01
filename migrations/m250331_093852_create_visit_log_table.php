<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%visit_log}}`.
 */
class m250331_093852_create_visit_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%visit_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(null),
            'route' => $this->string(255)->notNull(),
            'request_params' => $this->text()->defaultValue(null),
            'is_login' => $this->boolean()->notNull()->defaultValue(0),
            'visited_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip_address' => $this->string(45)->defaultValue(null),
            'user_agent' => $this->text()->defaultValue(null),
            'device_type' =>
                $this->db->driverName === 'mysql' 
                    ? "ENUM('desktop', 'mobile') NOT NULL" 
                    : $this->string(10)->notNull(),
            'referrer' => $this->string(2048)->defaultValue(null),
        ]);

        $this->createIndex('idx-visit_log-user_id', '{{%visit_log}}', 'user_id');

        $this->addForeignKey(
            'fk-visit_log-user_id',
            '{{%visit_log}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-visit_log-user_id', '{{%visit_log}}');
        $this->dropIndex('idx-visit_log-user_id', '{{%visit_log}}');
        $this->dropTable('{{%visit_log}}');
    }
}