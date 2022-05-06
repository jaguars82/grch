<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding}}`.
 */
class m220506_071528_add_active_column_to_newbuilding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%newbuilding}}', 'active', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%newbuilding}}', 'active');
    }
}
