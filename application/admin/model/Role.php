<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2019/12/11
	 * Time: 11:56
	 */
	
	namespace app\admin\model;
	
	use app\common\model\BaseModel;
	
	class Role extends BaseModel
	{
		protected $name = 'role';
		
		public function rolePrivilege()
		{
			return $this->hasMany('RolePrivilege', "role_id")->where("status", "1")->order("id asc");
		}
		
		
		//修改权限时候用到
		public function rolePrivilege2()
		{
			return $this->hasMany('RolePrivilege', "role_id")->where("status", "1")->order("id asc");
		}
		
		protected function getOnePrivilege($menu_obj)
		{
			$privilege_arr = array();
			if (!empty($menu_obj->show_method)) { // 查看权限
				$privilege_arr['SHOW'] = array("title" => "查看", "code" => "SHOW");
			}
			if (!empty($menu_obj->add_method)) { //新增权限
				$privilege_arr['ADD'] = array("title" => "新增", "code" => "ADD");
			}
			if (!empty($menu_obj->edit_method)) {
				$privilege_arr['EDIT'] = array("title" => "修改", "code" => "EDIT");
			}
			if (!empty($menu_obj->delete_method)) {
				$privilege_arr['DELETE'] = array("title" => "删除", "code" => "DELETE");
			}
			$other_privilege = $menu_obj->privilegeMethods;
			if (!empty($other_privilege)) {
				$other_privilege = collection($other_privilege)->toArray();
				foreach ($other_privilege as $obj) {
					$code = strtoupper($obj['other_method_code']);
					$title = $obj['other_method_title'];
					$privilege_arr[$code] = array("title" => $title, "code" => $code);
				}
			}
			return $privilege_arr;
		}
		
		
		/**
		 * @return array
		 * @user_privilege 用户
		 * 获取权限树形表
		 */
		public function getAllPrivilege($user_privilege = [])
		{
			$menu_model = model("menu");
			//首先获取第一集分类的
			$menu_obj_arr = $menu_model->where('status', '1')->where("parent_id", 0)->where("is_only_manager", 0)->order('parent_id asc,orders asc,id asc')->select();
			$arr = array();
			$all_menu_obj_arr = $menu_model->where('status', '1')->where("is_only_manager", 0)->order('parent_id asc,orders asc,id asc')->select();
			if (!empty($all_menu_obj_arr)) {
				$all_menu_rows = to_array($all_menu_obj_arr); //获取数组
				$all_parent_id_arr = array_column($all_menu_rows, "parent_id"); // 父类parent_id
				$all_parent_id_arr = array_unique($all_parent_id_arr);  //获取父类数组
			}
			
			if (!empty($menu_obj_arr)) {
				//对第一层进行遍历
				foreach ($menu_obj_arr as $menu_obj) {
					
					//如果下面有子元素
					if (in_array($menu_obj->id, $all_parent_id_arr)) {
						$menu_child = [];
						foreach ($all_menu_obj_arr as $one_menu_obj) {
							if ($one_menu_obj->parent_id == $menu_obj->id) {
								$privilege_arr = $this->getOnePrivilege($menu_obj);
								if (empty($privilege_arr)) {
									continue;
								} else {
									$menu_row = to_array($one_menu_obj);
									$menu_row['privilege'] = $privilege_arr;
									$menu_child[] = $menu_row;
								}
							}
						}
						if (!empty($menu_child)) {
							$menu_row = to_array($menu_obj);
							$tmp = [];
							
							$tmp['id'] = "third@" . $menu_row['id'] . "@" . "ADMIN" . "@" . count($menu_child);
							$tmp['title'] = $menu_row['title'];
							$tmp['children'] = [];
//							$tmp['spread'] = true;
							foreach ($menu_child as $children_row) {
								$children_arr = [];
								
								$children_arr['id'] = "second@" . $children_row['id'] . "@" . "ADMIN" . "@" . (count($children_row['privilege']));
								$children_arr['title'] = $children_row['title'];
//								$children_arr['spread'] = true;
								$children_arr['children'] = [];
								
								foreach ($children_row['privilege'] as $privilege_key => $privilege_row) {
									$third_child = [];
									$third_child['id'] = "first@" . $children_row['id'] . "@" . $privilege_key . "@1";
									$third_child['title'] = $privilege_row['title'];
//									$third_child['spread'] = true;
									if (isset($user_privilege[$children_row['id']])) {
										$user_rules = $user_privilege[$children_row['id']]['rules'];
										if (strrpos($user_rules, "ADMIN") !== false || strrpos($user_rules, $privilege_key) !== false) {
											$third_child['checked'] = true;
										}
									}
									$children_arr['children'][] = $third_child;
								}
								$tmp['children'][] = $children_arr;
							}
							$arr[] = $tmp;
						}
						
					} else {
						//下面没有子元素
						$privilege_arr = $this->getOnePrivilege($menu_obj);
						if (empty($privilege_arr)) {
							continue;
						} else {
							$menu_row = to_array($menu_obj);
							$menu_row['privilege'] = $privilege_arr;
							$tmp = [];
							$tmp['id'] = "second@" . $menu_row['id'] . "@" . "ADMIN" . "@" . (count($menu_row['privilege']));
							$tmp['title'] = $menu_row['title'];
//							$tmp['spread'] =true;
							$tmp['children'] = [];
							foreach ($menu_row['privilege'] as $privilege_key => $privilege_row) {
								$children_arr = [];
								$children_arr['id'] = "first@" . $menu_row['id'] . "@" . $privilege_key . "@1";
								$children_arr['title'] = $privilege_row['title'];
//								$children_arr['spread'] = true;
								if (isset($user_privilege[$menu_row['id']])) {
									$user_rules = $user_privilege[$menu_row['id']]['rules'];
									if (strrpos($user_rules, "ADMIN") !== false || strrpos($user_rules, $privilege_key) !== false) {
										$children_arr['checked'] = true;
									}
								}
								$tmp['children'][] = $children_arr;
							}
							$arr[] = $tmp;
						}
					}
					
				}
			}
			return $arr;
		}
		
		public function dealPrivilege($arr)
		{
			$role_id = 0;
			$privilege_arr = [];
			foreach ($arr as $row) {
				$key = $row['id'];
				$key = explode("@", $key);
				list($level, $menu_id, $rule, $count) = $key;
				if (strrpos($level, "third") !== false) { //别人的父级栏目
					$tmp = array();
					$tmp['role_id'] = $role_id;
					$tmp['menu_id'] = $menu_id;
					$tmp['rules'] = "ADMIN";
					$privilege_arr[] = $tmp;
					
					$children_arr = $row['children'];
					//进行获取第二级
					foreach ($children_arr as $children) {
						$key2 = $children['id'];
						$key2 = explode("@", $key2);
						list($level2, $menu_id2, $rule2, $count2) = $key2;
						
						$children_arr2 = $children['children'];
						//进行第三级的遍历
						$act_pri = [];
						foreach ($children_arr2 as $item) {
							$key3 = $item['id'];
							$key3 = explode("@", $key3);
							list($level3, $menu_id3, $rule3, $count3) = $key3;
							if (strrpos($level3, "first") !== false) {
								$act_pri[] = $rule3;
							}
						}
						if (!empty($act_pri)) {
							if (count($act_pri) == $count2) {
								$tmp = array();
								$tmp['role_id'] = $role_id;
								$tmp['menu_id'] = $menu_id2;
								$tmp['rules'] = "ADMIN";
								$privilege_arr[] = $tmp;
							} else {
								$tmp = array();
								$tmp['role_id'] = $role_id;
								$tmp['menu_id'] = $menu_id2;
								$tmp['rules'] = implode(",", $act_pri);
								$privilege_arr[] = $tmp;
							}
						}
					}
					
					
				} elseif (strrpos($level, "second") !== false) { //如果是第二级
					
					$children_arr2 = $row['children'];
					$act_pri = [];
					foreach ($children_arr2 as $item) {
						$key3 = $item['id'];
						$key3 = explode("@", $key3);
						list($level3, $menu_id3, $rule3, $count3) = $key3;
						if (strrpos($level3, "first") !== false) {
							$act_pri[] = $rule3;
						}
					}
					if (!empty($act_pri)) {
						if (count($act_pri) == $count) {
							$tmp = array();
							$tmp['role_id'] = $role_id;
							$tmp['menu_id'] = $menu_id;
							$tmp['rules'] = "ADMIN";
							$privilege_arr[] = $tmp;
						} else {
							$tmp = array();
							$tmp['role_id'] = $role_id;
							$tmp['menu_id'] = $menu_id;
							$tmp['rules'] = implode(",", $act_pri);
							$privilege_arr[] = $tmp;
						}
					}
				}
				
			}
			
			return $privilege_arr;
			
			
		}
		
		
		public function getListAllPrivilege()
		{
			$menu_model = model("menu");
			//首先获取第一集分类的
			$menu_obj_arr = $menu_model->where('status', '1')->where("parent_id", 0)->where("is_only_manager", 0)->order('parent_id asc,orders desc,id asc')->select();
			$arr = array();
			$all_menu_obj_arr = $menu_model->where('status', '1')->where("is_only_manager", 0)->order('parent_id asc,orders asc,id asc')->select();
			if (!empty($all_menu_obj_arr)) {
				$all_menu_rows = to_array($all_menu_obj_arr); //获取数组
				$all_parent_id_arr = array_column($all_menu_rows, "parent_id"); // 父类parent_id
				$all_parent_id_arr = array_unique($all_parent_id_arr);  //获取父类数组
			}
			if (!empty($menu_obj_arr)) {
				//对第一层进行遍历
				foreach ($menu_obj_arr as $menu_obj) {
					//如果下面有子元素
					if (in_array($menu_obj->id, $all_parent_id_arr)) {
						$menu_child = [];
						foreach ($all_menu_obj_arr as $one_menu_obj) {
							if ($one_menu_obj->parent_id == $menu_obj->id) {
								$privilege_arr = $this->getOnePrivilege($menu_obj);
								if (empty($privilege_arr)) {
									continue;
								} else {
									$menu_row = to_array($one_menu_obj);
									$menu_row['privilege'] = $privilege_arr;
									$arr[] = $menu_row;
								}
							}
						}
					} else {
						//下面没有子元素
						$privilege_arr = $this->getOnePrivilege($menu_obj);
						if (empty($privilege_arr)) {
							continue;
						} else {
							$menu_row = to_array($menu_obj);
							$menu_row['privilege'] = $privilege_arr;
							$arr[] = $menu_row;
						}
					}
					
				}
			}
			return $arr;
		}
		
		
		/**
		 * 获取能分配给客户的权限
		 * @return mixed
		 */
		public function getListAllPrivilege2()
		{
			$menu_model = model("menu");
			$menu_obj_arr = $menu_model->where('status', '1')->where("is_only_manager", 0)->order('parent_id asc,orders asc,id asc')->select();
			$arr = array();
			if (!empty($menu_obj_arr)) {
				$menu_rows = collection($menu_obj_arr)->toArray(); //获取数组
				$parent_id_arr = array_column($menu_rows, "parent_id");
				$parent_id_arr = array_unique($parent_id_arr);  //获取父类数组
				foreach ($menu_obj_arr as $menu_obj) {
					if (in_array($menu_obj->id, $parent_id_arr)) { //如果存在  是别人的父栏目
						continue;
					} else {
						$privilege_arr = array();
						if (!empty($menu_obj->show_method)) { // 查看权限
							$privilege_arr['SHOW'] = array("title" => "查看", "code" => "SHOW");
						}
						if (!empty($menu_obj->add_method)) { //新增权限
							$privilege_arr['ADD'] = array("title" => "新增", "code" => "ADD");
						}
						if (!empty($menu_obj->edit_method)) {
							$privilege_arr['EDIT'] = array("title" => "修改", "code" => "EDIT");
						}
						if (!empty($menu_obj->delete_method)) {
							$privilege_arr['DELETE'] = array("title" => "删除", "code" => "DELETE");
						}
						$other_privilege = $menu_obj->privilegeMethods;
						if (!empty($other_privilege)) {
							$other_privilege = collection($other_privilege)->toArray();
							foreach ($other_privilege as $obj) {
								$code = strtoupper($obj['other_method_code']);
								$title = $obj['other_method_title'];
								$privilege_arr[$code] = array("title" => $title, "code" => $code);
							}
						}
					}
					if (empty($privilege_arr)) {
						continue;
					}
					$menu_row = $menu_obj->toArray();
					$menu_row['privilege'] = $privilege_arr;
					$arr[] = $menu_row;
				}
			}
			
			return $arr;
			
			
		}
		
	}