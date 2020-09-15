<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/5/27
	 * Time: 22:11
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;
	use app\admin\model\User as model_user;
	use app\admin\model\Role as model_role;
	use app\admin\model\UserRole as model_user_role;
	use think\Exception;

	use app\admin\model\UserLogin as model_userlogin;

	class User extends Backbase
	{
		//不需要验证登录的方法
		protected $noNeedLogin = [];
		//不需要验证权限的方法
		protected $noNeedRight = [];

		protected $search_tips = "输入名称或账号按回车搜索";

		protected $search_field_arr = ["id", "username", "nickname"];

		protected $where_map = ["id" => ["<>", 1]]; //不能对 id 为1的数据进行修改


		protected $roles = "";

		// 默认选项 就是 用在opt 页面 新增那里
		protected $default_row = [
			'menu_id' => 0,
			'username' => "",
			'password' => "",
			"nickname" => "",
			"email" => "",
			"remark" => "",
			"orders" => 0,
			"status" => 1,
			"roles" => array() //对应用户所拥有的角色
		]; //默认数据


		protected $model_userlogin;


		protected $is_add_edit_same_templete = false; //编辑与修改显示不同的模板处理


		protected $login_history_sort = "create_time desc";

		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_user();
			$this->model_userlogin = new model_userlogin();
		}


		protected function list_deal($rows)
		{
			$roleModel = new model_role();
			try {
				$roles = $roleModel->where("status", "<>", 2)->select();
				$all_roles = to_array($roles);
			} catch (Exception $e) {
				$all_roles = [];
			}
			if (!empty($all_roles)) {
				$all_roles = array_column($all_roles, NULL, "id");
			}
			foreach ($rows as &$row) {
				$roles = $this->model->get($row['id'])->roles();
				$roles_arr = [];
				if (!empty($roles)) {
					foreach ($roles as $role) {
						if (isset($all_roles[$role])) {
							$tmp = array();
							$tmp['id'] = $role;
							$tmp['role_name'] = $all_roles[$role]['name'];
							$roles_arr[] = $tmp;
						}
					}
				}
				$row['roles'] = $roles_arr;
			}
			return $rows;
		}


		protected function opt_assigned($row)
		{
			$row['password'] = "";
			$model_role = new model_role();
			$all_roles = $model_role->where("status", "<>", 2)->order("id asc")->select();
			$all_roles = to_array($all_roles);
			if (!IS_ADD) {
				$roleModel = new model_role();
				$all_roles = $roleModel->where("status", "<>", 2)->order("id asc")->select();
				$all_roles = to_array($all_roles);
				$row['password'] = "";
				$roles = $this->model->get($row['id'])->roles();
				$row['roles'] = $roles;
			}
			return ['row' => $row, 'role_list' => $all_roles];
		}

		protected function opt_check($data)
		{
			$rule = [
				['username|登录账号', 'require|alphaDash'],
				['password|登录密码', 'require|length:6,20|alphaDash'],
				['password2|确认密码', 'require|confirm:password'],
				['nickname|昵称', 'require'],
				['email|邮箱', 'email'],
			];
			if (!IS_ADD) {
				//修改
				unset($rule[0]);
				if (empty($data['password'])) {
					unset($rule[1]);
					unset($rule[2]);
				}
			} else {
				//添加
				try {
					$has_data = $this->model->get(['username' => $data['username']]);
					if (!empty($has_data)) {
						return "登录账号重复,请重新输入一个新的账号";
					}
				} catch (Exception $e) {
					return "登录账号重复,请重新输入一个新的账号";
				}
			}
			$result = $this->validateData($data, $rule);
			if ($result !== true) {
				return $result;
			} else {
				return true;
			}
		}

		protected function opt_deal(&$data)
		{
			if (!empty($data['password'])) {
				//对密码进行加密处理
				$data['password'] = $this->auth->encryptPassword($data['password']);
			}
			unset($data['password2']);
			$data['menu_id'] = MENU_ID;
			if (isset($data['roles'])) {
				$role_arr = (array)$data['roles'];
				unset($data['roles']);
				$tmp = array();
				foreach ($role_arr as $item) {
					if (is_numeric($item) && $item > 0) {
						$tmp[] = (int)$item;
					}
				}
				if (!empty($tmp)) {
					$roles = implode(",", $tmp);
				} else {
					$roles = "";
				}
			} else {
				$roles = "";
			}
			$this->roles = $roles;
			return true;
		}


		protected function opt_after($id)
		{
			$roles = $this->roles;
			$userroleModel = new model_user_role();
			if (IS_ADD) {
				if (!empty($roles)) {
					$data = array("user_id" => $id, "role_ids" => $roles);
					$userroleModel->data($data);
					$userroleModel->save();
				}
			} else {
				$role_row = $userroleModel->get(['user_id' => $id]);
				if (!empty($role_row)) {
					$role_row->role_ids = $roles;
					$role_row->save();
				} else {
					$data = array("user_id" => $id, "role_ids" => $roles);
					if (!empty($roles)) {
						$userroleModel->data($data);
						$userroleModel->save();
					}
				}
			}
			return true;
		}


		/**
		 * 获取用户的访问记录
		 * @param $user_id
		 * @return array
		 */
		protected function getAccessList($user_id)
		{
			if ($user_id == 1) {
				$rows = [];
				$total_count = 0;
			} else {
				$page = input("param.page", 1);
				$limit = input("param.limit", $this->default_length);
				$search = input("param.search", "");
				$map = ["user_id" => $user_id, "status" => 1];
				try {
					$total_count = $this->model_userlogin->where($map)->count();
					$rows = $this->model_userlogin->where($map)->order($this->login_history_sort)->page($page, $limit)->select();
					$rows = to_array($rows);
				} catch (Exception $e) {
					$total_count = 0;
					$rows = [];
				}
				$rows = $this->login_list_deal($rows);
			}
			return $this->responseDatatable($rows, $total_count);
		}


		protected function login_list_deal($rows)
		{
			foreach ($rows as &$row) {
				$row['login_ip'] = $row['ip']."-".ip_to_address($row['ip']);
				$row['login_time'] = time_show($row['add_time']);
			}
			return $rows;
		}


		/**
		 *用户的访问记录
		 */
		public function access()
		{
			$user_id = input("param.user_id");
			$user_id = (int)$user_id;
			if (IS_AJAX) {
				$response = $this->getAccessList($user_id);
				return $response;
			} else {
				$this->assign("user_id", $user_id);
				return $this->xfetch();
			}

		}

	}