<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%application_history}}`.
 */
class m220823_100255_create_application_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%application_history}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'action' => $this->integer(2)->notNull(),
            'reason' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'comment' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'made_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-application_history-application_id',
            '{{%application_history}}',
            'application_id'
        );

        $this->addForeignKey(
            '{{%fk-application_history-application_id}}',
            '{{%application_history}}',
            'application_id',
            '{{%application}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-application_history-user_id',
            '{{%application_history}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-application_history-user_id}}',
            '{{%application_history}}',
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
        $this->dropForeignKey(
            '{{%fk-application_history-application_id}}',
            '{{%application_history}}',
        );

        $this->dropIndex(
            'idx-application_history-application_id',
            '{{%application_history}}',
        );

        $this->dropForeignKey(
            '{{%fk-application_history-user_id}}',
            '{{%application_history}}',
        );

        $this->dropIndex(
            'idx-application_history-user_id',
            '{{%application_history}}',
        );

        $this->dropTable('{{%application_history}}');
    }
}
