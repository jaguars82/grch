<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%entrance}}`.
 */
class m220530_071602_create_entrance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%entrance}}', [
            'id' => $this->primaryKey(),
            'newbuilding_id' => $this->integer()->notNull(),
            'name' => $this->string(200)->defaultValue(null),
            'number' => $this->smallInteger(3),
            'floors' => $this->smallInteger(3),
            'material' => $this->string()->defaultValue(null),
            'azimuth' => $this->smallInteger(3)->defaultValue(0),
            'longitude' => $this->decimal(8, 6),
            'latitude' => $this->decimal(8, 6),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        // creates index for column `newbuilding_id` for table `entrance`
        $this->createIndex(
            '{{%idx-entrance-newbuilding_id}}',
            '{{%entrance}}',
            'newbuilding_id'
        );

        // add foreign key for table `{{%agency_entrance}}`
        $this->addForeignKey(
            '{{%fk-entrance-newbuilding_id}}',
            '{{%entrance}}',
            'newbuilding_id',
            '{{%newbuilding}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        // drops foreign key for table `{{%entrance}}`
        $this->dropForeignKey(
            '{{%fk-entrance-newbuilding_id}}',
            '{{%entrance}}'
        );

        // drops index for column `newbuilding_id`
        $this->dropIndex(
            '{{%idx-entrance-newbuilding_id}}',
            '{{%sentrance}}'
        );
        
        $this->dropTable('{{%entrance}}');
    }
}
