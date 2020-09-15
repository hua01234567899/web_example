<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2020/4/21
 * Time: 23:17
 */

namespace app\common\model;

use app\common\model\BaseModel;

class ErrorLog extends BaseModel
{
    public static $allow_fields = ["menu_id", "log", "is_front", "module", "controller", "method", "user_input", "ip"]; //允许前台写入
    protected $name = 'error_log';

}