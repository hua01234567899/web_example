<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/7/31
     * Time: 0:32
     */

    namespace app\admin\controller;


    use app\common\controller\Backbase;

    use app\common\model\Sysconfig as model_sysconfig;
    use think\Exception;


    class Setting extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = [];


        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_sysconfig();
        }

        public function index()
        {
            $rows = $this->model->where(["status" => 1])->select();
            $rows = to_array($rows);
            $row = [];
            foreach ($rows as $item) {
                $key = $item['config_name'];
                $value = $item['config_value'];
                $row[$key] = $value;
            }
            $this->assign("row", $row);
            return $this->xfetch();
        }


        public function edit($id)
        {

            $data = $this->request->post();

            //对头像进行处理
            $data = $this->image_deal($data, "user_default_img_id");
            $data = $this->image_deal($data, "default_cover_img_id");
            $time = date("Y-m-d H:i:s");
            try {
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $this->model->where('config_name', $key)->update(['status' => 1, "update_time" => $time, "config_value" => $value]);
                    }
                }
                return $this->successJsonResponse("修改成功");
            } catch (Exception $exception) {
                return $this->failJsonResponse("修改失败" . $exception->getMessage());
            }

        }


    }