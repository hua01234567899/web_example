<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2020/4/21
 * Time: 23:37
 */

namespace app\common\model\db;
use think\Db;
use app\common\model\BaseModel;

class Btype extends BaseModel
{
    public $sort_order = "orders asc,add_time asc";

    public function getTypeArr($table)
    {
        $type_arr = Db::name($table)->field("type_id,title")->where('status', 1)->order($this->sort_order)->select();
        return $type_arr;
    }


}