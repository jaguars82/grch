<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m220911_180320_add_reservation_conditions_column_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'reservation_conditions', $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL')->after('manager_email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'reservation_conditions');
    }
}
