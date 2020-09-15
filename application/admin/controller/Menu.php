<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/5/14
     * Time: 14:50
     */

    namespace app\admin\controller;

    use app\common\controller\Backbase;
    use app\admin\model\Menu as model_menu;
    use think\Exception;
    use think\exception\ClassNotFoundException;


    class Menu extends Backbase
    {
        //不需要验证登录的方法
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = [];

        protected $where_menu = false;
        protected $search_tips = "输入标题按回车搜索";
        protected $search_field_arr = ["id", "title"];

        protected $default_sort = "orders asc,add_time asc";  //默认的排序方式就是 权重 添加时间

        protected $default_row = [
            'parent_id' => 0,
            'title' => "",
            "controller" => '',
            'icon' => "&#xe6b4;",
            "orders" => 0,
            "status" => 1,
            "is_show_class" => 0,
            "class_deep" => 0,
            "is_only_manager" => 0,
            "thumb_width" => 0,
            "thumb_height" => 0,
            "is_many_photo" => 0,
            "is_add_water" => 0,
            "rich_tags"=>"",
            "rich_css_link_url"=>""
        ]; //默认数据

        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model_menu();
            $this->default_row['thumb_width'] = DEFAULT_THUMB_WIDTH;
            $this->default_row['thumb_height'] = DEFAULT_THUMB_HEIGHT;
        }


        /**
         * 获取datatable数据列表
         */
        protected function getList()
        {
            $page = input("param.page", 1);
            $limit = input("param.limit", $this->default_length);
            $search = input("param.search", "");
            $map = [];
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
            try {
                $total_count = $this->model->where($map)->count();
                $rows = $this->model->where($map)->order($this->default_sort)->select();
                $rows = to_array($rows);
            } catch (Exception $e) {
                $total_count = 0;
                $rows = [];
            }
            if (!empty($rows)) {
                $rows2 = [];
                $first_menu_list = $this->model->getTopLevel();
                foreach ($first_menu_list as $firse_level) {
                    foreach ($rows as $row) {
                        if ($row['id'] == $firse_level['id']) {
                            $rows2[] = $row;
                            break;
                        }
                    }
                    foreach ($rows as $row) {
                        if ($row['parent_id'] == $firse_level['id']) {
                            $rows2[] = $row;
                        }
                    }
                }
                $start = ($page - 1) * $limit;
                $rows2 = array_slice($rows2, $start, $limit);
            } else {
                $rows2 = [];
            }
            return $this->responseDatatable($rows2, $total_count);
        }


        public function opt_assigned($row)
        {
            $first_menu_list = $this->model->getTopLevel();
            $assigned_arr = ['row' => $row, 'first_menu_list' => $first_menu_list];
            return $assigned_arr;
        }

        protected function opt_check($data)
        {
            $rule = [
                ['title|标题', 'require|length:1,20'],
                ['controller|控制器', 'require|length:1,30|alpha'],
            ];
            $result = $this->validateData($data, $rule);
            if ($result !== true) {
                return $result;
            } else {
                return true;
            }
        }

        protected function opt_deal(&$data)
        {
            $pk = $this->model->getPk();
            if (isset($data[$pk])) {
                unset($data[$pk]); //如果有id 删除ID
            }
            if (isset($data['menu_id'])) {
                unset($data['menu_id']); //如果有menu_id 删除
            }
            $data['controller'] = strtolower($data['controller']);
            $data['icon'] = htmlspecialchars_decode($data['icon']);
            $data['add_time'] = date("Y-m-d H:i:s");
            return true;
        }

        protected function opt_after($id)
        {
            $row = $this->model->get($id);
            $row = to_array($row);
            $controller = $row['controller'];
            //如果没涉及分类
            if (!$row['is_show_class'] && !in_array($controller, ["menu"])) {
                try {
                    $controller_class = controller($controller);
                    $menu_id = $row['id'];
                    $model = $controller_class->model;
                    $time = date("Y-m-d H:i:s");
                    $model->where("menu_id", $menu_id)->where("status", 3)->update(['status' => 1, "update_time" => $time]);
                } catch (ClassNotFoundException $exception) {

                } catch (Exception $e) {

                }
            }
            return true;
        }


    }