<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%archive}}`.
 */
class m211019_083724_create_archive_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%archive}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string(200),
            'checked' => $this->boolean(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->addColumn('newbuilding_complex', 'archive_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%archive}}');
        
        $this->dropColumn('newbuilding_complex', 'archive_id');
    }
}
