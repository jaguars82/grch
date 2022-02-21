<?php

use yii\db\Migration;

/**
 * Class m210305_103015_add_columns_for_offer
 */
class m210305_103015_add_columns_for_offer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agency', 'offer_info', $this->text());
        $this->addColumn('developer', 'offer_info', $this->text());
        $this->addColumn('newbuilding_complex', 'offer_info', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newbuilding_complex', 'offer_info');
        $this->dropColumn('developer', 'offer_info');
        $this->dropColumn('agency', 'offer_info');
    }
}
