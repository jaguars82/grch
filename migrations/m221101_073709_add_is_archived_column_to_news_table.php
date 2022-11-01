<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%news}}`.
 */
class m221101_073709_add_is_archived_column_to_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'is_archived', $this->integer(1)->after('search_link')->defaultValue(0)->notNull()->comment('flag shows if news or action is no more actual'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%news}}', 'is_archived');
    }
}
