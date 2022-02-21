<?php

use yii\db\Migration;

/**
 * Class m211026_100901_create_bank_tariff_table
 */
class m211026_100901_create_bank_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bank_tariff}}', [
            'id' => $this->primaryKey(),
            'bank_id' => $this->integer(),
            'name' => $this->string(200),
            'yearly_rate' => $this->float()->notNull(),
            'initial_fee_rate' => $this->float()->notNull(),
            'payment_period' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-bank-bank_id',
            '{{%bank_tariff}}',
            'bank_id'
        );

        $this->addForeignKey(
            '{{%fk-bank_tariff-bank_id}}',
            '{{%bank_tariff}}',
            'bank_id',
            '{{%bank}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-bank_tariff-bank_id}}',
            '{{%bank_tariff}}',
        );

        $this->dropIndex(
            'idx-bank-bank_id',
            '{{%bank_tariff}}',
        );

        $this->dropTable('{{%bank_tariff}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211026_100901_create_bank_tariff_talble cannot be reverted.\n";

        return false;
    }
    */
}
