<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m220829_071207_add_application_number_column_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'application_number', $this->string(30)->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'application_number');
    }
}
