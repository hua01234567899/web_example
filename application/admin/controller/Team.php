<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/21
	 * Time: 13:45
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;

	use app\common\model\Team as model_team;

	class Team extends Backbase
	{

        protected $default_sort = "orders asc,add_time asc";

		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = ["editclass", "delClass"];

		protected $search_tips = "输入名称按回车搜索";

		protected $search_field_arr = ["id", "title"];

		// 默认选项 就是 用在opt 页面 新增那里
		protected $default_row = [
			'class_id' => 0,
			'title' => "",
			'title_en' => '',
			'position' => '',
			"image_ids" => 0,
			"content" => "",
			"add_time" => "",
			"orders" => 0,
			"status" => 1,
			"class_name" => "无"
		]; //默认数据


		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_team();
		}

		protected function list_deal($rows)
		{
			foreach ($rows as &$row) {
				$row['add_time'] = time_show($row['add_time']);
				$row['images'] = get_cover_image($row['image_ids']);
			}
			return $rows;
		}

	}