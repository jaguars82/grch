<?php

use yii\db\Migration;

/**
 * Class m220128_131851_add_image_field_to_user
 */
class m220128_131851_add_image_field_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
        $this->addColumn('user', 'photo', $this->string(200));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'photo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220128_131851_add_image_field_to_user cannot be reverted.\n";

        return false;
    }
    */
}
