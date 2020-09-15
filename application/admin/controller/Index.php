<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/5/9
     * Time: 9:32
     */

    namespace app\admin\controller;

    use app\common\controller\Backbase;
    use app\common\model\Sysconfig;
    use think\Session;
    use think\Cookie;
    use app\admin\model\Admin;

    use app\index\model\News as model_news;
    use app\common\library\zip\ZIP;


    class Index extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = ['login'];
        //不需要验证权限的方法
        protected $noNeedRight = ['index', "export_user_data", "demo", "welcome", 'login_out', "userinfo", "password"];

        public function _initialize()
        {
            parent::_initialize();
        }

        public function index()
        {
            return $this->xfetch();
        }

        /**
         * 将用户资料导出
         */
        public function export_user_data()
        {
            $zip = new ZIP();
            $zip->export_user_data(USER_DATA_PATH, ADMIN_PATH . '/data.zip');
        }

        /**
         * 将网路日志导出
         */
        public function export_web_log()
        {
            $path = ACCESS_LOG_PATH . "access.log";
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-disposition: attachment; filename=' . basename($path)); //文件名
            header("Content-Type: application/log"); //zip格式的
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
            header('Content-Length: ' . filesize($path)); //告诉浏览器，文件大小
            @readfile($path);
        }

        /**
         * 我们的团队
         * @return mixed
         */
        public function team()
        {
            return $this->xfetch();
        }


        /**
         * 显示首页信息
         * @return mixed
         */
        public function welcome()
        {
            $system_info = get_system_info();
            $model_news = new model_news();
            $space_info = get_space_info();
//            dump($system_info);
//            dump($space_info);
            $new_arr = $model_news->where("status", 1)->order(WEB_SORT)->limit(0, 2)->select();
//			dump($new_arr);
            $new_arr = to_array($new_arr);
            $this->assign(["space_info" => $space_info, "system_info" => $system_info, "new_list" => $new_arr]);
            return $this->xfetch();
        }


        /**
         *用户登录
         * @return mixed|string
         */
        public function login()
        {
            $url = Session::has("referer") ? Session::get("referer") : url("index/index");
            if ($this->auth->isLogin()) {
                $this->success(("你已经登录"), $url);
                Session::delete("referer");
            }
            if (!IS_AJAX) {
                create_token();
                if (Cookie::has("admin_password")) {
                    $is_rember = true;
                    $username = Cookie::get("admin_username");
                    $password = Cookie::get("admin_password");

                } else {
                    $is_rember = false;
                    $username = "";
                    $password = "";
                }
                $this->assign([
                    "is_remember" => $is_rember,
                    "username" => $username,
                    "password" => $password
                ]);
                return $this->xfetch();
            } else {
                token_expired(); //检验token是否过期
                $rule = [
                    ['username|用户名', 'require|length:1,30'],
                    ['password|密码', 'require|length:3,30'],
                    ['captcha|验证码', 'require|captcha']
                ];
                $data = [
                    'username' => input('post.username/s'),
                    'password' => input('post.password/s'),
                    'captcha' => input('post.captcha/s'),
                ];
                $result = $this->validateData($data, $rule);
                $is_rember = input('post.remember/s');
                if (!empty($is_rember)) {
                    $is_rember = true;
                } else {
                    $is_rember = false;
                }
                //记住密码
                if ($result !== true) {
                    post_fail($result);
                }
                //开始验证账号与密码
                $error_msg = $this->auth->login($data['username'], $data['password'], $is_rember);
                if ($error_msg !== true) {
                    post_fail($error_msg);
                } else {
                    Session::delete("referer");
                    //记住登录
                    return $this->successJsonResponse("登录成功", array("url" => $url));
                }
            }

        }

        /**
         * @return mixed
         * 基本资料
         */
        public function userinfo()
        {
            if (IS_AJAX) {
                $rule = [
                    ['nickname|昵称', 'require|length:1,30'],
                    ['email|邮箱', 'email'],
                    ['remark|内容', 'length:1,300'],
                ];
                $data = [
                    'nickname' => input('post.nickname/s'),
                    'email' => input('post.email/s'),
                    'remark' => input('post.remark/s')
                ];
                $result = $this->validateData($data, $rule);
                if ($result !== true) {
                    return $this->failJsonResponse($result);
                } else {
                    $admin_model = new Admin();
                    $result = $admin_model->save($data, ["id" => $this->admin_id]);
                    if ($result !== false) {
                        return $this->successJsonResponse("修改成功");
                    } else {
                        return $this->failJsonResponse("修改失败");
                    }
                }
            } else {
                return $this->xfetch();
            }
        }

        /**
         * 修改密码
         */
        public function password()
        {
            if (IS_AJAX) {
                $rule = [
                    ['password|密码', 'require|length:6,30|alphaNum|confirm:repassword'],
                ];
                $data = [
                    'password' => input('post.password/s'),
                    'repassword' => input('post.repassword/s'),
                ];
                $result = $this->validateData($data, $rule);
                if ($result !== true) {
                    return $this->failJsonResponse($result);
                } else {
                    $arr = [];
                    $arr['password'] = $this->auth->encryptPassword($data['password']);
                    $admin_model = new Admin();
                    $result = $admin_model->save($arr, ["id" => $this->admin_id]);
                    if ($result !== false) {
                        if (Cookie::has("admin_password") && Cookie::has("admin_username")) {
                            $username = Cookie::get("admin_username");
                            Cookie::set("admin_username", $username, 3600 * 24 * 31 * 12);
                            Cookie::set("admin_password", $data['password'], 3600 * 24 * 31 * 12);
                        }
                        return $this->successJsonResponse("修改成功");
                    } else {
                        return $this->failJsonResponse("修改失败");
                    }
                }
            } else {
                return $this->xfetch();
            }
        }


        /**
         * 注销登录
         */
        public function login_out()
        {
            $this->auth->logout();
            $this->redirect("index/login");
            return true;
        }


    }