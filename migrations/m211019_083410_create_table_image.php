<?php

use yii\db\Migration;

/**
 * Class m211019_083410_create_table_image
 */
class m211019_083410_create_table_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('image', [
            'id' => $this->primaryKey(),
            'file' => $this->string(200)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropTable('image');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_083410_create_table_image cannot be reverted.\n";

        return false;
    }
    */
}
