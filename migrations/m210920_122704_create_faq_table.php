<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq}}`.
 */
class m210920_122704_create_faq_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200),
            'text' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq}}');
    }
}
