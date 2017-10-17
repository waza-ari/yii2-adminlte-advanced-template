<?php
namespace api\modules\v1\swagger;
/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="localhost/api",
 *     basePath="/v1",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="KRASv3 SmartAdmin",
 *         description="KRASv3 SmartAdmin, Version: __1.0.0__",
 *         @SWG\Contact(name = "Daniel Herrmann", email = "daniel.herrmann1@gmail.com")
 *     ),
 * )
 */
/**
 * @SWG\Definition(
 *   @SWG\Xml(name="##default")
 * )
 */
class ApiResponse
{
    /**
     * @SWG\Property(format="int32", description = "code of result")
     * @var int
     */
    public $code;
    /**
     * @SWG\Property
     * @var string
     */
    public $type;
    /**
     * @SWG\Property
     * @var string
     */
    public $message;
    /**
     * @SWG\Property(format = "int64", enum = {1, 2})
     * @var integer
     */
    public $status;
}