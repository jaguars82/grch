<?php

use yii\db\Migration;

/**
 * Class m240401_150120_add_fields_and_tables_for_renewed_reservation
 */
class m240401_150120_add_fields_and_tables_for_renewed_reservation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%application_document}}', [
            'id' => $this->primaryKey(),
            'application_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'category' => $this->integer(2)->notNull()->comment('a digital index that defines a document\'s category (corresponding categories are listed in the model)'),
            'file' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNUll(),
            'size' => $this->double(),
            'filetype' => $this->string(10)->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        // creates index for column `application_id` for table `application_document`
        $this->createIndex(
            '{{%idx-application_document-application_id}}',
            '{{%application_document}}',
            'application_id'
        );

        // add foreign key for table `{{%application_document}}`
        $this->addForeignKey(
            '{{%fk-application_document-application_id}}',
            '{{%application_document}}',
            'application_id',
            '{{%application}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id` for table `{{%application_document}}`
        $this->createIndex(
            '{{%idx-application_document-user_id}}',
            '{{%application_document}}',
            'user_id'
        );

        // add foreign key for table `{{%application_document}}`
        $this->addForeignKey(
            '{{%fk-application_document-user_id}}',
            '{{%application_document}}',
            'user_id',
            '{{%user}}',
            'id',
            'NO ACTION'
        );

        $this->addColumn('{{%application}}', 'is_toll', $this->integer(1)->after('is_active')->defaultValue(0)->comment('the flag indicates if the reservation is paid (1) or is free (0)'));
        $this->addColumn('{{%application}}', 'receipt_provided', $this->integer(1)->after('is_toll')->defaultValue(0)->comment('the flag indicates if the pay document (reciept) has been provided by the agent'));
        $this->addColumn('{{%application}}', 'ddu_provided', $this->integer(1)->after('receipt_provided')->defaultValue(0)->comment('the flag indicates if the agreement has been provided by the agent'));
        $this->addColumn('{{%application}}', 'ddu_price', $this->decimal(12, 2)->after('ddu_provided')->defaultValue(0.00));
        $this->addColumn('{{%application}}', 'ddu_cash', $this->decimal(12, 2)->after('ddu_price')->defaultValue(0.00));
        $this->addColumn('{{%application}}', 'ddu_mortgage', $this->decimal(12, 2)->after('ddu_cash')->defaultValue(0.00));
        $this->addColumn('{{%application}}', 'ddu_matcap', $this->decimal(12, 2)->after('ddu_mortgage')->defaultValue(0.00));
        $this->addColumn('{{%application}}', 'ddu_cash_paydate', $this->date()->after('ddu_matcap')->defaultValue(null));
        $this->addColumn('{{%application}}', 'ddu_mortgage_paydate', $this->date()->after('ddu_cash_paydate')->defaultValue(null));
        $this->addColumn('{{%application}}', 'ddu_matcap_paydate', $this->date()->after('ddu_mortgage_paydate')->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%application}}', 'ddu_matcap_paydate');
        $this->dropColumn('{{%application}}', 'ddu_mortgage_paydate');
        $this->dropColumn('{{%application}}', 'ddu_cash_paydate');
        $this->dropColumn('{{%application}}', 'ddu_matcap');
        $this->dropColumn('{{%application}}', 'ddu_mortgage');
        $this->dropColumn('{{%application}}', 'ddu_cash');
        $this->dropColumn('{{%application}}', 'ddu_price');
        $this->dropColumn('{{%application}}', 'ddu_provided');
        $this->dropColumn('{{%application}}', 'receipt_provided');
        $this->dropColumn('{{%application}}', 'is_toll');

        // drops foreign key for table `{{%application_document}}`
        $this->dropForeignKey(
            '{{%fk-application_document-user_id}}',
            '{{%application_document}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-application_document-user_id}}',
            '{{%application_document}}'
        );

        // drops foreign key for table `{{%application_document}}`
        $this->dropForeignKey(
            '{{%fk-application_document-application_id}}',
            '{{%application_document}}'
        );

        // drops index for column `application_id`
        $this->dropIndex(
            '{{%idx-application_document-application_id}}',
            '{{%application_document}}'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240401_150120_add_fields_and_tables_for_renewed_reservation cannot be reverted.\n";

        return false;
    }
    */
}
