<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2019/12/10
     * Time: 15:21
     */

    namespace app\admin\model;

    use app\common\model\BaseModel;

    use think\Db;

    use think\Request;


    class Menu extends BaseModel
    {
        // 表名
        protected $name = 'menu';
        protected static $all_menu = array();

        public static function getMenu($is_only_manager = false)
        {
            $request_menu_id = Request::instance()->param('m');
            $request_menu_id = (int)$request_menu_id;
            if ($is_only_manager) {
                $ret = collection(self::where('status', '1')->order('parent_id asc,orders asc,id asc')->select())->toArray();
            } else {
                $ret = collection(self::where('status', '1')->where("is_only_manager", 0)->order('parent_id asc,orders asc,id asc')->select())->toArray();
            }
            $ret = array_column($ret, NULL, "id");
            $menu_arr = array();
            foreach ($ret as $menu_id => $row) {
                $method_arr = array(); //方法表
                $rule_arr = array(); //权限表
                $row['is_show'] = true; //默认需要显示
                $row['is_active'] = false; //默认不是选中
                if (empty($row['controller'])) {
                    $row['methods'] = $method_arr;
                    $row['rules'] = $rule_arr;
                } else {
                    if (!empty($row['show_method'])) {
                        $rule_arr[] = array("title" => "查看", "rule" => "SHOW");
                        $methods = explode(",", $row['show_method']);
                        foreach ($methods as $method) {
                            $method = strtolower($method);
                            $method_arr[$method] = array("method" => $method, "rule" => "SHOW");
                        }
                    }
                    if (!empty($row['add_method'])) {
                        $rule_arr[] = array("title" => "新增", "rule" => "ADD");
                        $methods = explode(",", $row['add_method']);
                        foreach ($methods as $method) {
                            $method = strtolower($method);
                            $method_arr[$method] = array("method" => $method, "rule" => "ADD");
                        }
                    }
                    if (!empty($row['edit_method'])) {
                        $rule_arr[] = array("title" => "新增", "rule" => "EDIT");
                        $methods = explode(",", $row['edit_method']);
                        foreach ($methods as $method) {
                            $method = strtolower($method);
                            $method_arr[$method] = array("method" => $method, "rule" => "EDIT");
                        }
                    }
                    if (!empty($row['delete_method'])) {
                        $rule_arr[] = array("title" => "删除", "rule" => "DELETE");
                        $methods = explode(",", $row['delete_method']);
                        foreach ($methods as $method) {
                            $method = strtolower($method);
                            $method_arr[$method] = array("method" => $method, "rule" => "DELETE");
                        }
                    }
                    $other_method_arr = Db::name('menu_method')->where("menu_id", $row['id'])->where("status", 1)->order("orders asc,id asc")->select() ?: [];
                    if (!empty($other_method_arr)) {
                        foreach ($other_method_arr as $other_method) {
                            if (empty($other_method['other_method_code']) || empty($other_method['other_method_title']) || empty($other_method['other_method'])) {
                                continue;
                            } else {
                                $rule = strtoupper($other_method['other_method_code']);
                                $rule_arr[] = array("title" => $other_method['other_method_title'], "rule" => $rule);
                                $methods = explode(",", $other_method['other_method']);
                                foreach ($methods as $method) {
                                    $method = strtolower($method);
                                    $method_arr[$method] = array("method" => $method, "rule" => $rule);
                                }
                            }
                        }
                    }
                    $row['methods'] = $method_arr;
                    $row['rules'] = $rule_arr;
                }
                if (isset($ret[$row['parent_id']])) {
                    if (!isset($menu_arr[$row['parent_id']])) {
                        $menu_arr[$row['parent_id']] = $ret[$row['parent_id']];
                        $menu_arr[$row['parent_id']]['child'] = array();
                        $menu_arr[$row['parent_id']]['child_ids'] = array();
                    }
                    $menu_arr[$row['parent_id']]['child'][$menu_id] = $row;
                    $menu_arr[$row['parent_id']]['child_ids'][] = $menu_id;
                    if ($request_menu_id == $row['id']) {
                        $menu_arr[$row['parent_id']]['is_active'] = true;
                        $menu_arr[$row['parent_id']]['child'][$menu_id]['is_active'] = true;
                    }
                } else {
                    if (!isset($menu_arr[$menu_id])) {
                        $menu_arr[$menu_id] = $row;
                        $menu_arr[$menu_id]['child'] = array();
                        $menu_arr[$menu_id]['child_ids'] = array();
                    }
                    if ($request_menu_id == $row['id']) {
                        $menu_arr[$menu_id]['is_active'] = true;
                    }
                }
            }
            return $menu_arr;
        }


        /**
         * 获取到顶级栏目
         */
        public static function getTopLevel()
        {
            $ret = collection(self::where('parent_id', '0')->order('parent_id asc,orders asc,id asc')->select())->toArray();
            return $ret;
        }


        public function privilegeMethods()
        {
            return $this->hasMany('MenuMethod', "menu_id")->where("status", "1")->order("id asc");
        }


    }