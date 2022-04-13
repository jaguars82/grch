<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%support_tickets}}`.
 */
class m220413_102004_create_support_tickets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%support_tickets}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer->notNull(),
            'ticket_number' => $this->string(30)->notNull()->unique(),
            'title' => $this->string(255),
            'is_closed' => $this->boolean(),
            'has_unread_messages_from_support' => $this->boolean(),
            'has_unread_messages_from_author' => $this->boolean(),
            'last_enter_by_support' => $this->timestamp(),
            'last_enter_by_author' => $this->timestamp(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%support_tickets}}');
    }
}
