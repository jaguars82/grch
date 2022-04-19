<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%support_messages}}`.
 */
class m220419_061543_create_support_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%support_messages}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'message_number' => $this->integer()->notNull(),
            'reply_on' => $this->integer()->defaultValue(null), // id of linked message
            'author_role' => $this->string(15)->notNull(), // admin, agent etc.
            'seen_by_interlocutor' => $this->boolean(),
            'text' => $this->text(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),        
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-support_messages-ticket_id}}',
            '{{%support_messages}}',
            'ticket_id'
        );
        
        // add foreign key for table `{{%support_tickets}}`
        $this->addForeignKey(
            '{{%fk-support_messages-ticket_id}}',
            '{{%support_messages}}',
            'ticket_id',
            '{{%support_tickets}}',
            'id',
            'CASCADE'
        );        

        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-support_messages-author_id}}',
            '{{%support_messages}}',
            'author_id'
        );
        
        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-support_messages-author_id}}',
            '{{%support_messages}}',
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
            '{{%fk-support_messages-author_id}}',
            '{{%support_messages}}'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            '{{%idx-support_messages-author_id}}',
            '{{%support_messages}}'
        );
        
        // drops foreign key for table `{{%support_tickets}}`
        $this->dropForeignKey(
            '{{%fk-support_messages-ticket_id}}',
            '{{%support_messages}}'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            '{{%idx-support_messages-ticket_id}}',
            '{{%support_messages}}'
        );
        
        $this->dropTable('{{%support_messages}}');
    }
}
