<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding_complex}}`.
 */
class m201106_122510_add_advantages_column_to_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newbuilding_complex', 'advantages', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newbuilding_complex', 'advantages');
    }
}
