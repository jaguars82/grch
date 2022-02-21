<?php

use yii\db\Migration;

/**
 * Class m210916_142728_add_sort_column_to_developer
 */
class m210916_142728_add_sort_column_to_developer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('developer', 'sort', $this->integer()->defaultValue(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('developer', 'sort');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210916_142728_add_sort_column_to_developer cannot be reverted.\n";

        return false;
    }
    */
}
