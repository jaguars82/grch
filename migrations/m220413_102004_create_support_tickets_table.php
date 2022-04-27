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
            'author_id' => $this->integer()->notNull(),
            'ticket_number' => $this->string(30)->notNull()->unique(),
            'title' => $this->string(255),
            'is_closed' => $this->boolean(),
            'has_unread_messages_from_support' => $this->boolean(),
            'has_unread_messages_from_author' => $this->boolean(),
            'last_enter_by_support' => $this->timestamp()->defaultValue(null),
            'last_enter_by_author' => $this->timestamp()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-support_tickets-author_id}}',
            '{{%support_tickets}}',
            'author_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-support_tickets-author_id}}',
            '{{%support_tickets}}',
            'author_id',
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
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-support_tickets-author_id}}',
            '{{%support_tickets}}'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            '{{%idx-support_tickets-author_id}}',
            '{{%support_tickets}}'
        );

        $this->dropTable('{{%support_tickets}}');
    }
}
