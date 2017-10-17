<?php

use yii\db\Migration;

class m171017_092920_build_authorization_data extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // add "viewAPIDocumentation" permission
        $viewAPIDocumentation = $auth->createPermission('viewAPIDocumentation');
        $viewAPIDocumentation->description = 'View Swagger API documentation';
        $auth->add($viewAPIDocumentation);

        // add "admin" role
        $administrator = $auth->createRole('administrator');
        $auth->add($administrator);
        $auth->addChild($administrator, $viewAPIDocumentation);
    }

    public function safeDown()
    {
        echo "m171017_092920_build_authorization_data cannot be reverted.\n";

        return false;
    }
}
