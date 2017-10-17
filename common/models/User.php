<?php

namespace common\models;

use api\modules\v1\models\Token;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @SWG\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="The unique ID of this user",
 *     example="1"
 * )
 * @property integer $id
 *
 * @SWG\Property(
 *     property="username",
 *     type="string",
 *     description="The unique username of this user",
 *     example="admin"
 * )
 * @property string $username
 *
 * @SWG\Property(
 *     property="password",
 *   type="string",
 *   description="The password of this user",
 *   example="admin"
 * )
 * @property string $password
 *
 * @SWG\Property(
 *   property="auth_key",
 *   type="string",
 *   description="Random string used to authenticate a user over multiple requests using a cookie",
 *   example="skfhba6reg21nbre7agsdfubasurga6gisbdasd7asfa"
 * )
 * @property string $auth_key
 *
 * @SWG\Property(
 *     property="email",
 *   type="string",
 *   description="The email of this user",
 *   example="admin@example.com"
 * )
 * @property string $email
 *
 * @SWG\Property(
 *     property="status",
 *   type="integer",
 *   description="The current status of this user. Possible values include:
 *   - 0:  Deleted
 *   - 10: Active",
 *   example="0",
 *   enum={0, 10}
 * )
 * @property integer $status
 *
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @SWG\Property(
 *     property="tokens",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Token"),
 *     description="All tokens belonging to this user"
 * )
 * @property Token[] $tokens
 *
 * @SWG\Definition(definition="User", type="object", @SWG\Xml(name="User"))
 */
class User extends ActiveRecord implements IdentityInterface
{

    #################################
    ########### Constants ###########
    #################################

    /**
     * Define possible status values
     */
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * Define scenarios
     */
    const SCENARIO_LOGIN = 'login';

    ############################################
    ########### Attribute Definition ###########
    ############################################

    /**
     * Password used to login the user, will not be populated when reading a user from database
     * @var string
     * @SWG\Property(
     *     type="string",
     *     description="The password entered by the user used to validate his identity",
     *     example="secret_password"
     * )
     */
    public $login_password;

    ####################################
    ########### General data ###########
    ####################################

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Create random authentication key for cookie authentication whenever creating a new user
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            //TODO
        ];
    }

    ###########################################
    ########### Scenarios and Rules ###########
    ###########################################

    /**
     * @inheritdoc
     *
     * Only those fields will be returned by default when using a GET request
     */
    public function fields()
    {
        switch ($this->getScenario()) {
            /**
             * @SWG\Definition(
             *      definition="UserScenarioLogin",
             *      @SWG\Property(
             *          property="id",
             *          type="integer",
             *          format="int64",
             *          description="The unique ID of this user",
             *          example="1"
             *      ),
             *      @SWG\Property(
             *          property="username",
             *          type="string",
             *          description="The unique username of this user",
             *          example="admin"
             *      )
             * )
             */
            case self::SCENARIO_LOGIN:
                return ['id', 'username'];
                break;
            default:
                return $this->attributes;
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = ['username', 'login_password'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'login_password'], 'required', 'on' => self::SCENARIO_LOGIN],
            ['login_password', 'checkPassword', 'on' => self::SCENARIO_LOGIN],
            #['status', 'default', 'value' => self::STATUS_ACTIVE],
            #['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    ##########################################
    ########### Identity Interface ###########
    ##########################################

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     *
     * Also checks the validity of the token as well as whether the token is active or not.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Token::find()
            ->joinWith('user')
            ->where(['token.auth_key' => $token, 'active' => true])
            ->andWhere(['>=', 'valid_until', time()])
            ->andWhere(['user.status' => self::STATUS_ACTIVE])
            ->one()
            ->user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    ##################################################
    ########### Token and Login operations ###########
    ##################################################

    /**
     * Generates "remember me" authentication key
     */
    public function generateToken()
    {
        $token = new Token();
        $token->setIsNewRecord(true);
        $token->user_id = $this->id;
        $token->auth_key = Yii::$app->security->generateRandomString();
        $token->valid_until = time() + Yii::$app->settings->get('login.token_duration');
        $token->save();
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $authKey == $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    ########################################
    ########### Public functions ###########
    ########################################

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this/*, $this->rememberMe ? 3600 * 24 * 30 : 0*/);
        } else {
            return false;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function checkPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->login_password || !$this->validatePassword($this->login_password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Returns the token which is currently used to authenticate this user.
     * Returns null if the token could not be found.
     *
     * @return Token|null
     */
    public function getCurrentUsedAuthToken() {
        $authHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return Token::findOne(['user_id' => $this->id, 'auth_key' => $matches[1]]);
        } else {
            return null;
        }
    }
}