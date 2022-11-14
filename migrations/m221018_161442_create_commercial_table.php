<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%commercial}}`.
 */
class m221018_161442_create_commercial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%commercial}}', [
            'id' => $this->primaryKey(),
            'initiator_id'=> $this->integer()->notNull(), 
            'number'=> $this->string(10)->defaultValue(null),
            'name'=> $this->string()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'active'=> $this->integer(1)->defaultValue(1), 
            'is_formed'=> $this->integer(1)->defaultValue(0), 
            'settings'=> $this->string(1000)->defaultValue(null),
            'created_at'=>  $this->timestamp()->defaultValue(null), 
            'updated_at'=>  $this->timestamp()->defaultValue(null),
           
        ]);
        
        $this->createIndex(
            'idx-commercial-initiator_id',
            '{{%commercial}}',
            'initiator_id'
        );

        $this->addForeignKey(
            '{{%fk-commercial-initiator_id}}',
            '{{%commercial}}',
            'initiator_id',
            '{{%user}}',
            'id',
            'NO ACTION'
        );

        $this->createTable('{{%commercial_history}}', [
            'id' => $this->primaryKey(),
            'commercial_id'=> $this->integer()->notNull(),
            'sent_by' => $this->string(15)->notNull(),
            'email' => $this->string(200)->defaultValue(null),
            'sent_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-commercial_history-commercial_id',
            '{{%commercial_history}}',
            'commercial_id'
        );

        $this->addForeignKey(
            '{{%fk-commercial_history-commercial_id}}',
            '{{%commercial_history}}',
            'commercial_id',
            '{{%commercial}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%commercial_flat}}', [            
            'commercial_id' => $this->integer()->notNull(),
            'flat_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `commercial_id` for table `{{%commercial_flat}}`
        $this->createIndex(
            '{{%idx-commercial_flat-commercial_id}}',
            '{{%commercial_flat}}',
            'commercial_id'
        );

        // add foreign key for table `{{%commercial_flat}}`
        $this->addForeignKey(
            '{{%fk-commercial_flat-commercial_id}}',
            '{{%commercial_flat}}',
            'commercial_id',
            '{{%commercial}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `flat_id` for table `{{%commercial_flat}}`
        $this->createIndex(
            '{{%idx-commercial_flat-flat_id}}',
            '{{%commercial_flat}}',
            'flat_id'
        );

        // add foreign key for table `{{%commercial_flat}}`
        $this->addForeignKey(
            '{{%fk-commercial_flat-flat_id}}',
            '{{%commercial_flat}}',
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

        // drops foreign key for table `{{%commercial_flat}}`
        $this->dropForeignKey(
            '{{%fk-commercial_flat-commercial_id}}',
            '{{%commercial_flat}}'
        );

        // drops index for column `commercial_id` for table `{{%commercial_flat}}`
        $this->dropIndex(
            '{{%idx-commercial_flat-commercial_id}}',
            '{{%commercial_flat}}'
        );
                
        // drops foreign key for table `{{%commercial_flat}}`
        $this->dropForeignKey(
            '{{%fk-commercial_flat-flat_id}}',
            '{{%commercial_flat}}'
        );

        // drops index for column `flat_id` for table `{{%commercial_flat}}`
        $this->dropIndex(
            '{{%idx-commercial_flat-flat_id}}',
            '{{%commercial_flat}}'
        );
        
        $this->dropTable('{{%commercial_flat}}');

        $this->dropForeignKey(
            '{{%fk-commercial_history-commercial_id}}',
            '{{%commercial_history}}',
        );

        $this->dropIndex(
            'idx-commercial_history-commercial_id',
            '{{%commercial_history}}',
        );

        $this->dropTable('{{%commercial_history}}');
                
        $this->dropForeignKey(
            '{{%fk-commercial-initiator_id}}',
            '{{%commercial}}',
        );

        $this->dropIndex(
            'idx-commercial-initiator_id',
            '{{%commercial}}',
        );

        $this->dropTable('{{%commercial}}');
    }
}
