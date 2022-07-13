<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding_complex}}`.
 */
class m220709_124142_add_virtual_structure_and_use_virtual_structure_columns_to_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%newbuilding_complex}}', 'use_virtual_structure', $this->smallInteger(1)->defaultValue(0));
        $this->addColumn('{{%newbuilding_complex}}', 'virtual_structure', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%newbuilding_complex}}', 'virtual_structure');
        $this->dropColumn('{{%newbuilding_complex}}', 'use_virtual_structure');
    }
}
