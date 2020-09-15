<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/21
	 * Time: 10:26
	 */

	namespace app\common\model;


	class AdminConfig extends BaseModel
	{
		protected $name = 'admin_system_config';

		public function config_value($config_name)
		{
			$row = self::get(["config_name" => $config_name]);
			$row = to_array($row);
			$vaule = (empty($row) ? "" : $row['config_value']);
			return $vaule;
		}
	}