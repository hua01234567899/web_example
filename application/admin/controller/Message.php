<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/6/4
	 * Time: 22:12
	 */
	
	namespace app\admin\controller;
	
	use app\common\controller\Backbase;
	
	use app\common\model\Message as model_message;

	class Message extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = [];
		
		protected $search_tips = "输入Id或姓名或手机号按回车搜索";
		
		protected $search_field_arr = ["id","name", "mobile"];
		
		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_message();
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