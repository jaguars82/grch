<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m220905_080153_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'initiator_id' => $this->integer()->notNull()->comment('id of a user who has triggered some event (subject of notification)'),
            'type' => $this->integer(1)->defaultValue(1)->comment('is this an individual (1) or a group (2) notification'),
            'recipient_group' => $this->string(25)->comment('name of user group the notification is for (if it is a group notification)'),
            'recipient_id' => $this->integer()->comment('id of user the notification is for (if it is an andividual notification)'),
            'topic' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'body' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'initiator_comment' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'action_text' => $this->string()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'action_url' => $this->string()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'seen_by_recipient' => $this->integer(1)->defaultValue(0),
            'seen_by_recipient_at' => $this->timestamp()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-notification-initiator_id',
            '{{%notification}}',
            'initiator_id'
        );

        $this->addForeignKey(
            '{{%fk-notification-initiator_id}}',
            '{{%notification}}',
            'initiator_id',
            '{{%user}}',
            'id',
            'NO ACTION'
        );

        $this->createIndex(
            'idx-notification-recipient_id',
            '{{%notification}}',
            'recipient_id'
        );

        $this->addForeignKey(
            '{{%fk-notification-recipient_id}}',
            '{{%notification}}',
            'recipient_id',
            '{{%user}}',
            'id',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-notification-recipient_id}}',
            '{{%notification}}',
        );

        $this->dropIndex(
            'idx-notification-recipient_id',
            '{{%notification}}',
        );

        $this->dropForeignKey(
            '{{%fk-notification-initiator_id}}',
            '{{%notification}}',
        );

        $this->dropIndex(
            'idx-notification-initiator_id',
            '{{%notification}}',
        );

        $this->dropTable('{{%notification}}');
    }
}
