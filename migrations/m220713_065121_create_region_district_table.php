<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%region_district}}`.
 */
class m220713_065121_create_region_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%region_district}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200),
            'region_id' => $this->integer(),
        ]);

        $this->createIndex(
            'idx-region_district-region_id',
            '{{%region_district}}',
            'region_id'
        );

        $this->addForeignKey(
            '{{%fk-region_district-region_id}}',
            '{{%region_district}}',
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
            '{{%fk-region_district-region_id}}',
            '{{%region_district}}',
        );

        $this->dropIndex(
            'idx-region_district-region_id',
            '{{%region_district}}',
        );

        $this->dropTable('{{%region_district}}');
    }
}
