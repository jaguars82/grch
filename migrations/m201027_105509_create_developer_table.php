<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%developer}}`.
 */
class m201027_105509_create_developer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%developer}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)->notNull()->unique(),
            'address' => $this->string(200)->notNull(),
            'longitude' => $this->float(),
            'latitude' => $this->float(),
            'logo' => $this->string(200)->notNull()->unique(),
            'detail' => $this->text()->notNull(),
            'free_reservation' => $this->text(),
            'paid_reservation' => $this->text(),
        ]);
        
        $this->createTable('{{%developer_contact}}', [            
            'developer_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `developer_id` for table `developer_contact`
        $this->createIndex(
            '{{%idx-developer_contact-developer_id}}',
            '{{%developer_contact}}',
            'developer_id'
        );

        // add foreign key for table `{{%developer_contact}}`
        $this->addForeignKey(
            '{{%fk-developer_contact-developer_id}}',
            '{{%developer_contact}}',
            'developer_id',
            '{{%developer}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `contact_id` for table `developer_contact`
        $this->createIndex(
            '{{%idx-developer_contact-contact_id}}',
            '{{%developer_contact}}',
            'contact_id'
        );

        // add foreign key for table `{{%developer_contact}}`
        $this->addForeignKey(
            '{{%fk-developer_contact-contact_id}}',
            '{{%developer_contact}}',
            'contact_id',
            '{{%contact}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%developer_contact}}`
        $this->dropForeignKey(
            '{{%fk-developer_contact-contact_id}}',
            '{{%developer_contact}}'
        );

        // drops index for column `contact_id`
        $this->dropIndex(
            '{{%idx-developer_contact-contact_id}}',
            '{{%developer_contact}}'
        );
        
        // drops foreign key for table `{{%developer_contact}}`
        $this->dropForeignKey(
            '{{%fk-developer_contact-developer_id}}',
            '{{%developer_contact}}'
        );

        // drops index for column `developer_id`
        $this->dropIndex(
            '{{%idx-developer_contact-developer_id}}',
            '{{%developer_contact}}'
        );
        
        $this->dropTable('{{%developer_contact}}');
        
        $this->dropTable('{{%developer}}');
    }
}
