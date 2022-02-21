<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newbuilding_complex}}`.
 */
class m211028_072952_add_bank_tariff_column_to_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newbuilding_complex', 'bank_tariffs', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newbuilding_complex', 'bank_tariffs', $this->text());
    }
}
