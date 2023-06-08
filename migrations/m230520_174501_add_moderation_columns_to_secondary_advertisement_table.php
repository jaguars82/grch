<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%secondary_advertisement}}`.
 */
class m230520_174501_add_moderation_columns_to_secondary_advertisement_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%secondary_advertisement}}', 'is_moderated', $this->boolean()->after('is_active')->defaultValue(false));
        $this->addColumn('{{%secondary_advertisement}}', 'is_moderation_ok', $this->boolean()->after('is_moderated')->defaultValue(false));
        $this->addColumn('{{%secondary_advertisement}}', 'moderator_id', $this->integer()->after('is_moderation_ok')->defaultValue(NULL));
        $this->addColumn('{{%secondary_advertisement}}', 'moderated_at', $this->timestamp()->after('is_moderation_ok')->defaultValue(NULL));
        $this->addColumn('{{%secondary_advertisement}}', 'moderator_comment', $this->text()->after('moderated_at')->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'));

        // creates index for column `city_type_id` for table `{{%city}}`
        $this->createIndex(
            '{{%idx-secondary_advertisement-moderator_id}}',
            '{{%secondary_advertisement}}',
            'moderator_id'
        );

        // add foreign key for table `{{%agency}}`
        $this->addForeignKey(
            '{{%fk-secondary_advertisement-moderator_id}}',
            '{{%secondary_advertisement}}',
            'moderator_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-secondary_advertisement-moderator_id}}',
            '{{%secondary_advertisement}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_advertisement-moderator_id}}',
            '{{%secondary_advertisement}}',
        );

        $this->dropColumn('{{%secondary_advertisement}}', 'moderator_comment');
        $this->dropColumn('{{%secondary_advertisement}}', 'moderated_at');
        $this->dropColumn('{{%secondary_advertisement}}', 'moderator_id');
        $this->dropColumn('{{%secondary_advertisement}}', 'is_moderation_ok');
        $this->dropColumn('{{%secondary_advertisement}}', 'is_moderated');
    }
}
