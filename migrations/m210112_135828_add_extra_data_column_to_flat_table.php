<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m210112_135828_add_extra_data_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'extra_data', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'extra_data');
    }
}
