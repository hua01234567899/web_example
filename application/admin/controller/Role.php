<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/5/31
	 * Time: 13:45
	 */
	
	namespace app\admin\controller;
	
	use app\common\controller\Backbase;
	use app\admin\model\Menu as model_menu;
	use app\admin\model\Role as model_role;
	use app\admin\model\RolePrivilege as model_role_pricilege;
	
	class Role extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = [];
		
		protected $search_tips = "输入角色名称按回车搜索";
		
		protected $search_field_arr = ["id", "name"];
		
		protected $default_row = [
			'menu_id' => 0,
			'name' => "",
			'parent_id' => 0,
			"remark" => "",
			"orders" => 0,
			"status" => 1,
			"role_privilege" => array() //对应角色所拥有的角色
		]; //默认数据
		
		protected $privilege_data = []; //权限数据
		
		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_role();
		}
		
		/**
		 * @param $rows
		 * 处理将要展示的数据
		 */
		protected function list_deal($rows)
		{
			$model_menu = new model_menu();
			$all_privilege = $this->model->getListAllPrivilege();
			if (empty($all_privilege)) {
				$all_privilege = [];
			}
			if (!empty($rows)) {
				foreach ($rows as &$row) { //获取权限
					$row['privilege'] = array(); //默认空数组
					$role_privilege_list = $this->model->get($row['id'])->rolePrivilege;
					if (!empty($role_privilege_list)) {
						$role_privilege_list = to_array($role_privilege_list);
						$role_privilege_list = array_column($role_privilege_list, NULL, "menu_id");
						foreach ($all_privilege as $privilege_row) {
							$privilege_menu_id = $privilege_row['id'];
							$parent_id = $privilege_row['parent_id'];
							if ($parent_id > 0) {
								$menu_row = $model_menu->get($parent_id);
								$parent_title = $menu_row->title;
							} else {
								$parent_title = "";
							}
							if (isset($role_privilege_list[$privilege_menu_id])) {
								$rules = $role_privilege_list[$privilege_menu_id]['rules'];
								if (!empty($rules)) {
									$tmp = array();
									$tmp['title'] = $privilege_row['title'];
									$tmp['parent_id'] = $parent_id;
									$tmp['parent_title'] = $parent_title;
									$tmp['role'] = array();
									if ($rules == "ADMIN") {
										$tmp['role'][] = "管理";
									} else {
										$rules = explode(",", $rules);
										foreach ($rules as $rule) {
											if (isset($privilege_row['privilege'][$rule])) {
												$tmp['role'][] = $privilege_row['privilege'][$rule]['title'];
											}
										}
									}
									if (!empty($tmp['role'])) {
										$tmp['txt'] = implode("、", $tmp['role']);
										$row['privilege'][] = $tmp;
									}
								}
							}
						}
					}
				}
			}
			return $rows;
		}
		
		protected function opt_assigned($row)
		{
			if (IS_ADD) {
				$user_privilege = [];
				$all_privilege = $this->model->getAllPrivilege($user_privilege);
				return ['row' => $row, 'privilege_list' => $all_privilege];
			} else {
				$role_row = $this->model->get($row['id']);
				$user_privilege = $role_row->rolePrivilege2;
				if (!empty($user_privilege)) {
					$user_privilege = to_array($user_privilege);
					$user_privilege = array_column($user_privilege, NULL, "menu_id");
				} else {
					$user_privilege = [];
				}
				$all_privilege = $this->model->getAllPrivilege($user_privilege);
				return ['row' => $row, 'privilege_list' => $all_privilege];
			}
		}
		
		/**
		 * @param $data
		 * 数据处理
		 */
		protected function opt_deal(&$data)
		{
			$privilege_data = $data['privilege_data'] ?: [];
			$privilege_data = $this->model->dealPrivilege($privilege_data);
			unset($data['privilege_data']);
			$this->privilege_data = $privilege_data;
			$data['menu_id'] = MENU_ID;
			if(IS_ADD){
			    $data['add_time'] = get_current_time();
            }
			return true;
		}
		
		//进一步对role_privilege 进行处理
		protected function opt_after($id)
		{
			$rolePrivilegeModel = new model_role_pricilege();
			$privilege_data = $this->privilege_data;
			if (!empty($privilege_data)) {
				foreach ($privilege_data as &$row) {
					$row['role_id'] = $id;
					$row['status'] = 1;
				}
			}
			unset($row);
			if (IS_ADD) {
				if (!empty($privilege_data)) {
					$rolePrivilegeModel->saveAll($privilege_data);
				}
			} else {
				$rolePrivilegeModel->save(["status" => 2], ["role_id" => $id]); //讲之前的权限先删除
				foreach ($privilege_data as $row) {
					$rolePrivilegeModel2 = new model_role_pricilege();
					$exist_row = $rolePrivilegeModel2->get(["role_id" => $id, "menu_id" => $row['menu_id']]);
					if (!empty($exist_row)) {
						$exist_row->save($row);
					} else {
						$rolePrivilegeModel2->data($row);
						$rolePrivilegeModel2->save();
					}
				}
			}
		}
		
		
	}