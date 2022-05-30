<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m220530_073634_add_entrance_id_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'entrance_id', $this->integer()->after('newbuilding_id')->defaultValue(null));

        // creates index for column `entrance_id` for table `{{%flat}}`
        $this->createIndex(
            '{{%idx-flat-entrance_id}}',
            '{{%flat}}',
            'entrance_id'
        );

        // add foreign key for table `{{%flat}}`
        $this->addForeignKey(
            '{{%fk-flat-entrance_id}}',
            '{{%flat}}',
            'entrance_id',
            '{{%entrance}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%flat}}`
        $this->dropForeignKey(
            '{{%fk-flat-entrance_id}}',
            '{{%flat}}'
        );

        // drops index for column `entrance_id` for table `{{%flat}}`
        $this->dropIndex(
            '{{%idx-flat-entrance_id}}',
            '{{%flat}}'
        );

        $this->dropColumn('{{%flat}}', 'entrance_id');
    }
}
