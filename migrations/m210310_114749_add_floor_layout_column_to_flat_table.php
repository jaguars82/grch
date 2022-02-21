<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m210310_114749_add_floor_layout_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'floor_layout', $this->string(200));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'floor_layout');
    }
}
