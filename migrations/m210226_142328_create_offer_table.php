<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%offer}}`.
 */
class m210226_142328_create_offer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%offer}}', [
            'id' => $this->primaryKey(),
            'flat_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'new_price_cash' => $this->decimal(12, 2),
            'new_price_credit' => $this->decimal(12, 2),
            'url' => $this->string(200),
            'settings' => $this->text(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'visited_at' => $this->timestamp()->defaultValue(null),
        ]);
        
        // creates index for column `flat_id` for table `{{%offer}}`
        $this->createIndex(
            '{{%idx-offer-flat_id}}',
            '{{%offer}}',
            'flat_id'
        );

        // add foreign key for table `{{%offer}}`
        $this->addForeignKey(
            '{{%fk-offer-flat_id}}',
            '{{%offer}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `user_id` for table `{{%offer}}`
        $this->createIndex(
            '{{%idx-offer-user_id}}',
            '{{%offer}}',
            'user_id'
        );

        // add foreign key for table `{{%offer}}`
        $this->addForeignKey(
            '{{%fk-offer-user_id}}',
            '{{%offer}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%offer}}`
        $this->dropForeignKey(
            '{{%fk-offer-user_id}}',
            '{{%offer}}'
        );

        // drops index for column `user_id` for table `{{%offer}}`
        $this->dropIndex(
            '{{%idx-offer-user_id}}',
            '{{%offer}}'
        );
        
        // drops foreign key for table `{{%offer}}`
        $this->dropForeignKey(
            '{{%fk-offer-flat_id}}',
            '{{%offer}}'
        );

        // drops index for column `flat_id` for table `{{%offer}}`
        $this->dropIndex(
            '{{%idx-offer-flat_id}}',
            '{{%offer}}'
        );
        
        $this->dropTable('{{%offer}}');
    }
}
