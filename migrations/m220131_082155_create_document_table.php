<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document}}`.
 */
class m220131_082155_create_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string(200)->notNull(),
            'name' => $this->string(200)->notNUll(),
            'size' => $this->double(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ]);

        $this->createTable('{{%newbuilding_complex_document}}', [
            'document_id' => $this->integer(),
            'newbuilding_complex_id' => $this->integer()
        ]);

        // creates index for column `newbuilding_complex_id`
        $this->createIndex(
            '{{%idx-document_newbuilding_complex-newbuilding_complex_id}}',
            '{{%newbuilding_complex_document}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%newbuilding_complex_document}}`
        $this->addForeignKey(
            '{{%fk-newbuilding_complex_document-newbuilding_complex_id}}',
            '{{%newbuilding_complex_document}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `document_id`
        $this->createIndex(
            '{{%idx-newbuilding_complex_document-document_id}}',
            '{{%newbuilding_complex_document}}',
            'document_id'
        );

        // add foreign key for table `{{%document}}`
        $this->addForeignKey(
            '{{%fk-newbuilding_complex_document-document_id}}',
            '{{%newbuilding_complex_document}}',
            'document_id',
            '{{%document}}',
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
            '{{%fk-newbuilding_complex_document-document_id}}',
            '{{%newbuilding_complex_document}}'
        );
        $this->dropIndex(
            '{{%idx-newbuilding_complex_document-document_id}}',
            '{{%newbuilding_complex_document}}'
        );
        $this->dropForeignKey(
            '{{%fk-newbuilding_complex_document-newbuilding_complex_id}}',
            '{{%newbuilding_complex_document}}'
        );
        $this->dropIndex(
            '{{%idx-document_newbuilding_complex-newbuilding_complex_id}}',
            '{{%newbuilding_complex_document}}'
        );
        $this->dropTable('{{%newbuilding_complex_document}}');
        $this->dropTable('{{%document}}');
    }
}
