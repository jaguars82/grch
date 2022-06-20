<?php

use yii\db\Migration;

/**
 * Class m220620_111719_alter_table_action_data
 */
class m220620_111719_alter_table_action_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%action_data}}', 'discount', $this->integer(10)->defaultValue(0)->notNull());
        $this->addColumn('{{%action_data}}', 'discount_type', $this->smallInteger(1)->after('flat_filter')->defaultValue(0)->notNull());
        $this->addColumn('{{%action_data}}', 'discount_amount', $this->decimal(12,2)->after('discount')->defaultValue(null));
        $this->addColumn('{{%action_data}}', 'discount_price', $this->decimal(12,2)->after('discount_amount')->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%action_data}}', 'discount_price');
        $this->dropColumn('{{%action_data}}', 'discount_amount');
        $this->dropColumn('{{%action_data}}', 'discount_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_111719_alter_table_action_data cannot be reverted.\n";

        return false;
    }
    */
}
