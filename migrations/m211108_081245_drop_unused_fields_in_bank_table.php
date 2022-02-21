<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%unused_fields_in_bank}}`.
 */
class m211108_081245_drop_unused_fields_in_bank_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('bank', 'yearly_rate');
        $this->dropColumn('bank', 'initial_fee_rate');
        $this->dropColumn('bank', 'payment_period');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('bank', 'yearly_rate', $this->float()->notNull());
        $this->addColumn('bank', 'initial_fee_rate', $this->float()->notNull());
        $this->addColumn('bank', 'payment_period', $this->integer()->notNull());
    }
}
