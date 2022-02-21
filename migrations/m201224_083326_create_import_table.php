<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%import}}`.
 */
class m201224_083326_create_import_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%import}}', [
            'id' => $this->primaryKey(),
            'algorithm' => $this->text()->notNull(),
            'type' => $this->integer()->notNull(),
            'endpoint' => $this->text(),
            'schedule' => $this->text(),
            'imported_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        $this->addColumn('developer', 'import_id', $this->integer());
        
        // creates index for column `import_id` for table `{{%developer}}`
        $this->createIndex(
            '{{%idx-developer-import_id}}',
            '{{%developer}}',
            'import_id'
        );

        // add foreign key for table `{{%developer}}`
        $this->addForeignKey(
            '{{%fk-developer-import_id}}',
            '{{%developer}}',
            'import_id',
            '{{%import}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {        
        // drops foreign key for table `{{%developer}}`
        $this->dropForeignKey(
            '{{%fk-developer-import_id}}',
            '{{%developer}}'
        );

        // drops index for column `import_id` for table `{{%developer}}`
        $this->dropIndex(
            '{{%idx-developer-import_id}}',
            '{{%developer}}'
        );
        
        $this->dropColumn('developer', 'import_id');
        
        $this->dropTable('{{%import}}');
    }
}
