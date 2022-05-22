<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%action_data}}`.
 */
class m220522_185316_add_is_actual_column_to_action_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%action_data}}', 'is_actual', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%action_data}}', 'is_actual');
    }
}
