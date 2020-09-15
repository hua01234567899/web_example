<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/20
	 * Time: 11:53
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;
	use app\common\model\Servicer as model;

	class Servicer extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = ["editclass", "delClass"];

		protected $search_tips = "输入名称或qq按回车搜索";

		protected $search_field_arr = ["id", "name", "qq"];


		protected $default_row = [
			'class_id' => 0,
			'title' => "",
			'icon_images_ids' => 0,
			'wechat' => "",
			"wechat_images_ids" => 0,
			"qq" => "",
			"add_time" => "",
			"orders" => 0,
			"status" => 1,
			"class_name" => "无"
		]; //默认数据


		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model();
		}

		protected function list_deal($rows)
		{
			$rows = parent::list_deal($rows);
			foreach ($rows as &$row) {
				$row['icon_images_ids'] = get_cover_image($row['icon_images_ids']);
				$row['wechat_images_ids'] = get_cover_image($row['wechat_images_ids']);
			}
			return $rows;
		}

		protected function opt_deal(&$data)
		{
			parent::opt_deal($data);
			$data = $this->image_deal($data, "icon_images_ids");
			$data = $this->image_deal($data, "wechat_images_ids");
		}


	}