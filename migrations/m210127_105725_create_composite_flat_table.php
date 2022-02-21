<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%composite_flat}}`.
 */
class m210127_105725_create_composite_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%composite_flat}}', [
            'id' => $this->primaryKey(),
        ]);
        
        $this->addColumn('flat', 'composite_flat_id', $this->integer());
        
        // creates index for column `composite_flat_id` for table `{{%flat}}`
        $this->createIndex(
            '{{%idx-flat-composite_flat_id}}',
            '{{%flat}}',
            'composite_flat_id'
        );

        // add foreign key for table `{{%flat}}`
        $this->addForeignKey(
            '{{%fk-flat-composite_flat_id}}',
            '{{%flat}}',
            'composite_flat_id',
            '{{%composite_flat}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%flat}}`
        $this->dropForeignKey(
            '{{%fk-flat-composite_flat_id}}',
            '{{%flat}}'
        );

        // drops index for column `composite_flat_id` for table `{{%flat}}`
        $this->dropIndex(
            '{{%idx-flat-composite_flat_id}}',
            '{{%flat}}'
        );
        
        $this->dropColumn('flat', 'composite_flat_id');
        
        $this->dropTable('{{%composite_flat}}');
    }
}
