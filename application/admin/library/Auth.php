<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/5/9
	 * Time: 15:47
	 */

	namespace app\admin\library;

	use app\common\model\Sysconfig;
	use think\Exception;
	use think\Session;

	use think\Cookie;

	use app\admin\model\Admin;

	use think\Config;

	use app\admin\model\Menu;

	use xqy\Random;
	use think\Db;
	use app\admin\model\Role;

	use app\admin\model\UserLogs as model_userlog;

	class Auth
	{
		protected static $instance;

		protected $_error = '';

		protected $logined = false; //登录状态

		public $userid = 0;

		public $auth_menu = array();

		public $auth_privilege = array();

		public $all_menu = array();  //全部的菜单数组


		private function __construct()
		{

		}


		/**
		 * 单例模式
		 * @param array $options
		 * @return Auth
		 */
		public static function instance($options = [])
		{
			if (is_null(self::$instance)) {
				self::$instance = new self($options);
			}
			return self::$instance;
		}


		/**
		 * 检测当前控制器和方法是否匹配传递的数组
		 *
		 * @param array $arr 需要验证权限的数组
		 * @return bool
		 */
		public function match($arr = [])
		{
			$arr = is_array($arr) ? $arr : explode(',', $arr);
			if (!$arr) {
				return false;
			}
			$arr = array_map('strtolower', $arr);
			// 是否存在
			if (in_array(strtolower(ACTION), $arr) || in_array('*', $arr)) {
				return true;
			}
			// 没找到匹配
			return false;
		}


		public function getAdminInfo()
		{
			if ($this->userid > 0) {
				$admin = Admin::get($this->userid);
				$admin = to_array($admin);
			} else {
				$admin = [
					"id" => 0,
					"username" => "",
					"nickname" => "",
					"email" => ""
				];
			}
			return $admin;
		}

		/**
		 * 检测是否登录
		 *
		 * @return boolean
		 */
		public function isLogin()
		{
			if ($this->logined) {
				return true;
			}
			if (Session::has("admin_is_login") && Session::get("admin_is_login") == true && Session::has("admin_info") && Session::get("admin_info")) {
				$admin = Session::get("admin_info");
				$admin = to_array($admin);
				$db_admin = Admin::get($admin['id']);
				$db_admin = to_array($db_admin);
				if ($db_admin['status'] != 1) {
					return false;
				}
				$model_sys = new Sysconfig();
				$is_sso = $model_sys->config_value("SSO");
				if ($is_sso) {
					if ($db_admin['token'] != $admin['token']) {
						return false;
					}
				}
				$this->logined = true;
				$this->userid = $db_admin['id'];
				return true;
			}
			if (Cookie::has("admin_token")) {
				$login_token = Cookie::get("admin_token");
				$login_token_arr = explode("@", $login_token);
				if (count($login_token_arr) === 3) {
					$login_id = (int)$login_token_arr[0];
					$login_unique = (string)$login_token_arr[1];
					$login_username = (string)$login_token_arr[2];
					$admin = Admin::get(["id" => $login_id, "token" => $login_unique, "username" => $login_username]);
					$admin = to_array($admin);
					if (empty($admin) || $admin['status'] != 1) {
						return false;
					}
					$this->logined = true;
					$this->userid = $admin['id'];
					//重新登陆
					if (Cookie::has("admin_password")) {
						$is_rember = true;
						$password = Cookie::get("admin_password");
					} else {
						$is_rember = false;
						$password = false;
					}
					$this->dealLogin($admin, $is_rember, $password);
					//登记用户的登记记录

					$model_userlog = new model_userlog();

					return true;
				}
			}
			return false;
		}


		/**
		 * 用户完成登录后的操作
		 * @param $admin
		 */
		protected function dealLogin($admin, $is_remember, $password)
		{
			if (!is_array($admin)) {
				$admin = to_array($admin);
			}
			Session::set("admin_is_login", true);
			Session::set("admin_info", $admin);
			$admin_token = $admin['id'] . "@" . $admin['token'] . "@" . $admin['username'];
			if ($admin['id'] == 1) {
				Session::set("super_admin", 1);
			}
			if ($is_remember) {
				Cookie::set("admin_username", $admin['username'], 3600 * 24 * 31 * 12);
				Cookie::set("admin_password", $password, 3600 * 24 * 31 * 12);
				Cookie::set("admin_token", $admin_token, 3600 * 24 * 7);
			} else {
				Cookie::delete("admin_username");
				Cookie::delete("admin_password");
				Cookie::set("admin_token", $admin_token, 3600 * 24);
			}
			$ip = get_ip();
			$time = date("Y-m-d H:i:s");
			$data = ["user_id" => $admin['id'], "login_time" => $time, "ip" => $ip, "create_time" => $time, "update_time" => $time];
			Db::table('user_login_history')->insert($data);

			if ($admin['id'] == 1) {
				$op_is_manager = 1;
				$is_show = 0;
			} else {
				$op_is_manager = 1;
				$is_show = 1;
			}
			$data = [
				"menu_id" => 0,
				"op_is_manager" => $op_is_manager,
				"op_user_id" => $admin['id'],
				"op_user_account" => $admin['username'],
				"op_user_name" => $admin['nickname'],
				"is_show" => $is_show,
				"add_time" => date("Y-m-d H:i:s"),
				"create_time" => date("Y-m-d H:i:s"),
				"update_time" => date("Y-m-d H:i:s"),
				"title" => $admin['nickname'] . "登录",
				"op_result" => 1,
				"op_result_desc" => "登录成功",
				"ip" => get_ip()
			];
			Db::table('user_logs')->insert($data);
			return true;
		}


		/**
		 * 设置登录后的 权限数组 和菜单
		 * @param $request_menu_id 当前请求的menu_id
		 * @return bool
		 * @throws \think\exception\DbException
		 */
		public function setAuthMenuPrivilege()
		{
			if ($this->isSuperAdmin()) {
				$menu = Menu::getMenu(true);
				$this->all_menu = $menu;
				$this->auth_menu = $menu;
				$this->auth_privilege = "all"; //代表全部权限
				return true;
			} else {
				$menu = Menu::getMenu(false);
				$this->all_menu = $menu;
				$admin_roles = Admin::get($this->userid)->roles;
				$user_privilege_arr = array();
				if (!empty($admin_roles)) {
					$admin_roles = $admin_roles->role_ids;
					$admin_roles = explode(",", $admin_roles); //
					if (!empty($admin_roles)) {
						foreach ($admin_roles as $role) {
							$role_row = Role::get($role);
							if (empty($role_row) || $role_row['status'] != 1) {
								continue;
							} else {
								$role_privilege_arr = $role_row->rolePrivilege;
								if (!empty($role_privilege_arr)) { //没有找到相关权限
									foreach ($role_privilege_arr as $privilege_row) {
										$menu_id = $privilege_row->menu_id;
										$rules = $privilege_row->rules;
										if (!empty($rules)) {
											$rules = explode(",", $rules);
											foreach ($rules as $rule) {
												if (isset($user_privilege_arr[$menu_id]) && in_array($rule, $user_privilege_arr[$menu_id])) {
													continue;
												} else {
													$user_privilege_arr[$menu_id][] = $rule;
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$show_rule_id_arr = [];//具有查看权利的
				foreach ($user_privilege_arr as $menu_id => $rule_rows) {
					if (in_array("ADMIN", $rule_rows) || in_array("SHOW", $rule_rows)) {
						$show_rule_id_arr[] = $menu_id;
					} else {
						continue;
					}
				}
				if (!empty($show_rule_id_arr)) {
					$parent_menus = Menu::where("id", "in", $show_rule_id_arr)->column("parent_id");
					if (!empty($parent_menus)) {
						foreach ($parent_menus as $parent_menu) {
							if ($parent_menu != 0 && !in_array($parent_menu, $show_rule_id_arr)) {
								$show_rule_id_arr[] = $parent_menu;
							}
						}
					}
				}
				foreach ($menu as $menu_id => $menu_row) {
					if (!in_array($menu_id, $show_rule_id_arr)) {
						unset($menu[$menu_id]);
					} else {
						foreach ($menu_row['child'] as $child_id => $child_rows) {
							if (!in_array($child_id, $show_rule_id_arr)) {
								unset($menu[$menu_id]['child'][$child_id]);
							}
						}
					}
				}
				$this->auth_menu = $menu;
				$this->auth_privilege = $user_privilege_arr; //获取到用户的权限
			}
		}


		/***
		 * 是否是超级管理员
		 * @return bool
		 */
		public function isSuperAdmin()
		{
//			return false;
			if ($this->userid == 1 || Session::get("super_admin")) {
				return true;
			} else {
				return false;
			}
		}


		public function checkPrivilege($menu_id, $controller = "", $method = "")
		{
			$controller = strtolower($controller);
			$method = strtolower($method);
			$menu_row = Menu::get($menu_id);
			$privilege_arr = $this->getAuthPrivilege();
			if ($menu_id < 0 || empty($menu_row) || !isset($privilege_arr[$menu_id])) {
				return false;
			}
			$rule_arr = $privilege_arr[$menu_id];
			if (isset($this->all_menu[$menu_id])) {
				$rule_role = $this->all_menu[$menu_id];
			} elseif (isset($this->all_menu[$menu_row['parent_id']]) && isset($this->all_menu[$menu_row['parent_id']]['child'][$menu_id])) {//字迹
				$rule_role = $this->all_menu[$menu_row['parent_id']]['child'][$menu_id];
			} else {
				return false;
			}
			$methods = $rule_role['methods'] ?: [];
			if ($rule_role['controller'] != $controller || !isset($methods[$method])) {
				return false;
			} else {
				$need_rule = $methods[$method]['rule'];
				if (in_array("ADMIN", $rule_arr) || in_array($need_rule, $rule_arr)) {
					return true;
				}
			}
			return false;
		}


		/**
		 * 获取登录后的权限验证
		 * @return array
		 */
		public function getAuthPrivilege()
		{
			return $this->auth_privilege ? $this->auth_privilege : [];
		}


		/**
		 * 获取验证过的数组
		 * @return array
		 */
		public function getAuthMenu()
		{
			return $this->auth_menu ? $this->auth_menu : [];
		}


		/**
		 * 注销登录
		 */
		public function logout()
		{
			$this->logined = false; //重置登录状态
			Session::delete("admin_is_login");
			Session::delete("admin_info");
			Cookie::delete("admin_token");
			Session::delete("super_admin");
			return true;
		}


		/**
		 * 密码加密
		 * @param $value
		 * @return bool|mixed|string
		 */
		public function encryptPassword($value)
		{
			$encrypt_str = password_hash($value, PASSWORD_BCRYPT);
			$encrypt_str = str_replace("$", "#", $encrypt_str);
			return $encrypt_str;
		}

		/**
		 * 密码验证
		 * @param $value
		 * @param $encrypt_str
		 * @return bool
		 */
		private function verifyPassword($value, $encrypt_str)
		{
			return password_verify($value, str_replace('#', '$', $encrypt_str));
		}


		/**
		 * 管理员登录
		 *
		 * @param string $username 用户名
		 * @param string $password 密码
		 * @return  boolean
		 */
		public function login($username, $password, $is_remember)
		{
			try {
				$admin = Admin::get(['username' => $username]);
				if (!$admin) {
					return '用户名或密码错误';
				}
				if ($admin->status == 0) {
					return "该账户已经被禁用";
				}
				$model_sys = new Sysconfig();
				$login_failure = (int)($model_sys->config_value("max_loginfailure"));
				if ($login_failure >= 5 && $admin->loginfailure >= 2 && time() - strtotime($admin->update_time) < 86400) {
					return '错误次数较多,请一天后再试!';
				}
				if (!$this->verifyPassword($password, $admin['password'])) {
					$admin->loginfailure = $admin->loginfailure + 1;
					$admin->save();
					return "用户名或密码错误";
					return false;
				}
				$admin->loginfailure = 0; //重置登录失败错误的次数
				$admin->last_login_time = date("Y-m-d H:i:s", time());
				$admin->last_login_ip = request()->ip();
				$admin->token = md5(uniqid());
				$admin->save();
				$this->dealLogin($admin, $is_remember, $password);
				return true;
			} catch (Exception $exception) {
				return '用户名或密码错误';
			}
		}


		/**
		 * 设置错误信息
		 *
		 * @param string $error 错误信息
		 * @return Auth
		 */
		public function setError($error)
		{
			$this->_error = $error;
			return $this;
		}

		/**
		 * 获取错误信息
		 * @return string
		 */
		public function getError()
		{
			return $this->_error ? ($this->_error) : '';
		}


		private function __clone()
		{

		}


	}