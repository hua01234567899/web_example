<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/4/22
	 * Time: 19:13
	 */
	
	namespace app\common\model\db;
	
	use think\Db;
	use app\common\model\BaseModel;
	
	use xqy\Check;
	
	class Bmessage extends BaseModel
	{
		
		public function post($table, $data)
		{
			try {
				$data['create_time'] = date("Y-m-d H:i:s");
				$data['update_time'] = date("Y-m-d H:i:s");
				$result = Db::name($table)->insert($data);
				if ($result !== false) {
					return true;
				} else {
					return false;
				}
			} catch (Exception $e) {
				return false;
			}
		}
		
	}