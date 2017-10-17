<?php

use yii\db\Migration;

class m170715_184242_add_login_token_settings extends Migration
{

    public function safeUp()
    {
        /** @var pheme\settings\components\Settings $settings */
        $settings = Yii::$app->settings;
        $settings->set('tokens_per_user', 1, 'login', 'integer');
        $settings->set('token_duration', 3600, 'login', 'integer');
    }

    public function safeDown()
    {
        /** @var pheme\settings\components\Settings $settings */
        $settings = Yii::$app->settings;
        $settings->delete('tokens_per_user', 'login');
        $settings->delete('token_duration', 'login');
    }
}
