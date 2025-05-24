<?php

use yii\db\Migration;

/**
 * Class m250517_145505_create_messenger_tables
 */
class m250517_145505_create_messenger_tables extends Migration
{
    private string $connection = 'messenger_db';

    public function safeUp()
    {
        $this->db = Yii::$app->get($this->connection);

        // Creating table 'chat'
        $this->createTable('{{%chat}}', [
            'id' => $this->primaryKey(),
            'creator_id' => $this->integer()->notNull(),
            'interlocuter_id' => $this->integer(),
            'icon' => $this->string(),
            'title' => $this->string(),
            'details' => $this->text(),
            'type' => $this->string(20)->notNull()->defaultValue('private'),
            'is_url_attached' => $this->boolean()->notNull()->defaultValue(false),
            'url' => $this->string(),
            'was_edited' => $this->boolean()->notNull()->defaultValue(false),
            'is_archived' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());

        // Indexes for chat table
        $this->createIndex('idx-chat-creator_id', '{{%chat}}', 'creator_id');
        $this->createIndex('idx-chat-type', '{{%chat}}', 'type');
        $this->createIndex('idx-chat-is_archived', '{{%chat}}', 'is_archived');
        $this->createIndex('idx-chat-is_deleted', '{{%chat}}', 'is_deleted');

        // Creating table 'thread'
        $this->createTable('{{%thread}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'creator_id' => $this->integer()->notNull(),
            'icon' => $this->string(),
            'title' => $this->string()->notNull(),
            'details' => $this->text(),
            'was_edited' => $this->boolean()->notNull()->defaultValue(false),
            'is_archived' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());

        // Foreign key and index for thread
        $this->addForeignKey(
            'fk-thread-chat_id',
            '{{%thread}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx-thread-chat_id', '{{%thread}}', 'chat_id');
        $this->createIndex('idx-thread-creator_id', '{{%thread}}', 'creator_id');
        $this->createIndex('idx-thread-is_archived', '{{%thread}}', 'is_archived');
        $this->createIndex('idx-thread-is_deleted', '{{%thread}}', 'is_deleted');

        // Creating table 'message'
        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'thread_id' => $this->integer(),
            'author_id' => $this->integer()->notNull(),
            'text' => $this->text(),
            'reply_on_id' => $this->integer(),
            'is_seen_by_interlocutor' => $this->boolean()->notNull()->defaultValue(false),
            'was_edited' => $this->boolean()->notNull()->defaultValue(false),
            'is_archived' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());

        // Foreign keys and indexes for message
        $this->addForeignKey(
            'fk-message-chat_id',
            '{{%message}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-message-thread_id',
            '{{%message}}',
            'thread_id',
            '{{%thread}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-message-reply_on_id',
            '{{%message}}',
            'reply_on_id',
            '{{%message}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->createIndex('idx-message-chat_id', '{{%message}}', 'chat_id');
        $this->createIndex('idx-message-thread_id', '{{%message}}', 'thread_id');
        $this->createIndex('idx-message-author_id', '{{%message}}', 'author_id');
        $this->createIndex('idx-message-reply_on_id', '{{%message}}', 'reply_on_id');
        $this->createIndex('idx-message-is_archived', '{{%message}}', 'is_archived');
        $this->createIndex('idx-message-is_deleted', '{{%message}}', 'is_deleted');

        // Creating table 'message_attachment'
        $this->createTable('{{%message_attachment}}', [
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull(),
            'type' => $this->string(20)->notNull(),
            'location_type' => $this->string(20)->notNull(),
            'url' => $this->string(),
            'filename' => $this->string(),
            'file_mime' => $this->string(),
            'file_ext' => $this->string(),
            'file_size' => $this->integer(),
            'uploaded_at' => $this->timestamp()->notNull(),
        ], $this->getTableOptions());

        // Foreign key and index for message_attachment
        $this->addForeignKey(
            'fk-message_attachment-message_id',
            '{{%message_attachment}}',
            'message_id',
            '{{%message}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx-message_attachment-message_id', '{{%message_attachment}}', 'message_id');

        // Creating table 'public_chat_participant'
        $this->createTable('{{%public_chat_participant}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'icon' => $this->string(),
            'joined_at' => $this->timestamp()->notNull(),
            'left_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());

        // Foreign key and index for public_chat_participant
        $this->addForeignKey(
            'fk-public_chat_participant-chat_id',
            '{{%public_chat_participant}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx-public_chat_participant-chat_id', '{{%public_chat_participant}}', 'chat_id');
        $this->createIndex('idx-public_chat_participant-user_id', '{{%public_chat_participant}}', 'user_id');

        // Creating table 'changes_log'
        $this->createTable('{{%changes_log}}', [
            'id' => $this->primaryKey(),
            'entity_type' => $this->string(20)->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'action_at' => $this->timestamp()->notNull(),
            'action' => $this->string(20)->notNull(),
            'changes' => $this->json(),
            'ip_address' => $this->string(),
            'user_agent' => $this->text(),
        ], $this->getTableOptions());

        // Indexes for changes_log
        $this->createIndex('idx-changes_log-entity_id', '{{%changes_log}}', 'entity_id');
        $this->createIndex('idx-changes_log-user_id', '{{%changes_log}}', 'user_id');
    }

    public function safeDown()
    {
        $this->db = Yii::$app->get($this->connection);

        $this->dropTable('{{%changes_log}}');
        $this->dropTable('{{%public_chat_participant}}');
        $this->dropTable('{{%message_attachment}}');
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%thread}}');
        $this->dropTable('{{%chat}}');
    }

    private function getTableOptions(): string
    {
        return 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250517_145505_create_messenger_tables cannot be reverted.\n";

        return false;
    }
    */
}
