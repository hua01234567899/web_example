<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/4/21
     * Time: 23:28
     */

    namespace app\common\model;

    use app\common\model\BaseModel;

    use think\Db;
    use think\Exception;

    class ToolModel extends BaseModel
    {
        protected $name = "";

        public function getRowArr($table, $conArr = ["status" => 1])
        {
            $row = Db::name($table)->where($conArr)->find();
            $row = to_array($row);
            return $row;
        }

        public function getListArr($table, $conArr = ["status" => 1], $orderby, $start, $length)
        {
            $data = ["total" => 0, "list" => []];
            try {
                $data['total'] = Db::name($table)->where($conArr)->count();
                $data['list'] = Db::name($table)->where($conArr)->order($orderby)->limit($start, $length)->select();
                return $data;
            } catch (Exception $e) {
                return $data;
            }
        }

    }