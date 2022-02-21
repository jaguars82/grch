<?php

use yii\db\Migration;

/**
 * Class m201102_122521_create_banks_tables
 */
class m201102_122521_create_banks_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bank}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)->notNull()->unique(),
            'logo' => $this->string(200)->notNull()->unique(),
            'yearly_rate' => $this->float()->notNull(),
            'initial_fee_rate' => $this->float()->notNull(),
            'payment_period' => $this->integer()->notNull(),
        ]);
        
        $this->createTable('{{%bank_newbuilding_complex}}', [
            'newbuilding_complex_id' => $this->integer()->notNull(),
            'bank_id' => $this->integer()->notNull(),
        ]);
        
        // creates index for column `newbuilding_complex_id`
        $this->createIndex(
            '{{%idx-bank_newbuilding_complex-newbuilding_complex_id}}',
            '{{%bank_newbuilding_complex}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%bank_newbuilding_complex}}`
        $this->addForeignKey(
            '{{%fk-bank_newbuilding_complex-newbuilding_complex_id}}',
            '{{%bank_newbuilding_complex}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `bank_id`
        $this->createIndex(
            '{{%idx-bank_newbuilding_complex-bank_id}}',
            '{{%bank_newbuilding_complex}}',
            'bank_id'
        );

        // add foreign key for table `{{%bank}}`
        $this->addForeignKey(
            '{{%fk-bank_newbuilding_complex-bank_id}}',
            '{{%bank_newbuilding_complex}}',
            'bank_id',
            '{{%bank}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%bank}}`
        $this->dropForeignKey(
            '{{%fk-bank_newbuilding_complex-bank_id}}',
            '{{%bank_newbuilding_complex}}'
        );

        // drops index for column `bank_id`
        $this->dropIndex(
            '{{%idx-bank_newbuilding_complex-bank_id}}',
            '{{%bank_newbuilding_complex}}'
        );
        
        // drops foreign key for table `{{%bank_newbuilding_complex}}`
        $this->dropForeignKey(
            '{{%fk-bank_newbuilding_complex-newbuilding_complex_id}}',
            '{{%bank_newbuilding_complex}}'
        );

        // drops index for column `newbuilding_complex_id`
        $this->dropIndex(
            '{{%idx-bank_newbuilding_complex-newbuilding_complex_id}}',
            '{{%bank_newbuilding_complex}}'
        );
        
        $this->dropTable('{{%bank_newbuilding_complex}}');
        
        $this->dropTable('{{%bank}}');   
    }
}
