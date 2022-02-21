<?php

use yii\db\Migration;

/**
 * Class m211021_072549_add_svg_coords_field_to_flat
 */
class m211021_072549_add_svg_coords_field_to_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'layout_coords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'layout_coords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_072549_add_svg_coords_field_to_flat cannot be reverted.\n";

        return false;
    }
    */
}
