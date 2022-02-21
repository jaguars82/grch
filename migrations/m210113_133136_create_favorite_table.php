<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%favorite}}`.
 */
class m210113_133136_create_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%favorite}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'flat_id' => $this->integer()->notNull(),
            'comment' => $this->text(),
            'archived_by' => $this->timestamp()->defaultValue(null),
        ]);
        
        // creates index for column `user_id` for table `{{%favorite}}`
        $this->createIndex(
            '{{%idx-favorite-user_id}}',
            '{{%favorite}}',
            'user_id'
        );

        // add foreign key for table `{{%favorite}}`
        $this->addForeignKey(
            '{{%fk-favorite-user_id}}',
            '{{%favorite}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `flat_id` for table `{{%favorite}}`
        $this->createIndex(
            '{{%idx-favorite-flat_id}}',
            '{{%favorite}}',
            'flat_id'
        );

        // add foreign key for table `{{%favorite}}`
        $this->addForeignKey(
            '{{%fk-favorite-flat_id}}',
            '{{%favorite}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%favorite}}`
        $this->dropForeignKey(
            '{{%fk-favorite-flat_id}}',
            '{{%favorite}}'
        );

        // drops index for column `flat_id` for table `{{%favorite}}`
        $this->dropIndex(
            '{{%idx-favorite-flat_id}}',
            '{{%favorite}}'
        );
        
        // drops foreign key for table `{{%favorite}}`
        $this->dropForeignKey(
            '{{%fk-favorite-user_id}}',
            '{{%favorite}}'
        );

        // drops index for column `user_id` for table `{{%favorite}}`
        $this->dropIndex(
            '{{%idx-favorite-user_id}}',
            '{{%favorite}}'
        );
        
        $this->dropTable('{{%favorite}}');
    }
}
