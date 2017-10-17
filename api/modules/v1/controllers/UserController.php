<?php
/**
 * Created by PhpStorm.
 * User: dherrman
 * Date: 11.07.17
 * Time: 12:59
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Token;
use common\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\rest\OptionsAction;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;

class UserController extends ActiveController
{
    /**
     * @var string Specify model for CRUD operation (HTTP verbs, see http://www.yiiframework.com/doc-2.0/guide-rest-quick-start.html
     */
    public $modelClass = 'common\models\User';

    /**
     * @SWG\Options(
     *	path = "/users",
     *	tags = {"user"},
     *	operationId = "userOptions",
     *	summary = "options",
     *	produces = {"application/json"},
     *	consumes = {"application/json"},
     *	@SWG\Response(
     *     response = 200,
     *     description = "success",
     *     @SWG\Header(header="Allow", type="GET, POST, HEAD, OPTIONS"),
     *     @SWG\Header(header="Content-Type", type="application/json; charset=UTF-8")
     *  )
     *)
     */
    public function actions()
    {
        return [
            'options' => OptionsAction::class,
        ];
    }

    /**
     * @return array Require Bearer Auth for everything except login
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login'],
        ];
        //TODO: Add further authorization
        return $behaviors;
    }

    /**
     * Take login form, process and return either access token or errors
     *
     * @SWG\Definition(
     *      definition="UserLoginResponse",
     *      @SWG\Property(
     *          property="token",
     *          ref="#/definitions/TokenAnswer"
     *      ),
     *      @SWG\Property(
     *          property="user",
     *          ref="#/definitions/UserScenarioLogin"
     *      )
     * )
     *
     * @return User|array
     * @throws UnauthorizedHttpException
     * @SWG\Post(
     *     path="/users/login",
     *     tags={"user"},
     *     summary="User Login",
     *     description="Tries to login a user based on the supplied credentials. The program tries to find the user by matching the supplied username and checks the password afterwards. Returns a 401 error code if either the user could not be found or the password is wrong.",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "username",
     *        type = "string",
     *        description="The unique username of this user",
     *        required = true
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "login_password",
     *        type = "string",
     *        description="The password entered by the user used to validate his identity",
     *        required = true
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = " The user has been logged in successfully. An access token has been created and returned along with the user object.",
     *         @SWG\Schema(
     *             ref="#/definitions/UserLoginResponse"
     *         )
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "The login failed, either the username was not found or the password was wrong.",
     *         @SWG\Schema(ref="#/definitions/Error")
     *     )
     * )
     */
    public function actionLogin() {

        $params = Yii::$app->getRequest()->getBodyParams();

        if (ArrayHelper::keyExists('username', $params)) {
            $user = User::findByUsername($params['username']);
            if ($user) {
                $user->setScenario(User::SCENARIO_LOGIN);

                $user->login_password = ArrayHelper::getValue($params, 'login_password');

                if ($user->login()) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return ['token' => Yii::$app->user->identity->generateToken(), 'user' => $user];
                }
            }
        }

        throw new UnauthorizedHttpException('Wrong username or password');
    }

    /**
     * Set the token used to authenticate this user to inactive, which means he cannot login anymore
     *
     * @SWG\Get(
     *     path="/users/logout",
     *     tags={"user"},
     *     summary="User Logout",
     *     description="Performs a logout for the given user by setting is auth token to inactive.",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response = 200,
     *         description = " The user has been logged out successfully."
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function actionLogout() {
        //We know the token is valid, as the logout function is protected.
        /** @var Token $token */
        /** @noinspection PhpUndefinedMethodInspection */
        $token = Yii::$app->user->identity->getCurrentUsedAuthToken();
        $token->active = false;
        $token->save();
        return [];
    }

}