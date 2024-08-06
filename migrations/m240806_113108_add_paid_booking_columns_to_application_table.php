<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m240806_113108_add_paid_booking_columns_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'book_payment_provided', $this->boolean()->after('is_toll')->defaultValue(false));
        $this->addColumn('{{%application}}', 'book_payment_amount', $this->decimal(12, 2)->after('book_payment_provided')->defaultValue(0.00));
        $this->addColumn('{{%application}}', 'book_payment_way', $this->integer(2)->after('book_payment_amount')->defaultValue(null));
        $this->addColumn('{{%application}}', 'book_payment_date', $this->date()->after('book_payment_way')->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'book_payment_date');
        $this->dropColumn('{{%application}}', 'book_payment_way');
        $this->dropColumn('{{%application}}', 'book_payment_amount');
        $this->dropColumn('{{%application}}', 'book_payment_provided');
    }
}
