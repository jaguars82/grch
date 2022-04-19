<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%support_tickets}}`.
 */
class m220419_081522_add_is_archived_column_to_support_tickets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%support_tickets}}', 'is_archived', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%support_tickets}}', 'is_archived');
    }
}
