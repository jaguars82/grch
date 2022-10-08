<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m220831_084425_add_developer_id_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'developer_id', $this->integer()->after('agency_id'));

        $this->createIndex(
            'idx-user-developer_id',
            '{{%user}}',
            'developer_id'
        );

        $this->addForeignKey(
            '{{%fk-user-developer_id}}',
            '{{%user}}',
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
            '{{%fk-user-developer_id}}',
            '{{%user}}',
        );

        $this->dropIndex(
            'idx-user-developer_id',
            '{{%user}}',
        );

        $this->dropColumn('{{%user}}', 'developer_id');
    }
}
