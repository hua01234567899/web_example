<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/4/21
	 * Time: 23:19
	 */
	
	namespace app\common\model;
	
	use think\Model;
	
	class BaseModel extends Model
	{
		public $sort_order = "orders asc,add_time desc";
	}