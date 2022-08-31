<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m220831_092413_add_developer_id_column_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'developer_id', $this->integer()->after('flat_id'));

        $this->createIndex(
            'idx-application-developer_id',
            '{{%application}}',
            'developer_id'
        );

        $this->addForeignKey(
            '{{%fk-application-developer_id}}',
            '{{%application}}',
            'developer_id',
            '{{%developer}}',
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
            '{{%fk-application-developer_id}}',
            '{{%application}}',
        );

        $this->dropIndex(
            'idx-application-developer_id',
            '{{%application}}',
        );

        $this->dropColumn('{{%application}}', 'developer_id');
    }
}
