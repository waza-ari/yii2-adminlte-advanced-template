<?php

use common\models\User;
use yii\db\Migration;

class m170711_102952_create_admin_user extends Migration
{
    public function safeUp()
    {
        $user = new User();
        $user->username = 'admin';
        $user->setPassword('admin');
        $user->setIsNewRecord(true);
        $user->email = 'admin@example.com';
        $user->save();
    }

    public function safeDown()
    {
        User::findByUsername('admin')->delete();
    }

}
