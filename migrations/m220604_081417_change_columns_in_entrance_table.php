<?php

use yii\db\Migration;

/**
 * Class m220604_081417_change_columns_in_entrance_table
 */
class m220604_081417_change_columns_in_entrance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('entrance', 'name', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'));
        $this->alterColumn('entrance', 'material', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_081417_change_columns_in_entrance_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_081417_change_columns_in_entrance_table cannot be reverted.\n";

        return false;
    }
    */
}
