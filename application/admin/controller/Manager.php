<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/8/14
	 * Time: 11:52
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;
	use app\common\model\AdminConfig as model;
	use think\Exception;

	class Manager extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = [];

		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model();
		}


		public function index()
		{
			try {
				$rows = $this->model->where(["status" => 1])->select();
				$rows = to_array($rows);
			} catch (Exception $exception) {
				$rows = [];
			}
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
			$time = date("Y-m-d H:i:s");
			$index_config_config_path = APP_PATH . "index" . DS . "other" . DS . "config.json";
			if (!file_exists($index_config_config_path)) {
				$myfile = fopen($index_config_config_path, "w");
				fclose($myfile);
			}
			$config_data = file_get_contents($index_config_config_path);
			$config_data = is_json($config_data);
			if (!is_array($config_data)) {
				$config_data = [];
			}
			$put_config_fields = ["app_debug"];
			try {
				if (!empty($data)) {
					foreach ($data as $key => $value) {
						if (in_array($key, $put_config_fields)) {
							$config_data[$key] = $value;
						}
						$this->model->where('config_name', $key)->update(['status' => 1, "update_time" => $time, "config_value" => $value]);
					}
					$config_data = json_encode($config_data,JSON_UNESCAPED_UNICODE);
					file_put_contents($index_config_config_path,$config_data);
				}
				return $this->successJsonResponse("修改成功");
			} catch (Exception $exception) {
				return $this->failJsonResponse("修改失败" . $exception->getMessage());
			}

		}


	}