<?php

use yii\db\Migration;

/**
 * Class m211019_083554_create_references
 */
class m211019_083554_create_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('region', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)
        ]);

        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)
        ]);

        $this->createTable('district', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200),
            'city_id' => $this->integer()
        ]);

        $this->createIndex(
            'idx-district-city_id',
            '{{%district}}',
            'city_id'
        );

        $this->addForeignKey(
            '{{%fk-district-city_id}}',
            '{{%district}}',
            'city_id',
            '{{%city}}',
            'id',
            'CASCADE'
        );

        $this->createTable('street_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)
        ]);

        $this->createTable('building_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)
        ]);

        $this->createTable('advantage', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('advantage');
        $this->dropTable('building_type');
        $this->dropTable('street_type');

        $this->dropForeignKey(
            '{{%fk-district-city_id}}',
            '{{%district}}'
        );
        $this->dropIndex(
            'idx-district-city_id',
            '{{%district}}'
        );
        
        $this->dropTable('district');
        $this->dropTable('city');
        $this->dropTable('region');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_083554_create_references cannot be reverted.\n";

        return false;
    }
    */
}
