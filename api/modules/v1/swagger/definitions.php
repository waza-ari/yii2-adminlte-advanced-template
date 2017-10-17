<?php
namespace app\models\v1\swagger;
/**
 * @SWG\Definition(
 *      definition="Error",
 *      required={"status", "message"},
 *      @SWG\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=0
 *      ),
 *      @SWG\Property(
 *          property="message",
 *          type="string",
 *          example="Wrong username or password"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          example="Unauthorized"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          type="integer",
 *          example="401"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          type="string",
 *          example="yii\\web\\UnauthorizedHttpException"
 *      )
 * )
 */