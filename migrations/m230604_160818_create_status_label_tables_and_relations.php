<?php

use yii\db\Migration;

/**
 * Class m230604_160818_create_status_label_tables_and_relations
 */
class m230604_160818_create_status_label_tables_and_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%status_label_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'short_name' => $this->string(),
        ], $tableOptions);

        $this->insert('{{%status_label_type}}', [
            'id' => 1,
            'name' => 'нет в рекламе',
        ]);

        $this->createTable('{{%status_label}}', [
            'id' => $this->primaryKey(),
            'label_type_id' => $this->integer()->notNull(),
            'is_active' => $this->boolean()->defaultValue(true),
            'creator_id' => $this->integer()->defaultValue(NULL),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'has_expiration_date' => $this->boolean()->defaultValue(false),
            'expires_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        $this->createTable('{{%secondary_advertisement_status_label}}', [
            'secondary_advertisement_id' => $this->integer()->notNull(),
            'status_label_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `label_type_id` for table `{{%status_label}}`
        $this->createIndex(
            '{{%idx-status_label-label_type_id}}',
            '{{%status_label}}',
            'label_type_id'
        );

        // add foreign key for table `{{%status_label}}`
        $this->addForeignKey(
            '{{%fk-status_label-label_type_id}}',
            '{{%status_label}}',
            'label_type_id',
            '{{%status_label_type}}',
            'id',
            'CASCADE'
        );

        // creates index for column `creator_id` for table `{{%status_label}}`
        $this->createIndex(
            '{{%idx-status_label-creator_id}}',
            '{{%status_label}}',
            'creator_id'
        );

        // add foreign key for table `{{%status_label}}`
        $this->addForeignKey(
            '{{%fk-status_label-creator_id}}',
            '{{%status_label}}',
            'creator_id',
            '{{%user}}',
            'id',
            'NO ACTION'
        );

        // creates index for column `secondary_advertisement_id` for table `{{%secondary_advertisement_status_label}}`
        $this->createIndex(
            '{{%idx-advertisement_label-secondary_advertisement_id}}',
            '{{%secondary_advertisement_status_label}}',
            'secondary_advertisement_id'
        );

        // add foreign key for table `{{%secondary_advertisement_status_label}}`
        $this->addForeignKey(
            '{{%fk-advertisement_label-secondary_advertisement_id}}',
            '{{%secondary_advertisement_status_label}}',
            'secondary_advertisement_id',
            '{{%secondary_advertisement}}',
            'id',
            'CASCADE'
        );

        // creates index for column `status_label_id` for table `{{%secondary_advertisement_status_label}}`
        $this->createIndex(
            '{{%idx-advertisement_label-status_label_id}}',
            '{{%secondary_advertisement_status_label}}',
            'status_label_id'
        );

        // add foreign key for table `{{%secondary_advertisement_status_label}}`
        $this->addForeignKey(
            '{{%fk-advertisement_label-status_label_id}}',
            '{{%secondary_advertisement_status_label}}',
            'status_label_id',
            '{{%status_label}}',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%secondary_advertisement_status_label}}`
        $this->dropForeignKey(
            '{{%fk-advertisement_label-status_label_id}}',
            '{{%secondary_advertisement_status_label}}'
        );

        // drops index for column `status_label_id` for table `{{%secondary_advertisement_status_label}}`
        $this->dropIndex(
            '{{%idx-advertisement_label-status_label_id}}',
            '{{%secondary_advertisement_status_label}}'
        );

        // drops foreign key for table `{{%secondary_advertisement_status_label}}`
        $this->dropForeignKey(
            '{{%fk-advertisement_label-secondary_advertisement_id}}',
            '{{%secondary_advertisement_status_label}}'
        );

        // drops index for column `secondary_advertisement_id` for table `{{%secondary_advertisement_status_label}}`
        $this->dropIndex(
            '{{%idx-advertisement_label-secondary_advertisement_id}}',
            '{{%secondary_advertisement_status_label}}'
        );

        // drops foreign key for table `{{%status_label}}`
        $this->dropForeignKey(
            '{{%fk-status_label-creator_id}}',
            '{{%status_label}}'
        );

        // drops index for column `creator_id` for table `{{%status_label}}`
        $this->dropIndex(
            '{{%idx-status_label-creator_id}}',
            '{{%status_label}}'
        );

        // drops foreign key for table `{{%status_label}}`
        $this->dropForeignKey(
            '{{%fk-status_label-label_type_id}}',
            '{{%status_label}}'
        );

        // drops index for column `label_type_id` for table `{{%status_label}}`
        $this->dropIndex(
            '{{%idx-status_label-label_type_id}}',
            '{{%status_label}}'
        );

        $this->dropTable('{{%secondary_advertisement_status_label}}');
        $this->dropTable('{{%status_label}}');
        $this->dropTable('{{%status_label_type}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230604_160818_create_status_label_tables_and_relations cannot be reverted.\n";

        return false;
    }
    */
}
