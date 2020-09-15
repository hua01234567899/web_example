<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/6/29
     * Time: 16:39
     */

    namespace app\admin\controller;

    use  app\common\model\Files as model_file;
    use  app\common\model\FilesClass as model_class;
    use app\common\controller\Backbase;

    class Files extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = ["editclass", "delClass"];

        protected $search_tips = "输入标题按回车搜索";

        protected $search_field_arr = ["id", "title"];

        // 默认选项 就是 用在opt 页面 新增那里
        protected $default_row = [
            'class_id' => 0,
            'title' => "",
            'type' => 0,
            "file_id" => 0,
            "psd_id" => 0,
            "image_ids" => 0,
            "add_time" => "",
            "orders" => 0,
            "status" => 1,
            "class_name" => "无",
        ]; //默认数据


        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_file();
            $this->class_model = new model_class();
        }


        protected function list_deal($rows)
        {
            $rows = parent::list_deal($rows);
            foreach ($rows as &$row) {
                if ($row['type'] == 0) {
                    $row['type'] = '图片';
                } else {
                    $row['type'] = 'ICON';
                }
            }
            return $rows;
        }

        protected function opt_deal(&$data)
        {
            parent::opt_deal($data);
            $data = $this->image_deal($data, "file_id");
            $data = $this->image_deal($data, "psd_id");
            return true;
        }

    }