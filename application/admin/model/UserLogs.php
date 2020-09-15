<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/8/28
	 * Time: 10:44
	 */

	namespace app\admin\model;

	use app\common\model\BaseModel;

	class UserLogs extends BaseModel
	{
		protected $name = 'user_logs';


		public function Template($args)
		{
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象
		}


		protected function getOptTitle($id_row)
		{
			$opt_title = "";
			if (isset($id_row['title'])) {
				$opt_title = $id_row['title'];
			} elseif (isset($id_row['name'])) {
				$opt_title = $id_row['name'];
			} elseif (isset($id_row['username'])) {
				$opt_title = $id_row['username'];
			} elseif (isset($id_row['nickname'])) {
				$opt_title = $id_row['nickname'];
			}
			return $opt_title;
		}


		protected function getDefaultSetTextArr()
		{
			//将一些常见的能进行SETtEXT字段加进来
			$arr = [
				"title" => "标题",
				"orders" => "排序",
				"keywords" => "关键字",
				"nickname" => "昵称",
				"email" => "邮箱",
				"name" => "名称",
				"remark" => "描述",
				"link_url" => "跳转链接",
				"linkurl" => "跳转链接",
				"position" => "职位",
				"qq" => "QQ",
				"status" => "状态",
				"hot" => "推荐"
			];
			return $arr;
		}

		/**
		 * 删除数据默认的记录模板
		 * @param $args
		 */
		public function deleteTemplate($args)
		{
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象
			$ids = $params['ids'];
			$data['op_id'] = $ids;
			$model = $controller->model;
			$pk = $model->getPk();
			$list = $model->where($pk, 'in', $ids)->select();
			$list = to_array($list);
			$list = array_column($list, NULL, $pk);

			$ids_arr = explode(",", $ids);
			$op_title_arr = [];
			foreach ($ids_arr as $_id) {
				$id_row = $list[$_id];
				$op_title_arr[] = $this->getOptTitle($id_row);
			}
			if (!empty($op_title_arr)) {
				$data['op_title'] = implode("@##@", $op_title_arr);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "删除";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "删除列表下数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$data['title'] = "删除：“" . $data['op_menu_title'] . "”栏目下ID号是“" . $ids . "”的数据";
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}

		/**
		 * 新增数据默认的记录模板
		 * @param $args
		 */
		public function addTemplate($args)
		{
//            return true;
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象
			$id = $params['id'];
			$data['op_id'] = $id;
			if (isset($params['post'])) {
				$post_data = $params['post'];
			} else {
				$post_data = [];
			}
			$model = $controller->model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "新增";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "新增列表下数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功：新增的数据“ID号是" . $id . "”";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$title = "";
				$title .= "新增：“" . $data['op_menu_title'] . "”栏目下";
				if (!empty($post_data) && !empty($controller->flag_field_arr)) {
					$title .= "" . $controller->flag_field_arr['desc'] . "为" . "“" . $post_data[$controller->flag_field_arr['key']] . "”";
				}
				$title .= '的数据';
				$data['title'] = $title;
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}

		/**
		 * 修改数据默认的模板
		 * @param $args
		 */
		public function editTemplate($args)
		{
//            return true;
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象

			$id = $params['id'];
			$data['op_id'] = $id;

			if (isset($params['post'])) {
				$post_data = $params['post'];
			} else {
				$post_data = [];
			}
			$model = $controller->model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "修改";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "修改列表下数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$title = "";
				$title .= "修改：“" . $data['op_menu_title'] . "”栏目下ID号是“" . $id . "”";
				$title .= '的数据';
				$data['title'] = $title;
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}

		/**
		 * 改变文本值默认的记录模板
		 * @param $args
		 */
		public function setTextTemplate($args)
		{
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象

			$id = $params['id'];
			$data['op_id'] = $id;
			$field = $params['field'];
			$value = $params['value'];

			$model = $controller->model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			$text_arr = $this->getDefaultSetTextArr();
			if (isset($controller->field_desc) && !empty($controller->field_desc)) {
				$text_arr = array_merge($text_arr, $controller->field_desc);
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "修改";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "修改列表下数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$title = "";
				$title .= "修改：“" . $data['op_menu_title'] . "”栏目下ID号是“" . $id . "”";
				if (isset($text_arr[$field])) {
					$title .= "，" . $text_arr[$field] . "的数据为" . $value;
				} else {
					$title .= '的数据';
				}
				$data['title'] = $title;
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}

		/**
		 * 改变状态默认的记录模板
		 * @param $args
		 */
		public function setStatusTemplate($args)
		{
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象

			$id = $params['id'];
			$data['op_id'] = $id;
			$field = $params['field'];
			$value = $params['value'];

			$model = $controller->model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			$text_arr = $this->getDefaultSetTextArr();
			if (isset($controller->field_desc) && !empty($controller->field_desc)) {
				$text_arr = array_merge($text_arr, $controller->field_desc);
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "修改";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "修改列表下数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$title = "";
				$title .= "修改：“" . $data['op_menu_title'] . "”栏目下ID号“" . $id . "”";
				if (isset($text_arr[$field])) {
					if ($value == 1) {
						$value = "有效";
					} else {
						$value = "无效";
					}
					$title .= "，" . $text_arr[$field] . "的数据为" . $value;
				} else {
					$title .= '的数据';
				}
				$data['title'] = $title;
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}


		/**
		 * 删除分类默认的记录模板
		 * @param $args
		 */
		public function delClassTemplate($args)
		{
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象
			$class_id = $ids = $params['ids'];
			$data['op_id'] = $ids;
			$class_row = $controller->class_model->where('class_id', $class_id)->find(); //删除
			if (!empty($class_row)) {
				$data['op_title'] = $this->getOptTitle($class_row);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "删除";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "删除分类数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功：" . "子分类及分类下数据将被删除";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				if (!empty($data['op_title'])) {
					$data['title'] = "删除：“" . $data['op_menu_title'] . "”栏目下分类ID号“" . $ids . "”,分类名称“" . $data['op_title'] . "”的数据";
				} else {
					$data['title'] = "删除：“" . $data['op_menu_title'] . "”栏目下分类ID号是“" . $ids . "”的数据";
				}
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}


		/**
		 * 修改分类数据默认的模板
		 * @param $args
		 */
		public function editClassTemplate($args)
		{
//            return true;
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象

			$id = $params['id'];
			$data['op_id'] = $id;

			if (isset($params['post'])) {
				$post_data = $params['post'];
			} else {
				$post_data = [];
			}
			$model = $controller->class_model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "修改";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "修改分类";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				if (!empty($data['op_title'])) {
					$data['title'] = "修改：“" . $data['op_menu_title'] . "”栏目下分类ID号“" . $id . "”,分类名称“" . $data['op_title'] . "”的数据";
				} else {
					$data['title'] = "修改：“" . $data['op_menu_title'] . "”栏目下分类ID号是“" . $id . "”的数据";
				}
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}


		/**
		 * 新增分类数据默认的模板
		 * @param $args
		 */
		public function addClassTemplate($args)
		{
//            return true;
			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象

			$id = $params['id'];
			$data['op_id'] = $id;

			if (isset($params['post'])) {
				$post_data = $params['post'];
			} else {
				$post_data = [];
			}
			$model = $controller->class_model;
			if ($data['op_result']) {
				$id_row = $model->get($id);
				$id_row = to_array($id_row);
				$data['op_title'] = $this->getOptTitle($id_row);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "新增";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "新增分类数据";
			}
			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功：新增的分类ID号是“" . $id . "”";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				$title = "";
				$title .= "新增：“" . $data['op_menu_title'] . "”栏目下";
				if (!empty($post_data) && !empty($controller->flag_field_arr)) {
					$title .= $controller->flag_field_arr['desc'] . "为" . "“" . $post_data[$controller->flag_field_arr['key']] . "”";
				}
				$title .= '的分类数据';
				$data['title'] = $title;
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}


		/**
		 * 转移分类默认的模板
		 * @param $args
		 */
		public function moveClassTemplate($args)
		{

			$data = $args['data']; //数据
			$params = $args['param']; //参数
			$controller = $args['class']; //对象
			$source_id = $params['source_id'];
			$target_id = $params['target_id'];
			$type = $params['type'];
			$data['op_id'] = $source_id;
			$model = $controller->class_model;
			if (isset($params['source_row'])) {
				$data['op_title'] = $this->getOptTitle($params['source_row']);
			} else {
				$data['op_title'] = "";
			}
			if (!isset($data['op_type'])) {
				$data['op_type'] = "修改";
			}
			if (!isset($data['op_method'])) {
				$data['op_method'] = "转移分类";
			}

			if (!isset($data['op_result_desc'])) {
				if ($data['op_result']) {
					if (isset($params['success_tips'])) {
						if (strrpos($params['success_tips'], "成功") !== false) {
							$data['op_result_desc'] = $params['success_tips'];
						} else {
							$data['op_result_desc'] = "操作成功：" . $params['success_tips'];
						}
					} else {
						$data['op_result_desc'] = "操作成功";
					}
				} else {
					if (!empty($data['error_msg'])) {
						if (strrpos($data['error_msg'], "失败") !== false) {
							$data['op_result_desc'] = $data['error_msg'];
						} else {
							$data['op_result_desc'] = "操作失败：" . $data['error_msg'];
						}
					} else {
						$data['op_result_desc'] = "操作失败";
					}
				}
			}
			if (!isset($data['title']) || empty($data['title'])) {
				//操作 栏目 id 号
				if (isset($params['source_row'])) {
					$temp_title = $this->getOptTitle($params["source_row"]);
					$source_title = "分类ID号“" . $params['source_id'] . "”,分类名称“" . $temp_title . "”";
				} else {
					$source_title = "分类ID号“" . $params['source_id'] . "”";
				}
				if (isset($params['target_row'])) {
					$temp_title = $this->getOptTitle($params["target_row"]);
					$target_title = "分类ID号“" . $params['target_id'] . "”,分类名称“" . $temp_title . "”";
				} else {
					$target_title = "分类ID号“" . $params['target_id'] . "”";
				}
				if ($type == 'inner') {
					$data['title'] = "转移分类：" . "移动“" . $data['op_menu_title'] . "”栏目下" . $source_title . "成为" . $target_title . "的子级";
				} elseif ($type == 'prev') {
					$data['title'] = "转移分类：" . "移动“" . $data['op_menu_title'] . "”栏目下" . $source_title . "成为" . $target_title . "的同级上一个位置";
				} elseif ($type == 'next') {
					$data['title'] = "转移分类：" . "移动“" . $data['op_menu_title'] . "”栏目下" . $source_title . "成为" . $target_title . "的同级下一个位置";
				}
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$this->data($data);
			$this->save();
		}




	}