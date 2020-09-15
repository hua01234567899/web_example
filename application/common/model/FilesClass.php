<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/6/30
	 * Time: 11:54
	 */

	namespace app\common\model;

	use app\common\model\BaseModel;

	class FilesClass extends BaseModel
	{
		protected $pk = 'class_id';
		protected $name = 'files_class';
	}