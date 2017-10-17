<?php

use yii\db\Migration;

class m171017_093421_assign_initial_roles extends Migration
{
    public function safeUp()
    {
        $admin = \common\models\User::findByUsername('admin');

        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole("administrator");
        $auth->assign($adminRole, $admin->id);
    }

    public function safeDown()
    {
        echo "m171017_093421_assign_initial_roles cannot be reverted.\n";

        return false;
    }
}
