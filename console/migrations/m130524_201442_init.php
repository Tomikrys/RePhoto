<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    const TABLE_ON_UPDATE = 'no action';
    const TABLE_ON_DELETE = 'no action';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),

            'email' => $this->string()->notNull()->unique(),
            'email_verified' => $this->integer(1)->notNull()->defaultValue(0),

            'cookie_confirmed' => $this->integer(1)->notNull()->defaultValue(0),

            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(32)->null(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->integer(1)->notNull()->defaultValue(0),

            'fb_id' => $this->integer(32),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),

            'id_user' => $this->integer(11)->notNull(),
            'extension' => $this->string(32)->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_file_user', 'file', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%place}}', [
            'id' => $this->primaryKey(),

            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull()->defaultValue(""),

            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(11, 8)->notNull(),

            'id_user' => $this->integer(11)->notNull(),
            'id_category' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_place_user', 'place', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%photo}}', [
            'id' => $this->primaryKey(),

            'verified' => $this->integer(1)->notNull()->defaultValue(0),

            'captured_at' => $this->dateTime()->null(),

            'name' => $this->string(255)->notNull()->defaultValue(""),
            'description' => $this->text()->notNull()->defaultValue(""),

            'latitude' => $this->decimal(10, 8)->null(),
            'longitude' => $this->decimal(11, 8)->null(),

            'id_user' => $this->integer(11)->notNull(),
            'id_file' => $this->integer(11)->notNull(),
            'id_place' => $this->integer(11)->null(),


            'aligned' => $this->integer(1)->notNull()->defaultValue(0),
            'visible' => $this->integer(1)->notNull()->defaultValue(0),

            // TODO Photo info
            'exif_json' => $this->text()->null(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_user', 'photo', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_file', 'photo', 'id_file', 'file', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_place', 'photo', 'id_place', 'place', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),

            'name' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%photo_tag}}', [
            'id' => $this->primaryKey(),

            'id_photo' => $this->integer(11)->notNull(),
            'id_tag' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_tag_tag', 'photo_tag', 'id_tag', 'tag', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_tag_photo', 'photo_tag', 'id_photo', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%photo_like}}', [
            'id' => $this->primaryKey(),

            'id_photo' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_like_user', 'photo_like', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_like_photo', 'photo_like', 'id_photo', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%photo_save}}', [
            'id' => $this->primaryKey(),

            'id_photo' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_save_user', 'photo_save', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_save_photo', 'photo_save', 'id_photo', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);


        $this->createTable('{{%photo_comment}}', [
            'id' => $this->primaryKey(),

            'id_photo' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),
            'text' => $this->string(512)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_comment_user', 'photo_comment', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_comment_photo', 'photo_comment', 'id_photo', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%photo_edited}}', [
            'id' => $this->primaryKey(),

            'id_user' => $this->integer(11)->notNull(),
            'id_file' => $this->integer(11)->notNull(),
            'id_photo_1' => $this->integer(11)->notNull(),
            'id_photo_2' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_edited_user', 'photo_edited', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_edited_file', 'photo_edited', 'id_file', 'file', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_edited_photo_1', 'photo_edited', 'id_photo_1', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_edited_photo_2', 'photo_edited', 'id_photo_2', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%photo_wish_list}}', [
            'id' => $this->primaryKey(),

            'id_photo' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_photo_wish_list_user', 'photo_wish_list', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_photo_wish_list_photo', 'photo_wish_list', 'id_photo', 'photo', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);

        $this->createTable('{{%place_saved}}', [
            'id' => $this->primaryKey(),

            'id_place' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),

            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk_place_saved_user', 'place_saved', 'id_user', 'user', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);
        $this->addForeignKey('fk_place_saved_place', 'place_saved', 'id_place', 'place', 'id', self::TABLE_ON_DELETE, self::TABLE_ON_UPDATE);


        $this->execute(<<<SQL
          drop view if exists v_tag;
          create view v_tag as 
            select tag.id,
                   name,
                   count(tag.id) as tags_count
            from tag
            left join photo_tag on photo_tag.id_tag = tag.id
            group by tag.id
SQL
        );

        $this->execute(<<<SQL
          drop view if exists v_photo;
          create view v_photo as 
            select photo.*,
                   count(photo.id) as likes_count,
                   count(photo.id) as comments_count
            from photo
            left join photo_like on photo_like.id_photo = photo.id
            left join photo_comment on photo_comment.id_photo = photo.id
            group by photo.id
SQL
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_photo_wish_list_user', 'photo');
        $this->dropForeignKey('fk_photo_wish_list_photo', 'photo');
        $this->dropTable('{{%photo_wish_list}}');

        $this->dropForeignKey('fk_photo_user', 'photo');
        $this->dropTable('{{%photo}}');
        $this->dropTable('{{%user}}');
    }
}
