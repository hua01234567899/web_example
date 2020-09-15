<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/4/21
     * Time: 21:52
     */

    namespace app\common\controller;

    use think\Controller;
    use app\common\controller\Base;

    use app\index\library\Language;


    /**
     * 该类是controller 工具类 拥有
     * Class Tool
     * @package app\common\controller
     */
    class Tool extends Base
    {
        public function _initialize()
        {
            parent::_initialize();
            $this->setLang();
        }


        /**
         * 自动设置语言版本
         */
        public function setLang()
        {
            $lang_class = Language::getInstance();
            $lang = $lang_class->autoLang();
            $lang_class->set($lang);
        }

        /**
         * @param string $view 渲染的模本目录
         * @param int $lang_mode 语言默认 默认自动自动获取到 LANG 里面的 lang变量
         * @return mixed
         */
        public function xfetch($view = "index", $lang_mode = LANG_MODE_AUTO, $display = true)
        {
            $language = Language::getInstance();
            if ($lang_mode == LANG_MODE_AUTO) {
                $lang = $language->get();
            } elseif ($lang_mode == LANG_MODE_DEFAULT) {
                $lang = $language->getDefault(); //获取默认的语言版本
            } else {
                $lang = "";
            }
            //这里定义好 访问路径
            defined('VIEW_PATH') or define('VIEW_PATH', ROOT_PATH . "application" . DS . "index" . DS . 'view' . DS . ($lang ? $lang . DS : "")); // 环境变量的配置前缀
            //这里要加翻个语言的判断
            $template = ($lang ? $lang . "/" : "") . $view;
            if ($display) {
                return $this->fetch();
            } else {
                return $template;
            }
        }

        /**
         * 重定向
         */
        public function xRedirect($param, $lang_mode = LANG_MODE_AUTO)
        {
            if (isset($param['url'])) {
                $url = $param['url'];
            } else {
                $method = isset($param['m']) ? $param['m'] : "index";
                $controller = isset($param['c']) ? $param['c'] : "index";
                $param = isset($param['d']) ? $param['d'] : [];
                $url = create_url($method, $param, $controller);
            }
            return redirect($url, 302);
        }


        public function xview($path, $data = [])
        {
            if (!empty($data)) {
                return view($path, $data);
            } else {
                return view($path);
            }
        }


    }