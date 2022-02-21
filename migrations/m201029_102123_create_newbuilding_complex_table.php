<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%newbuilding_complex}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%developer}}`
 */
class m201029_102123_create_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%newbuilding_complex}}', [
            'id' => $this->primaryKey(),
            'developer_id' => $this->integer()->notNull(),
            'name' => $this->string(200)->notNull()->unique(),
            'address' => $this->string(200),
            'district' => $this->string(200),
            'longitude' => $this->float(),
            'latitude' => $this->float(),
            'logo' => $this->string(200)->unique(),
            'detail' => $this->text(),
            'algorithm' => $this->text(),
            'offer_new_price_permit' => $this->boolean(),
            'project_declaration' => $this->string(200),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        // creates index for column `developer_id`
        $this->createIndex(
            '{{%idx-newbuilding_complex-developer_id}}',
            '{{%newbuilding_complex}}',
            'developer_id'
        );

        // add foreign key for table `{{%developer}}`
        $this->addForeignKey(
            '{{%fk-newbuilding_complex-developer_id}}',
            '{{%newbuilding_complex}}',
            'developer_id',
            '{{%developer}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%newbuilding_complex_contact}}', [            
            'newbuilding_complex_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `newbuilding_complex_contact`
        $this->createIndex(
            '{{%idx-newbuilding_complex_contact-newbuilding_complex_id}}',
            '{{%newbuilding_complex_contact}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%newbuilding_complex_contact}}`
        $this->addForeignKey(
            '{{%fk-newbuilding_complex_contact-newbuilding_complex_id}}',
            '{{%newbuilding_complex_contact}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `contact_id` for table `newbuilding_complex_contact`
        $this->createIndex(
            '{{%idx-newbuilding_complex_contact-contact_id}}',
            '{{%newbuilding_complex_contact}}',
            'contact_id'
        );

        // add foreign key for table `{{%newbuilding_complex_contact}}`
        $this->addForeignKey(
            '{{%fk-newbuilding_complex_contact-contact_id}}',
            '{{%newbuilding_complex_contact}}',
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
        // drops foreign key for table `{{%newbuilding_complex_contact}}`
        $this->dropForeignKey(
            '{{%fk-newbuilding_complex_contact-contact_id}}',
            '{{%newbuilding_complex_contact}}'
        );

        // drops index for column `contact_id`
        $this->dropIndex(
            '{{%idx-newbuilding_complex_contact-contact_id}}',
            '{{%newbuilding_complex_contact}}'
        );
        
        // drops foreign key for table `{{%newbuilding_complex_contact}}`
        $this->dropForeignKey(
            '{{%fk-newbuilding_complex_contact-newbuilding_complex_id}}',
            '{{%newbuilding_complex_contact}}'
        );

        // drops index for column `newbuilding_complex_id`
        $this->dropIndex(
            '{{%idx-newbuilding_complex_contact-newbuilding_complex_id}}',
            '{{%newbuilding_complex_contact}}'
        );
        
        $this->dropTable('{{%newbuilding_complex_contact}}');
        
        // drops foreign key for table `{{%developer}}`
        $this->dropForeignKey(
            '{{%fk-newbuilding_complex-developer_id}}',
            '{{%newbuilding_complex}}'
        );

        // drops index for column `developer_id`
        $this->dropIndex(
            '{{%idx-newbuilding_complex-developer_id}}',
            '{{%newbuilding_complex}}'
        );

        $this->dropTable('{{%newbuilding_complex}}');
    }
}
