<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%entrance}}`.
 */
class m220601_070025_add_status_and_deadline_columns_to_entrance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%entrance}}', 'status', $this->smallInteger(2)->after('material')->defaultValue(0)->notNull());
        $this->addColumn('{{%entrance}}', 'deadline', $this->timestamp()->after('status')->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%entrance}}', 'status');
        $this->dropColumn('{{%entrance}}', 'deadline');
    }
}
