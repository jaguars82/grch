<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%not_usage_column_in_newbuilding_complex}}`.
 */
class m211029_015151_drop_not_usage_column_in_newbuilding_complex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('newbuilding_complex', 'advantages');
        $this->dropColumn('newbuilding_complex', 'address');
        $this->dropColumn('newbuilding_complex', 'district');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('newbuilding_complex', 'advantages', $this->text());
        $this->addColumn('newbuilding_complex', 'address', $this->string(200));
        $this->addColumn('newbuilding_complex', 'district', $this->string(200));
    }
}
