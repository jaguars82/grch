<?php

use yii\db\Migration;

/**
 * Class m201102_150630_create_objects_tables
 */
class m201102_150630_create_objects_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%newbuilding}}', [
            'id' => $this->primaryKey(),
            'newbuilding_complex_id' => $this->integer()->notNull(),
            'name' => $this->string(200),
            'address' => $this->string(200),
            'longitude' => $this->float(),
            'latitude' => $this->float(),
            'detail' => $this->text(),
            'total_floor' => $this->integer()->notNull(),
            'material' => $this->string(200),
            'status' => $this->integer()->defaultValue(0)->notNull(),
            'deadline' => $this->timestamp()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `{{%newbuilding}}`
        $this->createIndex(
            '{{%idx-newbuilding-newbuilding_complex_id}}',
            '{{%newbuilding}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%newbuilding}}`
        $this->addForeignKey(
            '{{%fk-newbuilding-newbuilding_complex_id}}',
            '{{%newbuilding}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%flat}}', [
            'id' => $this->primaryKey(),
            'newbuilding_id' => $this->integer()->notNull(),
            'number' => $this->integer()->notNull(),
            'layout' => $this->string(200),
            'detail' => $this->text(),
            'area' => $this->float()->notNull(),
            'rooms' => $this->integer()->notNull(),
            'floor' => $this->integer()->notNull(),
            'section' => $this->integer(),
            'unit_price_cash' => $this->decimal(12, 2),
            'price_cash' => $this->decimal(12, 2),
            'unit_price_credit' => $this->decimal(12, 2),
            'price_credit' => $this->decimal(12, 2),
            'discount' => $this->float()->notNull()->defaultValue(0),
            'azimuth'=> $this->integer(),
            'notification' => $this->text(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        // creates index for column `newbuilding_complex_id` for table `{{%flat}}`
        $this->createIndex(
            '{{%idx-flat-newbuilding_id}}',
            '{{%flat}}',
            'newbuilding_id'
        );

        // add foreign key for table `{{%flat}}`
        $this->addForeignKey(
            '{{%fk-flat-newbuilding_id}}',
            '{{%flat}}',
            'newbuilding_id',
            '{{%newbuilding}}',
            'id',
            'CASCADE'
        );
        
        $this->createTable('{{%flat_image}}', [
            'id' => $this->primaryKey(),
            'flat_id' => $this->integer()->notNull(),
            'image' => $this->string(200),
        ]);
        
        // creates index for column `flat_id` for table `{{%flat_image}}`
        $this->createIndex(
            '{{%idx-flat_image-flat_id}}',
            '{{%flat_image}}',
            'flat_id'
        );

        // add foreign key for table `{{%flat_image}}`
        $this->addForeignKey(
            '{{%fk-flat_image-flat_id}}',
            '{{%flat_image}}',
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
        // drops foreign key for table `{{%flat_image}}`
        $this->dropForeignKey(
            '{{%fk-flat_image-flat_id}}',
            '{{%flat_image}}'
        );

        // drops index for column `flat_id` for table `{{%flat_image}}`
        $this->dropIndex(
            '{{%idx-flat_image-flat_id}}',
            '{{%flat_image}}'
        );
        
        $this->dropTable('{{%flat_image}}');
        
        // drops foreign key for table `{{%flat}}`
        $this->dropForeignKey(
            '{{%fk-flat-newbuilding_id}}',
            '{{%flat}}'
        );

        // drops index for column `newbuilding_id` for table `{{%flat}}`
        $this->dropIndex(
            '{{%idx-flat-newbuilding_id}}',
            '{{%flat}}'
        );
        
        $this->dropTable('{{%flat}}');
        
        // drops foreign key for table `{{%newbuilding}}`
        $this->dropForeignKey(
            '{{%fk-newbuilding-newbuilding_complex_id}}',
            '{{%newbuilding}}'
        );

        // drops index for column `newbuilding_complex_id` for table `{{%newbuilding}}`
        $this->dropIndex(
            '{{%idx-newbuilding-newbuilding_complex_id}}',
            '{{%newbuilding}}'
        );
        
        $this->dropTable('{{%newbuilding}}');
    }
}
