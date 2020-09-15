<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/9/3
	 * Time: 10:10
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;
	use app\admin\model\UserLogs as model;

	class Userlogs extends Backbase
	{
		protected $noNeedLogin = [];

		//不需要验证权限的方法
		protected $noNeedRight = ["editclass", "delClass"];

		protected $search_tips = "输入标题按回车搜索";

		protected $search_field_arr = ["id", "title"];

		protected $where_menu = false; //是否需要加上menu_id 的查询条件  默认需要

		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model();
			$where_map = [];
			if (!$this->auth->isSuperAdmin()) {
				$where_map['is_show'] = 1;
				$this->where_map = $where_map;
			}
		}

		protected function list_deal($rows)
		{
			foreach ($rows as &$row) {
				$row['ip'] = ip_to_address($row['ip']);
				$row['add_time'] = time_show($row['add_time']);
			}
			return $rows;
		}

	}