<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2019/12/4
	 * Time: 17:00
	 * 该类用来编写常见方法
	 */

	namespace app\common\controller;

	use think\Config;
	use think\Controller;
	use think\Exception;
	use think\Loader;
	use think\Session;
	use think\Request;
	use app\common\controller\Base;
	use think\exception\PDOException;

	use app\admin\library\Auth;
	use app\common\library\upload\Upload;
	use think\Db;

	use app\admin\model\UserLogs as model_user_log;

	class Backbase extends Base
	{
		protected $default_sort = "orders asc,add_time desc";  //默认的排序方式就是 权重 添加时间

		protected $default_length = 20; //默认显示20条

		protected $where_map = []; //是否有其他的条件

		protected $where_menu = true; //是否需要加上menu_id 的查询条件  默认需要

		protected $search_field_arr = []; //默认需要搜索的数组

		protected $search_tips = "";

		//用户登录admin id
		public $admin_id = 0;
		/**
		 * 无需登录的方法,同时也就不需要鉴权了
		 * @var array
		 */
		protected $noNeedLogin = [];
		/**
		 * 无需鉴权的方法,但需要登录
		 * @var array
		 */
		protected $noNeedRight = [];

		/**
		 * 布局模板
		 * @var string
		 */
		protected $layout = 'default';

		/**
		 * 权限控制类
		 * @var Auth
		 */
		protected $auth = null;

		/**
		 * 模型对象
		 * @var \think\Model
		 */
		public $model = null;


		/**
		 * 分类模型
		 * @var \think\Model
		 */
		public $class_model = null;


		/**
		 * 快速搜索时执行查找的字段
		 */
		protected $searchFields = 'id';

		protected $default_row = [];

		public $model_log = null;


		public $flag_field_arr = ['key' => "title", "desc" => "标题"]; //标志性字段 用作记录错误日志时候使用

		public $field_desc = []; //用作setText 操作记录使用


		//默认的分类
		protected $class_default_row = [
			"parent_id" => 0,
			"title" => "",
			"image_ids" => 0,
			"add_time" => "",
			"orders" => 0,
			"status" => 1
		];

		protected $is_add_edit_same_templete = true; // 新增与修改是否是使用同一个模板

		protected $class_is_add_edit_same_templete = true; //分类新增与修改是否是使用同一个模板


		public function _initialize()
		{
			defined("IS_ADMIN_REQUEST") or define("IS_ADMIN_REQUEST", true);
			parent::_initialize();
			$request = Request::instance();
			$menu_id = $request->param('m');
			$menu_id = (int)$menu_id;
			$cid = $request->param('cid');
			$cid = (int)$cid;
			defined("MENU_ID") or define("MENU_ID", $menu_id);
			$is_menu_class = is_menu_class();
			defined("IS_SHOW_CLASS") or define("IS_SHOW_CLASS", $is_menu_class);
			defined("CURRENT_CLASS_ID") or define("CURRENT_CLASS_ID", $cid);
			//登录以及权限的验证开始
			$this->auth = Auth::instance();
			//需要验证登录
			if (!$this->auth->match($this->noNeedLogin)) {
				if (!$this->auth->isLogin()) {
					$referer_url = url(CONTROLLER . "/" . ACTION);
					Session::set("referer", $referer_url);
					if (!IS_AJAX) {
						$this->redirect("index/login");
						exit;
					} else {
						echo $this->failJsonResponse("请先登录");
						exit;
					}
				}
				//权限验证开始
				$this->auth->setAuthMenuPrivilege(); //登录后设置菜单
				if (!$this->auth->isSuperAdmin()) {
					if (!$this->auth->match($this->noNeedRight)) { //进行权限验证
						if (!$this->auth->checkPrivilege($menu_id, CONTROLLER, ACTION)) {
							if (!IS_AJAX) {
								echo $this->xfetch('common/no_auth');
								exit;
//                                $this->error('你没有权限访问', url('index/index'));
							} else {
								echo $this->failJsonResponse("你没有权限访问");
								exit;
							}
						}
					}
				}
				//权限验证结束
			}
			$user_menu = $this->auth->getAuthMenu();
			$admin_info = $this->auth->getAdminInfo();
			$this->admin_id = $admin_info['id'];
			$this->assign([
				'admin_info' => $admin_info,
				'admin_menu' => $user_menu
			]);
			$this->model_log = new model_user_log();
			//登录以及权限的验证结束
		}


		/**
		 * @param string $view 渲染的模本目录
		 * @param int $lang_mode 语言默认 默认自动自动获取到 LANG 里面的 lang变量
		 * @return mixed
		 */
		protected function xfetch($view = "")
		{
			if (!empty($view)) {
				return $this->fetch($view);
			} else {
				return $this->fetch();
			}
		}


		/**
		 * 获取datatable数据列表
		 */
		protected function getList()
		{
			$page = input("param.page", 1);
			$limit = input("param.limit", $this->default_length);
			$search = input("param.search", "");
			$map = $this->where_map;
			if (!empty($search) && !empty($this->search_field_arr)) {
				$search_filed = implode("|", $this->search_field_arr);
				$map[$search_filed] = ["like", "%" . $search . "%", "or"];
				$map['status'] = ['<>', 2];
			} else {
				$map['status'] = ['<>', 2];
			}
			if ($this->where_menu) {
				$map['menu_id'] = MENU_ID;
			}
			$current_class_id = get_class_id();
			if ((empty($search) || empty($this->search_field_arr)) && !empty($this->class_model) && !empty($current_class_id)) {
				//查找所有的parent_id;
				$current_class_row = $this->class_model->get($current_class_id);
				$current_class_row = to_array($current_class_row);
				$child_ids = $this->getChildClass([$current_class_row]);
				if (!empty($child_ids)) {
					$map['class_id'] = ["in", $child_ids];
				}
			}
			try {
				$total_count = $this->model->where($map)->count();
				$rows = $this->model->where($map)->order($this->default_sort)->page($page, $limit)->select();
				$rows = to_array($rows);
			} catch (Exception $e) {
				$total_count = 0;
				$rows = [];
			}
			$rows = $this->list_deal($rows);
			return $this->responseDatatable($rows, $total_count);
		}


		protected function list_deal($rows)
		{
			foreach ($rows as &$row) {
				if (isset($row['add_time'])) {
					$row['add_time'] = time_show($row['add_time']);
				}
				if (isset($row['image_ids'])) {
					$row['images'] = get_cover_image($row['image_ids']);
				}
			}
			return $rows;
		}


		/**
		 * 列表页
		 * @return array|mixed
		 */
		public function index()
		{
			if (IS_AJAX) {
				$response = $this->getList();
				return $response;
			} else {
				$table_name = $this->model->getTable();
				$left_content_nav = left_content_nav(IS_SHOW_CLASS, $table_name);
				$this->assign("left_content_nav", $left_content_nav);
				$this->assign("search_tips", $this->search_tips);
				return $this->xfetch();
			}
		}


		protected function delete_items($ids)
		{
			$ids = ids_filter_not_int($ids);
			if (empty($ids)) {
				return array("result" => false, "msg" => "没找到相关数据", "ids" => $ids);
			}
			$pk = $this->model->getPk();
			try {
				$list = $this->model->where($pk, 'in', $ids)->select();
				foreach ($list as $k => $v) {
					$v->save(['status' => 2]);
				}
			} catch (Exception $e) {
				return array("result" => false, "msg" => $e->getMessage(), "ids" => $ids);
			}
			return array("result" => true, "msg" => "", "ids" => $ids);
		}

		/**
		 * 删除数据
		 * @param int $ids
		 * @return mixed
		 */
		public function delete($ids = 0)
		{
			$delete_resullt = $this->delete_items($ids);
			if ($delete_resullt['result']) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["ids" => $ids]]);
				return $this->successJsonResponse("删除成功");
			} else {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $delete_resullt['msg']], 'p' => ["ids" => $ids]]);
				return $this->failJsonResponse("删除失败：" . $delete_resullt['msg']);
			}
		}


		/**
		 * 默认数据处理方法
		 * @param $data
		 */
		protected function opt_deal(&$data)
		{
			//使用默认的处理方法
			$pk = $this->model->getPk();
			if (isset($data[$pk])) {
				unset($data[$pk]); //如果有id 删除ID
			}
			if (isset($data['menu_id'])) {
				unset($data['menu_id']); //如果有menu_id 删除
			}
			$data['menu_id'] = MENU_ID;
			if (IS_ADD && empty($data['add_time'])) {
				$data['add_time'] = date("Y-m-d H:i:s");
			}
			//对图片的处理 默认处理封面图
			$data = $this->image_deal($data, "image_ids");

			//对class_id的处理
			$allow_fields = $this->getAllowEditField();
			if (in_array("class_id", $allow_fields)) {
				if (!isset($data['class_id'])) {
					$data['class_id'] = CURRENT_CLASS_ID;
				}
			} else {
				if (isset($data['class_id'])) {
					unset($data['class_id']);
				}
			}
			//如果存在class_id 就进一步验证class_id的属性设置 stutus的值
			if (!empty($this->class_model) && isset($data['class_id']) && $data['status'] == 1) {
				$class_id = $data['class_id'];
				try {
					//判断是否有分类
					$class_row = $this->class_model->where('class_id', $class_id)->where("status", '<>', 2)->find();
					if (!empty($class_row) && ($class_row['status'] == 0 || $class_row['status'] == 3)) {
						$data['status'] = 3;//代表父类状态无效
					}
				} catch (Exception $e) {
				}
			}
			return true;
		}


		/**
		 * 默认的上传处理函数 可以重写
		 * @param $data
		 */
		protected function opt($data)
		{
			$result = ["result" => true, "msg" => "", "data" => []];
			if (method_exists($this, "opt_check")) {
				$error_msg = $this->opt_check($data);
				if ($error_msg !== true) {
					$result['result'] = false;
					$result['msg'] = $error_msg;
					return $result;
				}
			}

			if (method_exists($this, "opt_deal")) {
				$this->opt_deal($data);
			}
			$result['msg'] = "成功";
			$result['data'] = $data;
			return $result;
		}


		protected function opt_assigned($row)
		{
			$add_time = date("Y-m-d H:i:s");
			$current_id = get_class_id();
			if (isset($row['add_time']) && IS_ADD) {
				$row['add_time'] = $add_time;
			}
			if (!IS_ADD) {
				if (!empty($row['class_id']) && !empty($this->class_model)) {
					$class_row = $this->class_model->get($row['class_id']);
					$row['class_name'] = (string)$class_row['title'];
				} else {
					$row['class_id'] = 0;
					$row['class_name'] = "无";
				}
			} else {
				if (!empty($current_id) && !empty($this->class_model)) {
					$row['class_id'] = $current_id;
					$class_row = $this->class_model->get($current_id);
					$row['class_name'] = $class_row['title'];
				} else {
					$row['class_id'] = 0;
					$row['class_name'] = "无";
				}
			}
			return ["row" => $row];
		}

		public function add()
		{
			defined("IS_ADD") or define("IS_ADD", true);
			try {
				$row = $this->default_row;
				if (IS_AJAX) {
					$post_data = $this->request->post();
					$opt_result = $this->opt($post_data);
					if (!$opt_result['result']) {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $opt_result['msg']], 'p' => ["id" => 0, "post" => $post_data]]);
						return $this->failJsonResponse($opt_result['msg']);
					} else {
						try {
							$post_data = $opt_result['data'];
							$pk = $this->model->getPk();
							$this->model->data($post_data);
							$this->model->save();
							$opt_id = $this->model->getLastInsID();
							if (method_exists($this, "opt_after")) {
								$this->opt_after($opt_id);
							}
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $opt_id, "post" => $post_data]]);
							return $this->successJsonResponse("添加成功");
						} catch (Exception $e) {
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["id" => 0, "post" => $post_data]]);
							return $this->failJsonResponse("添加失败:" . $e->getMessage());
						}
					}
				} else {
					if (method_exists($this, "opt_assigned")) {
						$assigned_arr = $this->opt_assigned($row);
					}
					$this->assign($assigned_arr);
					return $this->xfetch();
				}
			} catch (Exception $e) {
				return $this->failJsonResponse($e->getMessage());
			}
		}


		public function edit($id)
		{
			$id = (int)$id;
			defined("IS_ADD") or define("IS_ADD", false);
			try {
				$row = $this->model->get($id);
				$row = to_array($row);
				if (IS_AJAX) {
					$post_data = $this->request->post();
					$opt_result = $this->opt($post_data);
					if (!$opt_result['result']) {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $opt_result['msg']], 'p' => ["id" => $id, "post" => $post_data]]);
						return $this->failJsonResponse($opt_result['msg']);
					} else {
						try {
							$pk = $this->model->getPk();
							$this->model->save($opt_result['data'], [$pk => $id]);
							if (method_exists($this, "opt_after")) {
								$this->opt_after($id);
							}
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $id, "post" => $post_data]]);
							return $this->successJsonResponse("修改成功");
						} catch (Exception $e) {
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["id" => $id, "post" => $post_data]]);
							return $this->failJsonResponse("修改失败:" . $e->getMessage());
						}
					}
				} else {
					if (method_exists($this, "opt_assigned")) {
						$assigned_arr = $this->opt_assigned($row);
					} else {
						$assigned_arr = ["row" => $row];
					}
					$this->assign($assigned_arr);
					if ($this->is_add_edit_same_templete) {
						return $this->xfetch("add");
					} else {
						return $this->xfetch();
					}
				}
			} catch (Exception $e) {
				return $this->failJsonResponse($e->getMessage());
			}
		}


		protected function getAllowEditField()
		{
			if (!empty($this->allowFields)) {
				return $this->allowFields;
			} else {
				return $this->model->getTableFields();
			}
		}


		/**
		 * 修改单元格编辑框数据
		 * @param $id
		 * @param $txt
		 * @param $field
		 * @return mixed
		 */
		public function setText()
		{
			$id = input("param.id");
			$id = (int)$id;
			$value = input("param.value");
			$field = input("param.field");
			try {
				$row = $this->model->get($id);
			} catch (Exception $e) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到相关数据"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->failJsonResponse("没找到相关数据");
			}
			if (!in_array($field, $this->getAllowEditField())) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到相关字段"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->failJsonResponse("没找到相关字段");
			}
			$row->$field = $value;
			$result = $row->save();
			if ($result !== false) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->successJsonResponse("编辑成功", array("value" => $value));
			} else {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "编辑失败"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->failJsonResponse("编辑失败");
			}
		}


		/**
		 * 设置状态是否有效
		 * @param int $id
		 * @param int $status
		 * @return mixed
		 */
		public function setStatus()
		{
			$id = input("param.id");
			$id = (int)$id;
			$value = input("param.value");
			$field = input("param.field");
			if ($value != 0) {
				$value = 1;
			}
			try {
				$row = $this->model->get($id);
			} catch (Exception $e) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到相关数据"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->failJsonResponse("没找到相关数据");
			}
			if (!in_array($field, $this->getAllowEditField())) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到相关字段"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->failJsonResponse("没找到相关字段");
			}
			$row2 = to_array($row);
			if (!empty($this->class_model) && $field == 'status' && isset($row2['class_id']) && $row2['class_id'] > 0 && $value == 1) {
				//检查父类的class_id 是否正常
				$class_id = $row2['class_id'];
				try {
					//判断是否有分类
					$class_row = $this->class_model->where('class_id', $class_id)->where("status", '<>', 2)->find();
					if (!empty($class_row) && ($class_row['status'] == 0 || $class_row['status'] == 3)) {
						$value = 3;//代表父类状态无效
					}
				} catch (Exception $e) {
				}
			}
			$row->$field = $value;
			$result = $row->save();
			if ($result !== false) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->successJsonResponse("编辑成功", array("value" => $value));
			} else {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "编辑失败"], 'p' => ["id" => $id, "field" => $field, "value" => $value]]);
				return $this->successJsonResponse("编辑失败");
			}
		}


		//图片的处理方式
		protected function image_deal($data, $field)
		{
			if (isset($data[$field])) {
				$c_upload = new Upload();
				$image_ids = $c_upload->insert_data($field);
				$data[$field] = $image_ids;
				$field_title = $field . "_title";
				unset($data[$field_title]); //要删除区的图片标题
			} else {
				$allow_field = $this->getAllowEditField();
				if (in_array($field, $allow_field)) {
					$data[$field] = "";
				}
			}
			return $data;
		}


		//分类处理函数 开始
		private function getChildClass($class_row_arr)
		{
			$output = array();
			foreach ($class_row_arr as $k => $v) {
				$tmpRes = $this->class_model->where('parent_id', $v['class_id'])->where("status", "<>", 2)->select(); //删除
				$output [] = $v['class_id'];
				if (!empty($tmpRes)) {
					$tmpRes = to_array($tmpRes);
					$output = array_merge($output, $this->getChildClass($tmpRes));
				}
			}
			return $output;
		}


		/**
		 * 默认数据处理方法
		 * @param $data
		 */
		protected function class_opt_deal(&$data)
		{
			//使用默认的处理方法
			$pk = $this->class_model->getPk();
			if (isset($data[$pk])) {
				unset($data[$pk]); //如果有id 删除ID
			}
			if (isset($data['menu_id'])) {
				unset($data['menu_id']); //如果有menu_id 删除
			}
			$data['menu_id'] = MENU_ID;
			if (CLASS_IS_ADD && empty($data['add_time'])) {
				$data['add_time'] = date("Y-m-d H:i:s");
			}
			//对图片的处理 默认处理封面图
			$data = $this->image_deal($data, "image_ids");

			//进一步验证parent_id 的情况
			if ($data['status'] == 1) {
				$class_id = $data['parent_id'];
				try {
					//判断是否有分类
					$class_row = $this->class_model->where('class_id', $class_id)->where("status", '<>', 2)->find();
					if (!empty($class_row) && ($class_row['status'] == 0 || $class_row['status'] == 3)) {
						$data['status'] = 3;//代表父类状态依然是无效
					}
				} catch (Exception $e) {
				}
			}
			return true;
		}


		/**
		 * 默认的上传处理函数 可以重写
		 * @param $data
		 */
		protected function classOpt($data)
		{
			$result = ["result" => true, "msg" => "", "data" => []];
			if (method_exists($this, "class_opt_check")) {
				$error_msg = $this->class_opt_check($data);
				if ($error_msg !== true) {
					$result['result'] = false;
					$result['msg'] = $error_msg;
					return $result;
				}
			}
			if (method_exists($this, "class_opt_deal")) {
				$this->class_opt_deal($data);
			}
			$result['msg'] = "成功";
			$result['data'] = $data;
			return $result;
		}


		public function delClass()
		{
			$class_id = CURRENT_CLASS_ID;
			try {
				$time = get_time();
				//获取所有的子分类
				$class_row = $this->class_model->where('class_id', $class_id)->where("status", "<>", 2)->find(); //删除
				if (empty($class_row)) {
					$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => '没找到该分类'], 'p' => ["ids" => $class_id]]);
					return $this->successJsonResponse("没找到该分类");
				} else {
					$class_row = to_array($class_row);
					$arr = [$class_row];
					$child_arr = $this->getChildClass($arr);
					$child_arr = array_unique($child_arr);
					$deleted_result = $this->class_model->where('class_id', "in", $child_arr)->update(["update_time" => $time, 'status' => 2]); //删除
					$deleted_result = (int)$deleted_result;
					if ($deleted_result > 0) {
						$this->model->where("class_id", "in", $child_arr)->update(["status" => 4, "update_time" => $time]);
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["ids" => $class_id]]);
						return $this->successJsonResponse("删除分类成功");
					} else {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0], 'p' => ["ids" => $class_id]]);
						return $this->failJsonResponse("删除分类失败");
					}
				}
			} catch (Exception $exception) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $exception->getMessage()], 'p' => ["ids" => $class_id]]);
				return $this->successJsonResponse("删除失败" . $exception->getMessage());
			}
		}


		protected function getTreeClass($arr, $pid, $level)
		{
			$list = array();
			foreach ($arr as $k => $v) {
				if ($v['parent_id'] == $pid) {
					$v['level'] = $level;
					$v['child'] = $this->getTreeClass($arr, $v['class_id'], $level + 1);
					$list[] = $v;
				}
			}
			return $list;
		}

		/**
		 * 改变分类的status状态
		 * @param $class_row_arr
		 * @param $parent_status
		 */
		private function ChangeClassStatus($class_row_arr, $parent_status)
		{
			$time = date("Y-m-d H:i:s");
			foreach ($class_row_arr as $k => $row) {
				if ($parent_status == 0) {
					if ($row['status'] == 0) {
						//不用变
					} elseif ($row['status'] == 1) {
						$one_row = $this->class_model->get($row['class_id']);
						$one_row->status = 3;
						$one_row->save();
						$this->model->where('class_id', $row['class_id'])->where("status", 1)->update(['status' => 3, "update_time" => $time]);
						$row['status'] = 3;
					} elseif ($row['status'] == 3) {
						//不用变这个
					}
				} elseif ($parent_status == 1) {
					//子类
					if ($row['status'] == 0) {
						//不用变
					} elseif ($row['status'] == 1) {
						//不用变
					} elseif ($row['status'] == 3) {

						$one_row = $this->class_model->get($row['class_id']);
						$one_row->status = 1;
						$one_row->save();

						$this->model->where('class_id', $row['class_id'])->where("status", 3)->update(['status' => 1, "update_time" => $time]);

//                        $this->model->save(["status" => 1], ["class_id" => $row['class_id'], "status" => 3]);
						$row['status'] = 1;
					}
				} elseif ($parent_status == 3) {
					//子类
					if ($row['status'] == 0) {
						//不用变
					} elseif ($row['status'] == 1) {

						$one_row = $this->class_model->get($row['class_id']);
						$one_row->status = 3;
						$one_row->save();

						$this->model->where('class_id', $row['class_id'])->where("status", 1)->update(['status' => 3, "update_time" => $time]);
//                        $this->model->save(["status" => 3], ["class_id" => $row['class_id'], "status" => 1]); //删除
						$row['status'] = 3;
					} elseif ($row['status'] == 3) {
						//不用变这个
					}
				}
				if (isset($row['child']) && !empty($row['child'])) {
					$this->ChangeClassStatus($row['child'], $row['status']);
				}
			}

		}

		/**
		 * 默认的修改完分类后要进行的操作
		 * @param $cid
		 * @throws \think\exception\DbException
		 */
		protected function class_opt_after($cid)
		{
			$class_row = $this->class_model->get($cid);
			$class_row = to_array($class_row);
			$status = $class_row['status'];
			$arr = [$class_row];
			$child_ids_arr = $this->getChildClass($arr);
			$child_ids_arr = array_unique($child_ids_arr);
			array_shift($child_ids_arr);
			if ($status == 0) {
				$time = date("Y-m-d H:i:s");
				$this->model->where('class_id', $cid)->where("status", 1)->update(['status' => 3, "update_time" => $time]);
			}
			if (!empty($child_ids_arr)) {
				$child_arr = $this->class_model->where("class_id", "in", $child_ids_arr)->select();
				$child_arr = to_array($child_arr);
				$child_arr = $this->getTreeClass($child_arr, $cid, 0);
				$this->ChangeClassStatus($child_arr, $status);
			}
			if (isset($class_row['parent_id']) && isset($class_row['class_deep'])) {
				$parent_id = $class_row['parent_id'];
				$class_deep = "";
				if ($parent_id == 0) {
					$class_deep = $class_row['class_id'];
				} else {
					$fid = $parent_id;
					$fid_row = $this->class_model->get($fid);
					$fid_row = to_array($fid_row);
					$class_deep = $fid_row['class_deep'];
					$class_deep = $class_deep . "," . $class_row['class_id'];
				}
				$pk = $this->class_model->getPk();
				$arr2 = ["class_deep" => $class_deep];
				$this->class_model->save($arr2, [$pk => $class_row['class_id']]);
			}

		}


		public function editClass($cid)
		{
			$class_id = (int)$cid;
			defined("CLASS_IS_ADD") or define("CLASS_IS_ADD", false);
			try {
				$row = $this->class_model->where('class_id', $class_id)->where("status", "<>", 2)->find(); //删除
				$row = to_array($row);
				if (IS_AJAX) {
					$post_data = $this->request->post(); //获取到的提交数据
					$opt_result = $this->classOpt($post_data);
					if (!$opt_result['result']) {
						return $this->failJsonResponse($opt_result['msg']);
					} else {
						try {
							$pk = $this->class_model->getPk();
							$this->class_model->save($opt_result['data'], [$pk => $cid]);
							if (method_exists($this, "class_opt_after")) {
								$this->class_opt_after($cid);
							}
							$data = $this->class_model->get($cid);
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $class_id, "post" => $post_data]]);
							return $this->successJsonResponse("修改成功", $data);
						} catch (Exception $e) {
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["id" => $class_id, "post" => $post_data]]);
							return $this->failJsonResponse("修改失败:" . $e->getMessage());
						}
					}
				} else {
					if (method_exists($this, "class_opt_assigned")) {
						$assigned_arr = $this->class_opt_assigned($row);
					} else {
						$assigned_arr = ["row" => $row];
					}
					$this->assign($assigned_arr);
					if ($this->class_is_add_edit_same_templete) {
						return $this->xfetch("class_add");
					} else {
						return $this->xfetch();
					}
				}
			} catch (Exception $e) {
				return $this->failJsonResponse($e->getMessage());
			}
		}

		//分类处理函数结束


		public function addClass($cid)
		{
			$class_id = (int)$cid;
			defined("CLASS_IS_ADD") or define("CLASS_IS_ADD", true);
			try {
				$row = $this->class_default_row;
				if (IS_AJAX) {
					$post_data = $this->request->post(); //获取到的提交数据
					$opt_result = $this->classOpt($post_data);
					if (!$opt_result['result']) {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $opt_result['msg']], 'p' => ["id" => 0, "post" => $post_data]]);
						return $this->failJsonResponse($opt_result['msg']);
					} else {
						try {
							$pk = $this->class_model->getPk();
							$this->class_model->data($opt_result['data']);
							$this->class_model->save();
							$class_id = $this->class_model->getLastInsID();
							if (method_exists($this, "class_opt_after")) {
								$this->class_opt_after($class_id);
							}
							$data = $this->class_model->get($class_id);
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["id" => $class_id, "post" => $post_data]]);
							return $this->successJsonResponse("新增成功", $data);
						} catch (Exception $e) {
							$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["id" => 0, "post" => $post_data]]);
							return $this->failJsonResponse("新增失败:" . $e->getMessage());
						}
					}
				} else {
					$parent_id = input("param.parent");
					if (!empty($parent_id)) {
						$row['parent_id'] = $parent_id;
					}
					if (method_exists($this, "class_opt_assigned")) {
						$assigned_arr = $this->class_opt_assigned($row);
					} else {
						$assigned_arr = ["row" => $row];
					}
					$this->assign($assigned_arr);
					return $this->xfetch("class_add");
				}
			} catch (Exception $e) {
				return $this->failJsonResponse($e->getMessage());
			}
		}


		/**
		 * 移动分类
		 */
		public function moveClass($source_id, $target_id, $type)
		{
			$source_id = (int)$source_id;
			$target_id = (int)$target_id;
			$type = (string)$type;
			$type_arr = ["inner", "prev", "next"];
			if (!in_array($type, $type_arr)) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "操作方法错误,只能为inner prev next"], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
				return $this->failJsonResponse("操作方法错误,只能为inner prev next");
			}
			$class_deep = get_class_deep(); //能允许多少层
			//现在source 多少层
			//target_id 上面多少层
			if (empty($class_deep)) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "栏目不允许进行分类操作"], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
				return $this->failJsonResponse("栏目不允许进行分类操作");
			}
			try {
				$source_row = $this->class_model->where('class_id', $source_id)->where("status", "<>", 2)->find(); //删除
				$source_row = to_array($source_row);
				if (empty($source_row)) {
					$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到source数据"], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
					return $this->failJsonResponse("没找到source数据");
				}
			} catch (Exception $e) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
				return $this->failJsonResponse($e->getMessage());
			}
			try {
				$target_row = $this->class_model->where('class_id', $target_id)->where("status", "<>", 2)->find(); //删除
				$target_row = to_array($target_row);
				if (empty($target_row)) {
					$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "没找到target数据"], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
					return $this->failJsonResponse("没找到target数据");
				}
			} catch (Exception $e) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type]]);
				return $this->failJsonResponse($e->getMessage());
			}
			if (!empty($class_deep) && isset($source_row['class_deep']) && isset($target_row['class_deep'])) {
				$target_row_deep = $target_row['class_deep'];
				$target_row_deep = explode(",", $target_row_deep);
				$target_row_deep_count = count($target_row_deep);
				$source_deep_arr = $this->class_model->where('find_in_set(:class_id,class_deep)', ["class_id" => $source_id])->where("status", "<>", 2)->select();
				if (!empty($source_deep_arr)) {
					$source_deep_arr = to_array($source_deep_arr);
					$source_deep_count = 1; //默认自己一层
					foreach ($source_deep_arr as $source_deep_row) {
						$deep = $source_deep_row['class_deep'];
						$deep = explode(",", $deep);
						foreach ($deep as $index => $item) {
							if ($item == $source_id) {
								$tmp = count($deep) - $index;
								$source_deep_count = max($source_deep_count, $tmp);
								break;
							}
						}
					}
				}
				$qianzui = "";
				if ($type == 'inner') {
					//成为子级
					$heji_deep = (int)$target_row_deep_count + (int)$source_deep_count;
					if ($heji_deep > $class_deep) {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "转移失败,超过允许层数<br/>产品结构只允许" . $class_deep . "层,移动后会出现最多" . $heji_deep . "层"], 'p' => ["source_id" => $source_id, "source_row" => $source_row, "target_row" => $target_row, "target_id" => $target_id, "type" => $type]]);
						return $this->failJsonResponse("转移失败,超过允许层数<br/>产品结构只允许" . $class_deep . "层,移动后会出现最多" . $heji_deep . "层");
					} else {
						$qianzui = $target_row['class_deep'];
					}
				} else {
					$heji_deep = (int)($target_row_deep_count - 1) + (int)$source_deep_count;
					if ($heji_deep > $class_deep) {
						$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => "转移失败,超过允许层数<br/>产品结构只允许" . $class_deep . "层,移动后会出现最多" . $heji_deep . "层"], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "type" => $type, "source_row" => $source_row, "target_row" => $target_row]]);
						return $this->failJsonResponse("转移失败,超过允许层数<br/>产品结构只允许" . $class_deep . "层,移动后会出现最多" . $heji_deep . "层");
					} else {
						if ($target_row_deep_count > 1) {
							array_pop($target_row_deep);
							$qianzui = implode(",", $target_row_deep);
						} else {
							$qianzui = "";
						}
					}
				}
			}
			//有三种操作 分别是 inner 成为子节点 prev成为同级的前一个节点 next 成为同级的后一个节点
			//进行层级的判断
			try {
				if ($type == 'inner') {
					$pk = $this->class_model->getPk();
					$this->class_model->save(["parent_id" => $target_id], [$pk => $source_id]);
				} elseif ($type == 'prev') {
					//修改add_time 减少1秒
					$pk = $this->class_model->getPk();
					$add_time = date("Y-m-d H:i:s", strtotime('-1second', strtotime($target_row['add_time'])));
					$this->class_model->save(["parent_id" => $target_row['parent_id'], "add_time" => $add_time], [$pk => $source_id]);
				} elseif ($type == 'next') {
					//修改add_time 增加1秒
					$pk = $this->class_model->getPk();
					$add_time = date("Y-m-d H:i:s", strtotime('+1second', strtotime($target_row['add_time'])));
					$this->class_model->save(["parent_id" => $target_row['parent_id'], "add_time" => $add_time], [$pk => $source_id]);
				}
			} catch (Exception $e) {
				$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 0, "error_msg" => $e->getMessage()], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "source_row" => $source_row, "target_row" => $target_row, "type" => $type]]);
				return $this->failJsonResponse($e->getMessage());
			}
			if (isset($qianzui)) {
				//修改class_deep
				$source_deep_arr = $this->class_model->where('find_in_set(:class_id,class_deep)', ["class_id" => $source_id])->where("status", "<>", 2)->select();
				if (!empty($source_deep_arr)) {
					$source_deep_arr = to_array($source_deep_arr);
					foreach ($source_deep_arr as $source_deep_row) {
						$deep = $source_deep_row['class_deep'];
						$deep = explode(",", $deep);
						foreach ($deep as $index => $item) {
							if ($item == $source_id) {
								//找到source_id位置 前面的都要改变
								$tmp = array_slice($deep, $index);
								$tmp = implode(",", $tmp);
								if (!empty($qianzui)) {
									$tmp = $qianzui . "," . $tmp;
								}
								$pk = $this->class_model->getPk();
								$this->class_model->save(["class_deep" => $tmp], [$pk => $source_deep_row['class_id']]);
								break;
							}
						}
					}
				}
			}
			$this->userLog(['m' => __FUNCTION__, 'd' => ['op_result' => 1], 'p' => ["source_id" => $source_id, "target_id" => $target_id, "source_row" => $source_row, "target_row" => $target_row, "type" => $type]]);
			return $this->successJsonResponse("转移分类成功");
		}


		/**
		 * 记录用户的操作函数
		 */

		protected function getLogDefaultData()
		{
			$is_super_manager = $this->auth->isSuperAdmin();
			$admin_info = $this->auth->getAdminInfo();
			if ($is_super_manager) {
				$op_is_manager = 1;
				$is_show = 0;
			} else {
				$op_is_manager = 0;
				$is_show = 1;
			}
			$default_data = [
				"title" => "",
				"menu_id" => 0,
				"op_is_manager" => $op_is_manager,
				"op_user_id" => $admin_info['id'],
				"op_user_account" => $admin_info['username'],
				"op_user_name" => $admin_info['nickname'],
				"op_menu_id" => MENU_ID,
				"op_menu_title" => get_menu_title(MENU_ID),
				"is_show" => $is_show,
				"ip" => get_ip()
			];
			return $default_data;
		}

		protected function userLog($arges)
		{

			if (!isset($arges['m'])) {
				return false;
			} else {
				$method = $arges['m']; //方法
			}
			if (!isset($arges['d'])) {
				return false;
			}
			//要完整的插入到 user_log表  完整数据怎么进行拼装
			// 通过 $argss [d] 插入一部分  通过 args[p] 与 this 共同查询 数据一部分
			if (!isset($arges['d'])) {
				$data = $this->getLogDefaultData();
			} else {
				$data = $this->getLogDefaultData();
				$data = array_merge($data, $arges['d']);
			}
			if (isset($arges['p'])) {
				$params = $arges['p'];
			} else {
				$params = [];
			}
			$method_template = $method . "Template";
//            file_put_contents(__DIR__."/aa.txt",var_export($method_template,true));
			$input_args = ["data" => $data, "param" => $params, "class" => $this];
			//先查看自己方法有没有这个记录模板 有就是用
			if (method_exists($this, $method_template)) {
				call_user_func_array(array($this, $method_template), [$input_args]);
			} elseif (method_exists($this->model_log, $method_template)) {
				call_user_func_array(array($this->model_log, $method_template), [$input_args]);
			} else {

			}
			return true;
		}


	}