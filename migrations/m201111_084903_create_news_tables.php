<?php

use yii\db\Migration;

/**
 * Class m201111_084903_create_news_tables
 */
class m201111_084903_create_news_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(200)->notNull(),
            'detail' => $this->text()->notNull(),
            'image' => $this->string(200),
            'category' => $this->integer()->defaultValue(0)->notNull(),
            'search_link' => $this->text(),           
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        $this->createTable('{{%news_file}}', [
            'id' => $this->primaryKey(),
            'news_id' => $this->integer()->notNull(),
            'name' => $this->string(200),
            'saved_name' => $this->string(200),
        ]);
        
        // creates index for column `news_id` for table `{{%news_file}}`
        $this->createIndex(
            '{{%idx-news_file-news_id}}',
            '{{%news_file}}',
            'news_id'
        );

        // add foreign key for table `{{%news_file}}`
        $this->addForeignKey(
            '{{%fk-news_file-news_id}}',
            '{{%news_file}}',
            'news_id',
            '{{%news}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%news_newbuilding_complex}}', [            
            'newbuilding_complex_id' => $this->integer()->notNull(),
            'news_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `{{%news_newbuilding_complex}}`
        $this->createIndex(
            '{{%idx-news_newbuilding_complex-newbuilding_complex_id}}',
            '{{%news_newbuilding_complex}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%bank_newbuilding_complex}}`
        $this->addForeignKey(
            '{{%fk-news_newbuilding_complex-newbuilding_complex_id}}',
            '{{%news_newbuilding_complex}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `news_id` for table `{{%news_newbuilding_complex}}`
        $this->createIndex(
            '{{%idx-news_newbuilding_complex-news_id}}',
            '{{%news_newbuilding_complex}}',
            'news_id'
        );

        // add foreign key for table `{{%bank}}`
        $this->addForeignKey(
            '{{%fk-news_newbuilding_complex-news_id}}',
            '{{%news_newbuilding_complex}}',
            'news_id',
            '{{%news}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%news_flat}}', [            
            'flat_id' => $this->integer()->notNull(),
            'news_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `flat_id` for table `{{%news_flat}}`
        $this->createIndex(
            '{{%idx-news_flat-flat_id}}',
            '{{%news_flat}}',
            'flat_id'
        );

        // add foreign key for table `{{%news_flat}}`
        $this->addForeignKey(
            '{{%fk-news_flat-flat_id}}',
            '{{%news_flat}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `news_id` for table `{{%news_flat}}`
        $this->createIndex(
            '{{%idx-news_flat-news_id}}',
            '{{%news_flat}}',
            'news_id'
        );

        // add foreign key for table `{{%news_flat}}`
        $this->addForeignKey(
            '{{%fk-news_flat-news_id}}',
            '{{%news_flat}}',
            'news_id',
            '{{%news}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%action_data}}', [
            'id' => $this->primaryKey(),
            'news_id' => $this->integer()->notNull(),
            'resume' => $this->string(200),
            'expired_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        // creates index for column `news_id` for table `{{%action_data}}`
        $this->createIndex(
            '{{%idx-action_data-news_id}}',
            '{{%action_data}}',
            'news_id'
        );

        // add foreign key for table `{{%action_data}}`
        $this->addForeignKey(
            '{{%fk-action_data-news_id}}',
            '{{%action_data}}',
            'news_id',
            '{{%news}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%action_data}}`
        $this->dropForeignKey(
            '{{%fk-action_data-news_id}}',
            '{{%action_data}}'
        );

        // drops index for column `news_id` for table `{{%action_data}}`
        $this->dropIndex(
            '{{%idx-action_data-news_id}}',
            '{{%action_data}}'
        );
        
        $this->dropTable('{{%action_data}}');
        
        // drops foreign key for table `{{%news_flat}}`
        $this->dropForeignKey(
            '{{%fk-news_flat-news_id}}',
            '{{%news_flat}}'
        );

        // drops index for column `news_id` for table `{{%news_flat}}`
        $this->dropIndex(
            '{{%idx-news_flat-news_id}}',
            '{{%news_flat}}'
        );
        
        // drops foreign key for table `{{%news_flat}}`
        $this->dropForeignKey(
            '{{%fk-news_flat-flat_id}}',
            '{{%news_flat}}'
        );

        // drops index for column `flat_id` for table `{{%news_flat}}`
        $this->dropIndex(
            '{{%idx-news_flat-flat_id}}',
            '{{%news_flat}}'
        );
        
        $this->dropTable('{{%news_flat}}');
        
        // drops foreign key for table `{{%news_newbuilding_complex}}`
        $this->dropForeignKey(
            '{{%fk-news_newbuilding_complex-news_id}}',
            '{{%news_newbuilding_complex}}'
        );

        // drops index for column `news_id`
        $this->dropIndex(
            '{{%idx-news_newbuilding_complex-news_id}}',
            '{{%news_newbuilding_complex}}'
        );
        
        // drops foreign key for table `{{%news_newbuilding_complex}}`
        $this->dropForeignKey(
            '{{%fk-news_newbuilding_complex-newbuilding_complex_id}}',
            '{{%news_newbuilding_complex}}'
        );

        // drops index for column `newbuilding_complex_id` for table `{{%news_newbuilding_complex}}`
        $this->dropIndex(
            '{{%idx-news_newbuilding_complex-newbuilding_complex_id}}',
            '{{%news_newbuilding_complex}}'
        );
        
        $this->dropTable('{{%news_newbuilding_complex}}');
        
        // drops foreign key for table `{{%news_file}}`
        $this->dropForeignKey(
            '{{%fk-news_file-news_id}}',
            '{{%news_file}}'
        );

        // drops index for column `news_id` for table `{{%news_file}}`
        $this->dropIndex(
            '{{%idx-news_file-news_id}}',
            '{{%news_file}}'
        );
        
        $this->dropTable('{{%news_file}}');
        
        $this->dropTable('{{%news}}');
    }
}
