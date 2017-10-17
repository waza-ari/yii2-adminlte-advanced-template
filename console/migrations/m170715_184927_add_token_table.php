<?php

use yii\db\Migration;

class m170715_184927_add_token_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'valid_until' => $this->integer(),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-token-user_id', '{{%token}}', 'user_id');

        $this->addForeignKey(
            'fk-token-user_id',
            '{{%token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('idx-token-user_id', '{{%token}}');
        $this->dropIndex('idx-token-user_id', '{{%token}}');
        $this->dropTable('{{%user}}');
    }
}
