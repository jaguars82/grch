<?php

use yii\db\Migration;

/**
 * Class m220713_080959_alter_city_table_add_index_and_foreign_key_for_region_table
 */
class m220713_080959_alter_city_table_add_index_and_foreign_key_for_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-city-region_id',
            '{{%city}}',
            'region_id'
        );

        $this->addForeignKey(
            '{{%fk-city-region_id}}',
            '{{%city}}',
            'region_id',
            '{{%region}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-city-region_id}}',
            '{{%city}}',
        );

        $this->dropIndex(
            'idx-city-region_id',
            '{{%city}}',
        );
    }

}
