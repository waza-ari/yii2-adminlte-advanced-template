<?php

namespace api\modules\v1\models;

use common\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "token".
 *
 * @SWG\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="The unique ID of this login token",
 *     example="1"
 * )
 * @property integer $id
 *
 * @SWG\Property(
 *     property="user_id",
 *     type="integer",
 *     format="int64",
 *     description="The unique ID of this user",
 *     example="1"
 * )
 * @property integer $user_id
 *
 * @SWG\Property(
 *     property="auth_key",
 *     type="string",
 *     description="Time limited token that can be used to authenticate API requests after the user has logged in.",
 *     example="kNKVgDh7yH22ueEmKjoBlWnY4048goQr"
 * )
 * @property string $auth_key
 *
 * @SWG\Property(
 *     property="valid_until",
 *     type="datetime",
 *     description="Format: YYYY-MM-DDThh:mm:ssZ, where Z refers to timezone offset. Date until the token will be valid.",
 *     example="2017-05-29T17:12:12-5:00"
 * )
 * @property integer $valid_until
 *
 * @SWG\Property(
 *     property="active",
 *     type="boolean",
 *     description="Defines whether this token is active or not.",
 *     example="true"
 * )
 * @property boolean $active
 *
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @SWG\Property(
 *     property="user",
 *     type="User",
 *     description="The user this token belongs to"
 * )
 * @property User $user
 *
 * @SWG\Definition(definition="Token", type="object", @SWG\Xml(name="Token"))
 *
 */
class Token extends ActiveRecord
{

    ####################################
    ########### General data ###########
    ####################################

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%token}}';
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'user_id' => 'User ID',
            'auth_key' => 'Auth Key',
            'valid_until' => 'Valid Until',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
            default:
                return ['auth_key', 'valid_until'];
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'auth_key'], 'required'],
            [['user_id', 'valid_until'], 'integer'],
            [['auth_key'], 'string', 'max' => 32],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    ##########################################
    ########### Relation Functions ###########
    ##########################################

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

/**
 * @SWG\Definition(
 *      definition="TokenAnswer",
 *      @SWG\Property(
 *          property="auth_key",
 *          type="string",
 *          description="Time limited token that can be used to authenticate API requests after the user has logged in.",
 *          example="kNKVgDh7yH22ueEmKjoBlWnY4048goQr"
 *      ),
 *      @SWG\Property(
 *          property="valid_until",
 *          type="datetime",
 *          description="Format: YYYY-MM-DDThh:mm:ssZ, where Z refers to timezone offset. Date until the token will be valid.",
 *          example="2017-05-29T17:12:12-5:00"
 *      )
 * )
 */