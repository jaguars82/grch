<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%street_type}}`.
 */
class m230615_094952_add_aliases_column_to_street_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%street_type}}', 'aliases', $this->string()->after('name')->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL'));

        $this->update('{{%street_type}}', ['aliases' => 'ул., ул'], ['name' => 'улица']);
        $this->update('{{%street_type}}', ['aliases' => 'пер., пер, п-к, пер-к, переул.'], ['name' => 'переулок']);
        $this->update('{{%street_type}}', ['aliases' => 'пр-т, п-т, просп.'], ['name' => 'проспект']);
        $this->update('{{%street_type}}', ['aliases' => 'б-р, б., бульв., бул-р'], ['name' => 'бульвар']);
        $this->update('{{%street_type}}', ['aliases' => 'наб., наб, набереж., н., бул-р'], ['name' => 'набережная']);
        $this->update('{{%street_type}}', ['aliases' => 'пл., пл, площ., п-д, площ-д'], ['name' => 'площадь']);

        $this->batchInsert('{{%street_type}}', ['name', 'short_name', 'aliases'], [
            ['проезд', 'пр.', 'пр., пр, пр-д, пр-зд, п-зд'],
            ['шоссе', 'ш.', 'ш., ш, шос., шосс.'],
            ['жилой массив', 'жм', 'жм, ж.м., ж-м, ж/м, ж\м, жил.м., жил.масс., жилмассив']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%street_type}}', ['name' => 'шоссе']);
        $this->delete('{{%street_type}}', ['name' => 'проезд']);

        $this->dropColumn('{{%street_type}}', 'aliases');
    }
}
