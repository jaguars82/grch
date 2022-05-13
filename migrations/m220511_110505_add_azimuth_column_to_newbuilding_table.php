<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding}}`.
 */
class m220511_110505_add_azimuth_column_to_newbuilding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%newbuilding}}', 'azimuth', $this->smallInteger(3)->after('latitude')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%newbuilding}}', 'azimuth');
    }
}
