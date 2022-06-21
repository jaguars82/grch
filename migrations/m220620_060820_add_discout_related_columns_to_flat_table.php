<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m220620_060820_add_discout_related_columns_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'discount_type', $this->smallInteger(1)->after('unit_price_cash')->defaultValue(0)->notNull());
        $this->addColumn('{{%flat}}', 'discount_amount', $this->decimal(12,2)->after('discount')->defaultValue(null));
        $this->addColumn('{{%flat}}', 'discount_price', $this->decimal(12,2)->after('discount_amount')->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flat}}', 'discount_price');
        $this->dropColumn('{{%flat}}', 'discount_amount');
        $this->dropColumn('{{%flat}}', 'discount_type');
    }
}
