<?php

use yii\db\Migration;

/**
 * Class m240712_172700_add_column_developers_statistics_to_tariff_table
 */
class m240712_172700_add_column_developers_statistics_to_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tariff}}', 'developers_in_statistics', $this->json()->after('tariff_table')->defaultValue(NULL)->comment('array of developer\'s idies with deal statistics turned on'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tariff}}', 'developers_in_statistics');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240712_172700_add_columndevelopers_statistics_to_tariff_table cannot be reverted.\n";

        return false;
    }
    */
}
