<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/21
	 * Time: 17:44
	 */

	namespace app\common\model;

	use app\common\model\BaseModel;

	class WebsClass extends BaseModel
	{
		protected $pk = 'class_id';
		protected $name = 'webs_class';
	}