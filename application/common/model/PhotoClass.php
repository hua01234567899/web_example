<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/6/29
	 * Time: 15:19
	 */

	namespace app\common\model;

	use app\common\model\BaseModel;

	class PhotoClass extends BaseModel
	{
		protected $pk = 'class_id';
		protected $name = 'photo_class';
	}