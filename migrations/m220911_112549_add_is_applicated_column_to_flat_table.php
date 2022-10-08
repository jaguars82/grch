<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m220911_112549_add_is_applicated_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'is_applicated', $this->integer(1)->after('status')->defaultValue(0)->notNull()->comment('flag shows if flat has an active application'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flat}}', 'is_applicated');
    }
}
