<?php

use yii\db\Migration;

/**
 * Class m211029_105814_add_action_flat_filter
 */
class m211029_105814_add_action_flat_filter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('action_data', 'flat_filter', $this->text());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('action_data', 'flat_filter');

    }
}
