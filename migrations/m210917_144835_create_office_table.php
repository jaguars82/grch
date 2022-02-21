<?php

use yii\db\Migration;

/**
 * Handles the creation of table `office`.
 */
class m210917_144835_create_office_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%office}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200),
            'address' => $this->string(200),
            'phones' => $this->text(),
            'comment' => $this->text(),
        ]);

        $this->createTable('{{%developer_office}}', [
            'developer_id' => $this->integer()->notNull(),
            'office_id' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            '{{%idx-developer_office-developer_id}}',
            '{{%developer_office}}',
            'developer_id'
        );

        $this->addForeignKey(
            '{{%fk-developer_office-developer_id}}',
            '{{%developer_office}}',
            'developer_id',
            '{{%developer}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-developer_office-office_id}}',
            '{{%developer_office}}',
            'office_id'
        );

        $this->addForeignKey(
            '{{%fk-developer_office-office_id}}',
            '{{%developer_office}}',
            'office_id',
            '{{%office}}',
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
            '{{%fk-developer_office-developer_id}}',
            '{{%developer_office}}'
        );

        $this->dropIndex(
            '{{%idx-developer_office-developer_id}}',
            '{{%developer_office}}'
        );


        $this->dropForeignKey(
            '{{%fk-developer_office-office_id}}',
            '{{%developer_office}}'
        );

        $this->dropIndex(
            '{{%idx-developer_office-office_id}}',
            '{{%developer_office}}'
        );

        $this->dropTable('{{%developer_office}}');
        $this->dropTable('{{%office}}');
    }
}
