<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%flat}}`.
 */
class m220911_151001_add_sold_by_application_column_to_flat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flat}}', 'sold_by_application', $this->integer(1)->after('status')->defaultValue(0)->notNull()->comment('flag shows if flat has been sold by application'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flat}}', 'sold_by_application');
    }
}
