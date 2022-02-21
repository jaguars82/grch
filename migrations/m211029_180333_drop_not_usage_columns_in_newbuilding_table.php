<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%not_usage_columns_in_newbuilding}}`.
 */
class m211029_180333_drop_not_usage_columns_in_newbuilding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('newbuilding', 'address');
        $this->dropColumn('newbuilding', 'district');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('newbuilding', 'address', $this->string(200));
        $this->addColumn('newbuilding', 'district', $this->string(200));
    }
}
