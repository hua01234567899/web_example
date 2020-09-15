<?php

    namespace app\admin\controller;

    use app\common\controller\Backbase;
    use app\common\model\Site;
    use app\common\library\upload\Upload;
    use think\Exception;

    class  System extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = [];

        protected $row = [];

        public function _initialize()
        {
            parent::_initialize();
            $this->model = new Site();
            $this->row = $this->model->get(["status" => 1]);
        }

        public function index()
        {
            $row = to_array($this->row);
            $this->assign("row", $row);
            return $this->xfetch();
        }

        public function edit($id)
        {
            $c_upload = new Upload();
            $logo_ids = $c_upload->insert_data("logo_id");
            $rule = [
                ['title|网站名称', 'require|length:1,40'],
                ['seo_title|首页标题', 'length:0,200'],
                ['keywords|META关键词', 'length:0,200'],
            ];
            $data = [
                'title' => input('post.title/s'),
                'logo_id' => $logo_ids,
                'seo_title' => input('post.seo_title/s'),
                'keywords' => input('post.keywords/s'),
                'description' => input('post.description/s'),
                'beian' => input('post.beian/s'),
                'ga_beian' => input('post.ga_beian/s'),
				'tongji_code'=>input('post.tongji_code/s'),
				'shangqiao_code'=>input('post.shangqiao_code/s')
            ];
            $error_msg = $this->validateData($data, $rule);
            if ($error_msg !== true) {
                return $this->failJsonResponse($error_msg);
            } else {
                try {
                    $update_result = $this->row->save($data);
                    if ($update_result !== false) {
                        return $this->successJsonResponse("修改成功");
                    } else {
                        return $this->failJsonResponse("修改失败");
                    }
                } catch (Exception $exception) {
                    return $this->failJsonResponse("修改失败" . $exception->getMessage());
                }
            }
        }

    }
	