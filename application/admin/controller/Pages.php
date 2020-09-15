<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/6/5
	 * Time: 17:11
	 */

	namespace app\admin\controller;

	use app\common\controller\Backbase;
	use app\common\model\Pages as model_pages;
	use think\Exception;

	class Pages extends Backbase
	{
		protected $default_row = ["content" => "", "status" => 1];
		protected $row = [];


		public function _initialize()
		{
			parent::_initialize();
			$this->model = new model_pages();
			try {
				$row = $this->model->get(["menu_id" => MENU_ID]);
				if (empty($row)) {
					$row = $this->default_row;
				} else {
					$row = to_array($row);
				}
			} catch (Exception $e) {
				$row = $this->default_row;
			}
			$this->row = $row;
		}


		public function index()
		{
			$row = to_array($this->row);
			$this->assign("row", $row);
			return $this->xfetch();
		}


		public function edit($id)
		{
			$data = $this->request->post();
			try {
				unset($data['menu_id']);
				$exist_row = $this->model->get(["menu_id" => MENU_ID]);
				if (!empty($exist_row)) {
					$exist_row->save($data);
				} else {
					$data['menu_id'] = MENU_ID;
					$this->model->data($data);
					$this->model->save();
				}
				return $this->successJsonResponse("修改成功");
			} catch (Exception $e) {
				return $this->failJsonResponse("修改失败:" . $e->getMessage());
			}
		}

	}