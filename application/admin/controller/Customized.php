<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/2
	 * Time: 13:37
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;

	use app\common\model\Customized as model_customized;

	use app\common\model\CustomizedType as model_type;


	class Customized extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = [];

		protected $search_tips = "输入姓名或手机号按回车搜索";

		protected $search_field_arr = ["id", "name", "mobile"];

		protected $type_arr = [];

		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_customized();
			$model_type = new model_type();
			$type_arr = $model_type->where("status", 1)->select();
			if (!empty($type_arr)) {
				$type_arr = to_array($type_arr);
				$type_arr = array_column($type_arr, NULL, "type_id");
			}
			$this->type_arr = $type_arr;
		}

		protected function list_deal($rows)
		{
			foreach ($rows as &$row) {
				$row['ip'] = ip_to_address($row['ip']);
				$row['add_time'] = time_show($row['add_time']);
				if (!empty($row['type_id']) && isset($this->type_arr[$row['type_id']])) {
					$row['type'] = $this->type_arr[$row['type_id']]['title'];
				} else {
					$row['type'] = "";
				}
			}
			return $rows;
		}


		protected function opt_assigned($row)
		{
			if (!empty($row['type_id']) && isset($this->type_arr[$row['type_id']])) {
				$row['type'] = $this->type_arr[$row['type_id']]['title'];
			} else {
				$row['type'] = "";
			}
			return ["row" => $row];
		}


	}