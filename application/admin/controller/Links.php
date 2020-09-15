<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/7/15
     * Time: 22:12
     */

    namespace app\admin\controller;

    use app\common\model\Links as model_link;
    use app\common\controller\Backbase;

    class Links extends Backbase
    {
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = ["editclass", "delClass"];

        protected $search_tips = "输入Id或标题按回车搜索";

        protected $search_field_arr = ["id", "title"];

        // 默认选项 就是 用在opt 页面 新增那里
        protected $default_row = [
            'title' => "",
            "image_ids" => 0,
            "link" => '',
            "add_time" => "",
            "orders" => 0,
            "status" => 1
        ]; //默认数据


        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_link();

        }



    }