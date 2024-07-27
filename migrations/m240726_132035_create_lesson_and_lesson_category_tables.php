<?php

use yii\db\Migration;

/**
 * Class m240726_132035_create_lesson_and_lesson_category_tables
 */
class m240726_132035_create_lesson_and_lesson_category_tables extends Migration
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

        $this->createTable('{{%lesson_category}}', [
            'id' => $this->primaryKey(),
            'parent_category_id' => $this->integer()->defaultValue(NULL)->comment('The id of parent category, NULL - for categories in the root'),
            'sort_order' => $this->integer(5)->defaultValue(NULL)->comment('Position of the category in the list'),
            'title' => $this->string(255),
            'subtitle' => $this->string(255),
            'description' => $this->text(),
            'image' => $this->string(55)->comment('filename of categorie\'s tumbnail'),
            'icon' => $this->string(25)->comment('categorie\'s pictogram'),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        $this->createTable('{{%lesson}}', [
            'id' => $this->primaryKey(),
            'lesson_category_id' => $this->integer()->defaultValue(NULL),
            'sort_order' => $this->integer(5)->defaultValue(NULL)->comment('Position of the lesson in the list'),
            'image' => $this->string(55)->comment('filename of lesson\'s tumbnail'),
            'icon' => $this->string(25)->comment('lesson\'s pictogram'),
            'title' => $this->string(255),
            'subtitle' => $this->string(255),
            'description' => $this->text(),
            'content' => $this->text(),
            'videohosting_type' => $this->integer(2),
            'video_source' => $this->string(255)->comment('url of the video on videohosting or the name of the file on local server'),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        // creates index for column `lesson_category_id` for table `{{%lesson}}`
        $this->createIndex(
            '{{%idx-lesson-lesson_category_id}}',
            '{{%lesson}}',
            'lesson_category_id'
        );

        // add foreign key for table `{{%lesson}}`
        $this->addForeignKey(
            '{{%fk-lesson-lesson_category_id}}',
            '{{%lesson}}',
            'lesson_category_id',
            '{{%lesson_category}}',
            'id',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%lesson}}`
        $this->dropForeignKey(
            '{{%fk-lesson-lesson_category_id}}',
            '{{%lesson}}'
        );

        // drops index for column `lesson_category_id` for table `{{%lesson}}`
        $this->dropIndex(
            '{{%idx-lesson-lesson_category_id}}',
            '{{%lesson}}'
        );

        $this->dropTable('{{%lesson}}');
        $this->dropTable('{{%lesson_category}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240726_132035_create_lesson_and_lesson_category_tables cannot be reverted.\n";

        return false;
    }
    */
}
