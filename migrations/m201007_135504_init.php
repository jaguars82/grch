<?php

use yii\db\Migration;

/**
 * Class m201007_135504_init
 */
class m201007_135504_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(200)->notNull(),
            'person' => $this->string(200)->notNull(),
            'phone' => $this->string()->notNull(),
            'photo' => $this->string(200),
            'worktime' => $this->string(200),
        ]);
        
        $this->createTable('{{%agency}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)->notNull(),
            'address' => $this->string(200)->notNull(),
            'longitude' => $this->float(),
            'latitude' => $this->float(),
            'logo' => $this->string(200)->notNull(),
            'detail' => $this->text()->notNull(),
            'user_limit' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);
        
        $this->createTable('{{%agency_contact}}', [            
            'agency_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `agency_contact`
        $this->createIndex(
            '{{%idx-agency_contact-agency_id}}',
            '{{%agency_contact}}',
            'agency_id'
        );

        // add foreign key for table `{{%agency_contact}}`
        $this->addForeignKey(
            '{{%fk-agency_contact-agency_id}}',
            '{{%agency_contact}}',
            'agency_id',
            '{{%agency}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `bank_id` for table `agency_contact`
        $this->createIndex(
            '{{%idx-agency_contact-contact_id}}',
            '{{%agency_contact}}',
            'contact_id'
        );

        // add foreign key for table `{{%agency_contact}}`
        $this->addForeignKey(
            '{{%fk-agency_contact-contact_id}}',
            '{{%agency_contact}}',
            'contact_id',
            '{{%contact}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'agency_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'middle_name' => $this->string(100),
            'phone' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'telegram_id' => $this->string()->unique(),
            'telegram_chat_id' => $this->string()->unique(),
            'otp' => $this->string(6),
            'otp_created_at' => $this->integer(),
            'otp_expired_at' => $this->integer(),
            'auth_key' => $this->string(32),
        ]);

        $this->createIndex(
            'idx-user-agency_id',
            'user',
            'agency_id'
        );

        $this->addForeignKey(
            'fk-user-agency_id',
            'user',
            'agency_id',
            'agency',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        echo "m201007_135504_init cannot be reverted.\n";

        return false;
    }
}
