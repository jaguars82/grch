<?php

use yii\db\Migration;

/**
 * Class m250309_142353_add_price_change_and_area_change_tables
 */
class m250309_142353_add_price_change_and_area_change_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create 'price_change' table
        $this->createTable('{{%price_change}}', [
            'id' => $this->primaryKey(),
            'flat_id' => $this->integer()->notNull(),
            'price_cash' => $this->decimal(12,2)->defaultValue(null),
            'unit_price_cash' => $this->decimal(12,2)->defaultValue(null),
            'price_credit' => $this->decimal(12,2)->defaultValue(null),
            'unit_price_credit' => $this->decimal(12,2)->defaultValue(null),
            'area_snapshot' => $this->float(10,2)->defaultValue(null),
            'is_initial' => $this->boolean()->defaultValue(false)->comment('indicates, if this is the first entry for current flat'),
            'price_updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create 'area_change' table
        $this->createTable('{{%area_change}}', [
            'id' => $this->primaryKey(),
            'flat_id' => $this->integer()->notNull(),
            'area' => $this->float(10,2)->defaultValue(null),
            'is_initial' => $this->boolean()->defaultValue(false)->comment('indicates, if this is the first entry for current flat'),
            'area_updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create an index for 'flat_id' in 'price_change'
        $this->createIndex(
            'idx-price_change-flat_id',
            '{{%price_change}}',
            'flat_id'
        );

        // Create an index for 'flat_id' in 'area_change'
        $this->createIndex(
            'idx-area_change-flat_id',
            '{{%area_change}}',
            'flat_id'
        );

        // Add a foreign key for 'price_change'
        $this->addForeignKey(
            'fk-price_change-flat_id',
            '{{%price_change}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add a foreign key for 'area_change'
        $this->addForeignKey(
            'fk-area_change-flat_id',
            '{{%area_change}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys
        $this->dropForeignKey('fk-price_change-flat_id', '{{%price_change}}');
        $this->dropForeignKey('fk-area_change-flat_id', '{{%area_change}}');

        // Drop indexes
        $this->dropIndex('idx-price_change-flat_id', '{{%price_change}}');
        $this->dropIndex('idx-area_change-flat_id', '{{%area_change}}');
        
        // Drop tables
        $this->dropTable('{{%price_change}}');
        $this->dropTable('{{%area_change}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250309_142353_add_price_change_and_area_change_tables cannot be reverted.\n";

        return false;
    }
    */
}
