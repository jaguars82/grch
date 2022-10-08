<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%application}}`.
 */
class m220823_093157_create_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%application}}', [
            'id' => $this->primaryKey(),
            'flat_id' => $this->integer()->notNull(),
            'applicant_id' => $this->integer()->notNull(),
            'status' => $this->integer(2)->defaultValue(0),
            'client_firstname' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'client_lastname' => $this->string(100)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'client_middlename' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'client_phone' => $this->string(12)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'client_email' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'applicant_comment' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'manager_firstname' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'manager_lastname' => $this->string(100)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'manager_middlename' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'manager_phone' => $this->string(12)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'manager_email' => $this->string(55)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'admin_comment' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'is_active' => $this->smallInteger(1)->defaultValue(1),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-application-flat_id',
            '{{%application}}',
            'flat_id'
        );

        $this->addForeignKey(
            '{{%fk-application-flat_id}}',
            '{{%application}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-application-applicant_id',
            '{{%application}}',
            'applicant_id'
        );

        $this->addForeignKey(
            '{{%fk-application-applicant_id}}',
            '{{%application}}',
            'applicant_id',
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
            '{{%fk-application-flat_id}}',
            '{{%application}}',
        );

        $this->dropIndex(
            'idx-application-flat_id',
            '{{%application}}',
        );

        $this->dropForeignKey(
            '{{%fk-application-applicant_id}}',
            '{{%application}}',
        );

        $this->dropIndex(
            'idx-application-applicant_id',
            '{{%application}}',
        );

        $this->dropTable('{{%application}}');
    }
}
