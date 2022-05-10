<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding_complex}}`.
 */
class m220510_202537_add_has_active_buildings_column_to_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%newbuilding_complex}}', 'has_active_buildings', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%newbuilding_complex}}', 'has_active_buildings');
    }
}
