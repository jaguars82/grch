<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%floor_layout}}`.
 */
class m210217_082629_create_floor_layout_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%floor_layout}}', [
            'id' => $this->primaryKey(),
            'newbuilding_id' => $this->integer()->notNull(),
            'floor' => $this->string(200)->notNull(),
            'section' => $this->string(200)->notNull(),
            'image' => $this->string(200)->notNull(),
            'map' => $this->text(),
        ]);
        
        // creates index for column `newbuilding_id` for table `{{%floor_layout}}`
        $this->createIndex(
            '{{%idx-floor_layout-newbuilding_id}}',
            '{{%floor_layout}}',
            'newbuilding_id'
        );

        // add foreign key for table `{{%floor_layout}}`
        $this->addForeignKey(
            '{{%fk-floor_layout-newbuilding_id}}',
            '{{%floor_layout}}',
            'newbuilding_id',
            '{{%newbuilding}}',
            'id',
            'CASCADE'
        );
        
        $this->addColumn('flat', 'floor_position', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'floor_position');
        
        // drops foreign key for table `{{%floor_layout}}`
        $this->dropForeignKey(
            '{{%fk-floor_layout-newbuilding_id}}',
            '{{%floor_layout}}'
        );

        // drops index for column `newbuilding_id` for table `{{%floor_layout}}`
        $this->dropIndex(
            '{{%idx-floor_layout-newbuilding_id}}',
            '{{%floor_layout}}'
        );
        
        $this->dropTable('{{%floor_layout}}');
    }
}
