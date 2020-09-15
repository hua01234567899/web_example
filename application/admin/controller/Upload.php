<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/5/14
     * Time: 18:16
     */

    namespace app\admin\controller;

    use app\common\controller\Backbase;

    use app\common\model\Sysconfig;

    use app\common\model\db\Bcollect;


    use app\common\library\upload\Upload as BaseUpload;


    class Upload extends Backbase
    {

        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = ["index","editorUpload"];

        public function _initialize()
        {
            parent::_initialize();
        }


        /**
         * 文件上传的对应的URL
         */
        public function index()
        {
            $base_upload = new BaseUpload();
            $params = input('param.params');
            $params = think_decrypt($params);
            if (!empty($params)) {
                $new_arr = [];
                parse_str($params, $new_arr);
                $params = $new_arr;
            }
            if (empty($params) || !isset($params['m'])) {
                return $this->failJsonResponse("上传接口异常");
            }
            $back_arr = [];
            //上传视频设置
            $error_msg = $base_upload->filecurl($params, $back_arr);
            if ($error_msg !== true) {
                return $this->failJsonResponse($error_msg);
            } else {
                return $this->successJsonResponse("上传成功", $back_arr);
            }
        }

        public function editorUpload($action="")
        {
            define("IS_EDITOR_UPLOAD",true);
            $ueditor_path  =  ADMIN_PATH."library/xqy_ueditor/";
            $config_path = $ueditor_path."ueditor_admin_config.json";
            $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config_path)), true);
            switch ($action) {
                case 'config':
                    $result =  json_encode($CONFIG);
                    break;
                /* 上传图片 */
                case 'uploadimage':
                    /* 上传涂鸦 */
                case 'uploadscrawl':
                    /* 上传视频 */
                case 'uploadvideo':
                    /* 上传文件 */
                case 'uploadfile':
                    $result = include($ueditor_path."action_upload.php");
                    break;
                /* 列出图片 */
                case 'listimage':
                    $result = include($ueditor_path."action_list.php");
                    break;
                /* 列出文件 */
                case 'listfile':
                    $result = include($ueditor_path."action_list.php");
                    break;

                /* 抓取远程文件 */
                case 'catchimage':
                    $result = include($ueditor_path."action_crawler.php");
                    break;

                default:
                    $result = json_encode(array(
                        'state'=> '请求地址出错'
                    ));
                    break;
            }
            /* 输出结果 */
            if (isset($_GET["callback"])) {
                if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                    echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
                } else {
                    echo json_encode(array(
                        'state'=> 'callback参数不合法'
                    ));
                }
            } else {
                echo $result;
            }


        }


    }