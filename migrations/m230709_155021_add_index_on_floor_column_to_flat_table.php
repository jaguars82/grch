<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m230709_155021_add_index_on_floor_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'index_on_floor', $this->integer(3)->after('floor')->defaultValue(NULL)->comment('digital index corresponding to flat\'s order on the floor'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flat}}', 'index_on_floor');
    }
}
