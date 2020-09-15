<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/6/29
	 * Time: 15:21
	 */

	namespace app\admin\controller;

	use app\common\model\Photo as model_obj;
	use app\common\model\PhotoClass as model_Class;
	use app\common\controller\Backbase;

	class Photo extends Backbase
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
			"image_ids" => 0,
			"link_url"=>"",
			"add_time" => "",
			"orders" => 0,
			"status" => 1,
			"class_name" => "无"
		]; //默认数据

		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_obj();
			$this->class_model = new model_Class();
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