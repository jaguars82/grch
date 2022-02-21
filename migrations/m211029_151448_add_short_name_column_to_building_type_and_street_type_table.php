<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%building_type_and_street_type}}`.
 */
class m211029_151448_add_short_name_column_to_building_type_and_street_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_type', 'short_name', $this->string(20));
        $this->addColumn('street_type', 'short_name', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('building_type', 'short_name');
        $this->dropColumn('street_type', 'short_name');
    }
}
