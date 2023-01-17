<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariff}}`.
 */
class m230115_121521_create_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tariff}}', [
            'id' => $this->primaryKey(),
            'tariff_table' => $this->json(),
            'changes' => $this->text()->append('CHARACTER SET utf8 COLLATE utf8_general_ci NULL'),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tariff}}');
    }
}
