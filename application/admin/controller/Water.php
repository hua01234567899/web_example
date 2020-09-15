<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/6/4
     * Time: 17:32
     */

    namespace app\admin\controller;

    use app\common\controller\Backbase;
    use app\common\model\WaterSet as model_water;
    use app\common\model\Sysconfig as model_sysconfig;
    use app\admin\model\Menu as model_menu;
    use think\Exception;

    class Water extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = [];

        protected $row = [];

        protected $model_sysconfig = "";

        protected $model_menu = "";

        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_water();
            $this->model_sysconfig = new model_sysconfig();
            $this->model_menu = new model_menu();
        }

        public function index()
        {
            //先查询能用水印的栏目
            $menu_arr = $this->model_menu->where("is_add_water", 1)->where("status", "1")->order(CLASS_WEB_SORT)->select();
            if (!empty($menu_arr)) {
                $menu_arr = to_array($menu_arr);
                $menu_ids = array_column($menu_arr, "id");
                //更新存在不在上面栏目的menu
                $time = date("Y-m-d H:i:s");
                $this->model->where('menu_id', "not in", $menu_ids)->where("status", 1)->update(['status' => 0, "update_time" => $time]);
            } else {
                $menu_arr = [];
            }

            $check_arr = $this->model->where("status", 1)->select();
            if (!empty($check_arr)) {
                $check_arr = to_array($check_arr);
                $check_id_arr = array_column($check_arr, "menu_id");
            } else {
                $check_arr = [];
                $check_id_arr = [];
            }
            $row = [];

            //获取水印图片
            $is_water_row = $this->model_sysconfig->get(["config_name" => "is_water"]);
            if (empty($is_water_row)) {
                $this->model_sysconfig->data(["config_name" => "is_water", "config_value" => 0]);
                $this->model_sysconfig->save();
                $is_water = 0;
            } else {
                $is_water = $is_water_row->config_value;
            }


            //获取水印图片
            $id_row = $this->model_sysconfig->get(["config_name" => "water_id"]);
            if (empty($id_row)) {
                $this->model_sysconfig->data(["config_name" => "water_id", "config_value" => 0]);
                $this->model_sysconfig->save();
                $water_id = 0;
            } else {
                $water_id = $id_row->config_value;
            }

            $position_row = $this->model_sysconfig->get(["config_name" => "water_position"]);
            if (empty($position_row)) {
                //默认就是左上角
                $this->model_sysconfig->data(["config_name" => "water_position", "config_value" => 1]);
                $this->model_sysconfig->save();
                $position = 1;
            } else {
                $position = $position_row->config_value;
            }
            $row['is_water'] = $is_water;
            $row['water_id'] = $water_id;
            $row['water_position'] = $position;

            $position_arr = config("water_position");

            //水印位置
            $this->assign(["row" => $row, "position_arr" => $position_arr, "check_id_arr" => $check_id_arr, "menu_arr" => $menu_arr]);

            return $this->xfetch();
        }

        public function edit($id)
        {

            $post_data = $this->request->post();
            $post_data = $this->image_deal($post_data, "water_id");
//            file_put_contents(__DIR__ . "/RR.txt", var_export($post_data, true));
//			return $this->successJsonResponse("修改成功");
            $arr1 = [];

            $arr1['is_water'] = $post_data['is_water'];
            $arr1['water_id'] = isset($post_data['water_id']) ? $post_data['water_id'] : 0;
            $arr1['water_position'] = $post_data['water_position'];
            if (isset($post_data['menu'])) {
                $select_menu = $post_data['menu'];
            } else {
                $select_menu = [];
            }
//            file_put_contents(__DIR__ . "/zz.txt", var_export($arr1, true));
            $time = date("Y-m-d H:i:s");
//            file_put_contents(__DIR__ . "/yy.txt", var_export($select_menu, true));
            try {
                foreach ($arr1 as $key => $item) {
                    $this->model_sysconfig->where('config_name', $key)->update(['status' => 1, "update_time" => $time, "config_value" => $item]);
                }
            } catch (Exception $exception) {
                return $this->failJsonResponse("修改失败" . $exception->getMessage());
            }

            //修改让所有的先变为0
            $this->model->where("status", 1)->update(['status' => 0, "update_time" => $time]);

            foreach ($select_menu as $menu_id) {
                $row = $this->model->get(["menu_id" => $menu_id]);
                if (empty($row)) {
                    $this->model->data(['menu_id' => $menu_id, "add_time" => $time]);
                    $this->model->save();
                } else {
                    $row->status = 1;
                    $row->save();
                }
            }

            return $this->successJsonResponse("修改成功");
//			file_put_contents(__DIR__ . '/aa.txt', var_export($select_menu, true));


            return $this->successJsonResponse("修改成功");

//			$this->image_deal();
//			return $this->successJsonResponse("修改成功");
//			$c_upload = new Upload();
//			$logo_ids = $c_upload->insert_data("logo_id");
//			$rule = [
//				['title|网站名称', 'require|length:1,40'],
//				['seo_title|首页标题', 'length:0,30'],
//				['keywords|META关键词', 'length:0,200'],
//			];
//			$data = [
//				'title' => input('post.title/s'),
//				'logo_id' => $logo_ids,
//				'seo_title' => input('post.seo_title/s'),
//				'keywords' => input('post.keywords/s'),
//				'description' => input('post.description/s'),
//				'beian' => input('post.beian/s'),
//				'ga_beian' => input('post.ga_beian/s'),
//			];
//			$error_msg = $this->validateData($data, $rule);
//			if ($error_msg !== true) {
//				return $this->failJsonResponse($error_msg);
//			} else {
//				try {
//					$update_result = $this->row->save($data);
//					if ($update_result !== false) {
//						return $this->successJsonResponse("修改成功");
//					} else {
//						return $this->failJsonResponse("修改失败");
//					}
//				} catch (Exception $exception) {
//					return $this->failJsonResponse("修改失败");
//				}
//			}
        }


    }