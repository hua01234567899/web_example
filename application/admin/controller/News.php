<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/6/5
     * Time: 15:25
     */

    namespace app\admin\controller;

    use app\common\model\News as model_news;
    use app\common\model\NewsClass as model_newsClass;
    use app\common\controller\Backbase;


    class News extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = ["editclass", "delClass"];

        protected $search_tips = "输入标题或关键字按回车搜索";

        protected $search_field_arr = ["id", "title", "keywords"];

        // 默认选项 就是 用在opt 页面 新增那里
        protected $default_row = [
            'class_id' => 0,
            'title' => "",
            'keywords' => "",
            'subtitle' => '',
            "image_ids" => 0,
            "content" => "",
            "seo_title" => "",
            "seo_keywords" => "",
            "seo_description" => "",
            "add_time" => "",
            "orders" => 0,
            "status" => 1,
            "class_name" => "无"
        ]; //默认数据


        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_news();
            $this->class_model = new model_newsClass();
        }


        protected function opt_deal(&$data)
        {
            if (empty($data['seo_title']) && !empty($data['title'])) {
                $data['seo_title'] = $data['title'];
            }
            if (empty($data['subtitle']) && !empty($data['content'])) {
                $data['subtitle'] = cut_str($data['content']);
            }
            parent::opt_deal($data);
            return true;
        }


    }