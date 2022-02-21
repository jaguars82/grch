<?php

use yii\db\Migration;

/**
 * Class m220121_122610_add_icon_field_to_advantage_table
 */
class m220121_122610_add_icon_field_to_advantage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('advantage', 'icon', $this->string(200)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('advantage', 'icon');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_122610_add_icon_field_to_advantage_table cannot be reverted.\n";

        return false;
    }
    */
}
