<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/9
 * Time: 11:44
 */

namespace app\common\library\traits;

use app\common\model\ErrorLog;

trait Response
{

    private $successful_default_code = 0;

    private $fail_default_code = -404;

    private $successful_default_msg = 'ok';

    private $fail_default_msg = 'error';

    public function successJsonResponse($msg, $data = [], $count = 0, $code = 0)
    {
        if (true == is_array($msg)) {
            $data = $msg;
            $msg = $this->successful_default_msg;
        }
        return $this->responseResult($code, $msg, $data, $count);
    }

    public function responseDatatable($data = [], $count = 0, $code = 0, $msg = "")
    {
        return compact('code', 'msg', 'data', 'count');
    }

    public function failJsonResponse($msg, $data = [], $count = 0, $code = -404)
    {
        if (true == is_array($msg)) {
            $data = $msg;
            $msg = $this->fail_default_msg;
        }
        //记录错误信息开始
        $user_input = input('post.');
        if (!empty($user_input)) {
            $user_input = json_encode($user_input, JSON_UNESCAPED_UNICODE);
        } else {
            $user_input = "";
        }
        $errorModel = new ErrorLog();

        $error_data = [
            "menu_id" => 0,
            "log" => $msg,
            "is_front" => 1,
            "module" => request()->module(),
            "controller" => request()->controller(),
            "method" => request()->action(),
            "user_input"=>$user_input,
            "ip" => request()->ip()
        ];
        $errorModel->data($error_data);
        $result = $errorModel->allowField($errorModel::$allow_fields)->save();
        //记录错误信息结束
        return $this->responseResult($code, $msg, $data);
    }

    protected function responseResult($code, $msg = '', $data = [], $count = 0)
    {
        return json_encode(compact('code', 'msg', 'data', 'count'), JSON_UNESCAPED_UNICODE);
    }

}