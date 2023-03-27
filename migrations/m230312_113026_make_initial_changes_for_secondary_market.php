<?php

use yii\db\Migration;

/**
 * Class m230312_113026_make_initial_changes_for_secondary_market
 */
class m230312_113026_make_initial_changes_for_secondary_market extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%secondary_import}}', [
            'id' => $this->primaryKey(),
            'algorithm' => $this->text()->notNull(),
            'endpoint' => $this->text(),
            'imported_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createTable('{{%secondary_advertisement}}', [
            'id' => $this->primaryKey(),
            'external_id' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('advertisement ID in external source (e.g. in feed)'),
            'deal_type' => $this->integer(1)->comment('1 - sell, 2 - rent'),
            'deal_status_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about deal from external sources (e.g. from feed) or manually added'),
            'agency_id' => $this->integer(),
            'creation_type' => $this->integer(1)->comment('1 - created from feed, 2 - created manually via a webform'),
            'author_id' => $this->integer()->defaultValue(null),
            'author_info' => $this->json()->comment('information about the author from external sources (e.g. from feed)'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('advertisement status flag'),
            'expiration_date' => $this->timestamp()->defaultValue(null),
            'is_expired' => $this->boolean()->defaultValue(false),
            'is_prolongated' => $this->boolean()->defaultValue(false),
            'last_prolongation_date' => $this->timestamp()->defaultValue(null),
            'creation_date' => $this->timestamp()->defaultValue(null)->comment('actual creation date (e.g. from outer source)'),
            'last_update_date' => $this->timestamp()->defaultValue(null)->comment('actual update date (e.g. from outer source)'),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createTable('{{%secondary_room}}', [
            'id' => $this->primaryKey(),
            'advertisement_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "secondary_advertisement" table'),
            'category_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "secondary_category" table'),
            'category_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about room category from external sources (e.g. from feed) or manually added'),
            'property_type_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "secondary_property_type" table'),
            'property_type_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about property type from external sources (e.g. from feed) or manually added'),
            'building_series_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "secondary_building_series" table'),
            'building_series_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about building series from external sources (e.g. from feed) or manually added'),
            'newbuilding_complex_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "newbuilding_complex" table'),
            'newbuilding_complex_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about newbuilding complex (e.g. from feed) or manually added'),
            'newbuilding_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "newbuilding" table'),
            'entrance_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "entrance" table'),
            'flat_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "flat" table'),
            'number' => $this->integer()->comment('digital part of flat number'),
            'number_suffix' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('additional part of flat number (if exists)'),
            'price' => $this->decimal(12, 2),
            'unit_price' => $this->decimal(12, 2),
            'mortgage' => $this->boolean(),
            'layout' => $this->string(200)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'detail' => $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'),
            'special_conditions' => $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'),
            'area' => $this->float(),
            'kitchen_area' => $this->float(),
            'living_area' => $this->float(),
            'ceiling_height' => $this->float(),
            'rooms' => $this->integer(),
            'balcony_info' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'balcony_amount' => $this->integer(2),
            'loggia_amount' => $this->integer(2),
            'windowview_info' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'windowview_street' => $this->boolean(),
            'windowview_yard' => $this->boolean(),
            'dressing_room' => $this->boolean(),
            'panoramic_windows' => $this->boolean(),
            'is_studio' => $this->boolean(),
            'is_euro' => $this->boolean(),
            'built_year' => $this->integer(4),
            'renovation_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "secondary_renovation" table'),
            'renovation_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about renovation from external sources (e.g. from feed) or manually added'),
            'quality_index' => $this->integer(2)->defaultValue(null),
            'quality_string' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about quality from external sources (e.g. from feed) or manually added'),
            'floor' => $this->integer(),
            'section' => $this->integer(),
            'total_floors' => $this->integer(),
            'material_id' => $this->integer()->defaultValue(null)->comment('foreign key for linking with "building_material" table'),
            'material' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'elevator' => $this->boolean(),
            'elevator_info' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'elevator_passenger_amount' => $this->integer(2),
            'elevator_freight_amount' => $this->integer(2),
            'rubbish_chute' => $this->boolean(),
            'gas_pipe' => $this->boolean(),
            'phone_line' => $this->boolean(),
            'internet' => $this->boolean(),
            'bathroom_index' => $this->integer(2)->defaultValue(null),
            'bathroom_unit' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'concierge' => $this->boolean(),
            'closed_territory' => $this->boolean(),
            'playground' => $this->boolean(),
            'underground_parking' => $this->boolean(),
            'ground_parking' => $this->boolean(),
            'open_parking' => $this->boolean(),
            'multilevel_parking' => $this->boolean(),
            'parking_info' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('information about parking from external sources (e.g. from feed)'),
            'barrier' => $this->boolean(),
            'longitude' => $this->decimal(8, 6),
            'latitude' => $this->decimal(8, 6),
            'azimuth'=> $this->integer(),
            'region_id' => $this->integer()->defaultValue(null),
            'region_district_id' => $this->integer()->defaultValue(null),
            'city_id' => $this->integer()->defaultValue(null),
            'district_id' => $this->integer()->defaultValue(null),
            'street_type_id' => $this->integer()->defaultValue(null),
            'street_name' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'building_number' => $this->string(20)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'address' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'location_info' => $this->json()->comment('information about room location from external sources (e.g. from feed)'),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createTable('{{%secondary_category}}', [
            'id' => $this->primaryKey(),
            'level' => $this->integer(2)->comment('1 - root level of category tree, 2 - second level'),
            'parent_id' => $this->integer()->comment('id of a parent (level 1) category for level 2 items'),
            'name' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'alias' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('alternative name (e.g. in latin or from outer source (feed))'),
        ]);

        $this->createTable('{{%secondary_property_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'alias' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('alternative name (e.g. in latin or from outer source (feed))'),
        ]);

        $this->createTable('{{%secondary_renovation}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'alias' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('alternative name (e.g. in latin or from outer source (feed))'),
            'detail' => $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'),
        ]);

        $this->createTable('{{%secondary_building_series}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
            'alias' => $this->string()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->comment('alternative name (e.g. in latin or from outer source (feed))'),
            'detail' => $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL'),
        ]);

        $this->createTable('{{%building_material}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'alias' => $this->string()->comment('alternative name (e.g. in latin or from outer source (feed))'),
            'detail' => $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'),
        ]);

        $this->createTable('{{%secondary_room_image}}', [
            'id' => $this->primaryKey(),
            'secondary_room_id' => $this->integer(),
            'location_type' => $this->string(10)->comment('"local" - if the image is on local server, "remote" - if the image is on remote server'),
            'url' => $this->string(2000)->comment('image url for remote files'),
            'filename' => $this->string()->comment('image filename for local files')
        ]);

        $this->addColumn('{{%agency}}', 'import_id', $this->integer()->after('contact_id')->defaultValue(null));

        // creates index for column `import_id` for table `{{%agency}}`
        $this->createIndex(
            '{{%idx-agency-import_id}}',
            '{{%agency}}',
            'import_id'
        );

        // add foreign key for table `{{%agency}}`
        $this->addForeignKey(
            '{{%fk-agency-import_id}}',
            '{{%agency}}',
            'import_id',
            '{{%secondary_import}}',
            'id',
            'SET NULL'
        );

        // creates index for column `agency_id` for table `{{%secondary_advertisement}}`
        $this->createIndex(
            '{{%idx-secondary_advertisement-agency_id}}',
            '{{%secondary_advertisement}}',
            'agency_id'
        );

        // add foreign key for table `{{%secondary_advertisement}}`
        $this->addForeignKey(
            '{{%fk-secondary_advertisement-agency_id}}',
            '{{%secondary_advertisement}}',
            'agency_id',
            '{{%agency}}',
            'id',
            'CASCADE'
        );

        // creates index for column `author_id` for table `{{%secondary_advertisement}}`
        $this->createIndex(
            '{{%idx-secondary_advertisement-author_id}}',
            '{{%secondary_advertisement}}',
            'author_id'
        );

        // add foreign key for table `{{%secondary_advertisement}}`
        $this->addForeignKey(
            '{{%fk-secondary_advertisement-author_id}}',
            '{{%secondary_advertisement}}',
            'author_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );

        // creates index for column `advertisement_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-advertisement_id}}',
            '{{%secondary_room}}',
            'advertisement_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-advertisement_id}}',
            '{{%secondary_room}}',
            'advertisement_id',
            '{{%secondary_advertisement}}',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-category_id}}',
            '{{%secondary_room}}',
            'category_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-category_id}}',
            '{{%secondary_room}}',
            'category_id',
            '{{%secondary_category}}',
            'id',
            'SET NULL'
        );

        // creates index for column `property_type_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-property_type_id}}',
            '{{%secondary_room}}',
            'property_type_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-property_type_id}}',
            '{{%secondary_room}}',
            'property_type_id',
            '{{%secondary_property_type}}',
            'id',
            'SET NULL'
        );

        // creates index for column `building_series_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-building_series_id}}',
            '{{%secondary_room}}',
            'building_series_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-building_series_id}}',
            '{{%secondary_room}}',
            'building_series_id',
            '{{%secondary_building_series}}',
            'id',
            'SET NULL'
        );

        // creates index for column `material_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-material_id}}',
            '{{%secondary_room}}',
            'material_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-material_id}}',
            '{{%secondary_room}}',
            'material_id',
            '{{%building_material}}',
            'id',
            'SET NULL'
        );

        // creates index for column `newbuilding_complex_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-newbuilding_complex_id}}',
            '{{%secondary_room}}',
            'newbuilding_complex_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-newbuilding_complex_id}}',
            '{{%secondary_room}}',
            'newbuilding_complex_id',
            '{{%newbuilding_complex}}',
            'id',
            'SET NULL'
        );

        // creates index for column `newbuilding_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-newbuilding_id}}',
            '{{%secondary_room}}',
            'newbuilding_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-newbuilding_id}}',
            '{{%secondary_room}}',
            'newbuilding_id',
            '{{%newbuilding}}',
            'id',
            'SET NULL'
        );

        // creates index for column `entrance_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-entrance_id}}',
            '{{%secondary_room}}',
            'entrance_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-entrance_id}}',
            '{{%secondary_room}}',
            'entrance_id',
            '{{%entrance}}',
            'id',
            'SET NULL'
        );

        // creates index for column `flat_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-flat_id}}',
            '{{%secondary_room}}',
            'flat_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-flat_id}}',
            '{{%secondary_room}}',
            'flat_id',
            '{{%flat}}',
            'id',
            'SET NULL'
        );

        // creates index for column `renovation_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-renovation_id}}',
            '{{%secondary_room}}',
            'renovation_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-renovation_id}}',
            '{{%secondary_room}}',
            'renovation_id',
            '{{%secondary_renovation}}',
            'id',
            'SET NULL'
        );

        // creates index for column `region_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-region_id}}',
            '{{%secondary_room}}',
            'region_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-region_id}}',
            '{{%secondary_room}}',
            'region_id',
            '{{%region}}',
            'id',
            'SET NULL'
        );

        // creates index for column `region_district_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-region_district_id}}',
            '{{%secondary_room}}',
            'region_district_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-region_district_id}}',
            '{{%secondary_room}}',
            'region_district_id',
            '{{%region_district}}',
            'id',
            'SET NULL'
        );

        // creates index for column `city_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-city_id}}',
            '{{%secondary_room}}',
            'city_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-city_id}}',
            '{{%secondary_room}}',
            'city_id',
            '{{%city}}',
            'id',
            'SET NULL'
        );

        // creates index for column `district_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-district_id}}',
            '{{%secondary_room}}',
            'district_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-district_id}}',
            '{{%secondary_room}}',
            'district_id',
            '{{%district}}',
            'id',
            'SET NULL'
        );

        // creates index for column `street_type_id` for table `{{%secondary_room}}`
        $this->createIndex(
            '{{%idx-secondary_room-street_type_id}}',
            '{{%secondary_room}}',
            'street_type_id'
        );

        // add foreign key for table `{{%secondary_room}}`
        $this->addForeignKey(
            '{{%fk-secondary_room-street_type_id}}',
            '{{%secondary_room}}',
            'street_type_id',
            '{{%street_type}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-secondary_room-street_type_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-street_type_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-district_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-district_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-city_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-city_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-region_district_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-region_district_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-region_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-region_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-renovation_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-renovation_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-flat_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-flat_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-entrance_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-entrance_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-newbuilding_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-newbuilding_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-newbuilding_complex_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-newbuilding_complex_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-material_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-material_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-building_series_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-building_series_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-property_type_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-property_type_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-category_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-category_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_room-advertisement_id}}',
            '{{%secondary_room}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_room-advertisement_id}}',
            '{{%secondary_room}}',
        );

        $this->dropForeignKey(
            '{{%fk-secondary_advertisement-author_id}}',
            '{{%secondary_advertisement}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_advertisement-author_id}}',
            '{{%secondary_advertisement}}',
        );
        
        $this->dropForeignKey(
            '{{%fk-secondary_advertisement-agency_id}}',
            '{{%secondary_advertisement}}',
        );

        $this->dropIndex(
            '{{%idx-secondary_advertisement-agency_id}}',
            '{{%secondary_advertisement}}',
        );

        $this->dropForeignKey(
            '{{%fk-agency-import_id}}',
            '{{%agency}}',
        );

        $this->dropIndex(
            '{{%idx-agency-import_id}}',
            '{{%agency}}',
        );

        $this->dropColumn('{{%agency}}', 'import_id');

        $this->dropTable('{{%secondary_room_image}}');
        $this->dropTable('{{%building_material}}');
        $this->dropTable('{{%secondary_building_series}}');
        $this->dropTable('{{%secondary_renovation}}');
        $this->dropTable('{{%secondary_property_type}}');
        $this->dropTable('{{%secondary_category}}');
        $this->dropTable('{{%secondary_room}}');
        $this->dropTable('{{%secondary_advertisement}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230312_113026_make_initial_changes_for_secondary_market cannot be reverted.\n";

        return false;
    }
    */
}
