<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%application}}`.
 */
class m240409_101054_add_report_act_columns_to_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%application}}', 'report_act_provided', $this->integer(1)->after('ddu_matcap_paydate')->defaultValue(0)->comment('the flag indicates if the report-act has been provided by the agent'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'report_act_provided');
    }
}
