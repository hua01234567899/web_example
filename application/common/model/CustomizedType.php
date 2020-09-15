<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/7/2
	 * Time: 13:52
	 */

	namespace app\common\model;

	use app\common\model\BaseModel;

	class CustomizedType extends BaseModel
	{
		protected $pk = 'type_id';
		protected $name = 'customized_type';
	}