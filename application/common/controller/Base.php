<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/4/21
     * Time: 1:04
     */

    namespace app\common\controller;

    use think\Controller;

    use app\common\model\db\Bcollect;

    class Base extends Controller
    {
        //使用共有的
        use \app\common\library\traits\Response;


        public function _initialize()
        {
            !defined('IS_GET') && define('IS_GET', $this->request->isGet());
            !defined('IS_POST') && define('IS_POST', $this->request->isPost());
            !defined('IS_AJAX') && define('IS_AJAX', $this->request->isAjax());
            !defined("CONTROLLER") && define("CONTROLLER", $this->request->controller());
            !defined("ACTION") && define("ACTION", $this->request->action());
            //限制 非法IP访问
            $ip = get_ip();
            $Bcollect = new Bcollect();
            $is_in = $Bcollect->is_in("black_ip", ["ip" => $ip]);
            if ($is_in) {
                if (IS_AJAX) {
                    echo $this->failJsonResponse("非法来源!!!");
                    exit;
                } else {
                    $this->page_404();
                    exit;
                }
            }
        }


        protected function validateData($data = array(), $rule = array(), $msg = [])
        {
            return $this->validate($data, $rule, $msg, false);
        }
        
        public function page_404()
        {
            $this->redirect("/404.html");
            exit;
        }
        
        
    }