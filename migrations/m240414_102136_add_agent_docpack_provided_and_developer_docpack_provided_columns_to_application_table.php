<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m240414_102136_add_agent_docpack_provided_and_developer_docpack_provided_columns_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'agent_docpack_provided', $this->integer(1)->after('report_act_provided')->defaultValue(0)->comment('the flag indicates if the agent has uploded a pack of documents while taking application in work'));
        $this->addColumn('{{%application}}', 'developer_docpack_provided', $this->integer(1)->after('agent_docpack_provided')->defaultValue(0)->comment('the flag indicates if the developer has uploded a pack of documents for the agent'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'developer_docpack_provided');
        $this->dropColumn('{{%application}}', 'agent_docpack_provided');
    }
}
