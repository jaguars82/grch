<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%newbuilding_complex_stage}}`.
 */
class m211025_140728_create_newbuilding_complex_stage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%newbuilding_complex_stage}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200),
            'description' => $this->text(),
            'newbuilding_complex_id' => $this->integer()
        ]);

        $this->createIndex(
            'idx-newbuilding_complex_stage-newbuilding_complex_id',
            '{{%newbuilding_complex_stage}}',
            'newbuilding_complex_id'
        );

        $this->addForeignKey(
            '{{%fk-newbuilding_complex_stage-newbuilding_complex_id}}',
            '{{%newbuilding_complex_stage}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-newbuilding_complex_stage-newbuilding_complex_id}}',
            '{{%newbuilding_complex_stage}}',
        );

        $this->dropIndex(
            'idx-newbuilding_complex_stage-newbuilding_complex_id',
            '{{%newbuilding_complex_stage}}',
        );

        $this->dropTable('{{%newbuilding_complex_stage}}');
    }
}
