<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%secondary_room_fee}}`.
 */
class m250323_151848_create_secondary_room_fee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%secondary_room_fee}}', [
            'id' => $this->primaryKey(),
            'secondary_room_id' => $this->integer()->notNull(),
            'created_user_id' => $this->integer()->notNull(),
            'updated_user_id' => $this->integer()->defaultValue(null),
            'fee_type' => $this->integer(2)->notNull(),
            'fee_percent' => $this->decimal(5,2)->defaultValue(null),
            'fee_amount' => $this->decimal(8,2)->defaultValue(null),
            'has_expiration_date' => $this->boolean()->defaultValue(0),
            'expires_at' => $this->dateTime()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-secondary_room_fee-secondary_room_id', '{{%secondary_room_fee}}', 'secondary_room_id');
        $this->createIndex('idx-secondary_room_fee-created_user_id', '{{%secondary_room_fee}}', 'created_user_id');
        $this->createIndex('idx-secondary_room_fee-updated_user_id', '{{%secondary_room_fee}}', 'updated_user_id');

        $this->addForeignKey('fk-secondary_room_fee-secondary_room_id', '{{%secondary_room_fee}}', 'secondary_room_id', '{{%secondary_room}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-secondary_room_fee-created_user_id', '{{%secondary_room_fee}}', 'created_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-secondary_room_fee-updated_user_id', '{{%secondary_room_fee}}', 'updated_user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-secondary_room_fee-secondary_room_id', '{{%secondary_room_fee}}');
        $this->dropForeignKey('fk-secondary_room_fee-created_user_id', '{{%secondary_room_fee}}');
        $this->dropForeignKey('fk-secondary_room_fee-updated_user_id', '{{%secondary_room_fee}}');

        $this->dropIndex('idx-secondary_room_fee-secondary_room_id', '{{%secondary_room_fee}}');
        $this->dropIndex('idx-secondary_room_fee-created_user_id', '{{%secondary_room_fee}}');
        $this->dropIndex('idx-secondary_room_fee-updated_user_id', '{{%secondary_room_fee}}');

        $this->dropTable('{{%secondary_room_fee}}');
    }
}
