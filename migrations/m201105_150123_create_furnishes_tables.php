<?php

use yii\db\Migration;

/**
 * Class m201105_150123_create_furnishes_tables
 */
class m201105_150123_create_furnishes_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%furnish}}', [
            'id' => $this->primaryKey(),
            'newbuilding_complex_id' => $this->integer()->notNull(),
            'name' => $this->string(200)->notNull(),
            'detail' => $this->text(),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `{{%furnish}}`
        $this->createIndex(
            '{{%idx-furnish-newbuilding_complex_id}}',
            '{{%furnish}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%furnish}}`
        $this->addForeignKey(
            '{{%fk-furnish-newbuilding_complex_id}}',
            '{{%furnish}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%furnish_image}}', [
            'id' => $this->primaryKey(),
            'furnish_id' => $this->integer()->notNull(),
            'image' => $this->string(200),
        ]);
        
        // creates index for column `furnish_id` for table `{{%furnish_image}}`
        $this->createIndex(
            '{{%idx-furnish_image-furnish_id}}',
            '{{%furnish_image}}',
            'furnish_id'
        );

        // add foreign key for table `{{%furnish_image}}`
        $this->addForeignKey(
            '{{%fk-furnish_image-furnish_id}}',
            '{{%furnish_image}}',
            'furnish_id',
            '{{%furnish}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%furnish_image}}`
        $this->dropForeignKey(
            '{{%fk-furnish_image-furnish_id}}',
            '{{%furnish_image}}'
        );

        // drops index for column `furnish_id` for table `{{%furnish_image}}`
        $this->dropIndex(
            '{{%idx-furnish_image-furnish_id}}',
            '{{%furnish_image}}'
        );
        
        $this->dropTable('{{%furnish_image}}');
        
        // drops foreign key for table `{{%newbuilding}}`
        $this->dropForeignKey(
            '{{%fk-furnish-newbuilding_complex_id}}',
            '{{%furnish}}'
        );

        // drops index for column `newbuilding_complex_id` for table `{{%newbuilding}}`
        $this->dropIndex(
            '{{%idx-furnish-newbuilding_complex_id}}',
            '{{%furnish}}'
        );
        
        $this->dropTable('{{%furnish}}');
    }
}
