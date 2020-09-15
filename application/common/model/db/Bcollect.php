<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2020/4/22
 * Time: 22:26
 */

namespace app\common\model\db;

use think\Db;
use app\common\model\BaseModel;

/**
 * Class Bcollect
 * @package app\common\model\db
 */
class Bcollect extends BaseModel
{
    public $product_filed = "product_id";

    public function setProductField($filed_id){
        $this->product_filed = $filed_id;
    }
    public function is_in($table, $product_id)
    {
        if (is_array($product_id)) {
            $count = Db::name($table)->where($product_id)->where('status', 1)->count();
        } else {
            $count = Db::name($table)->where($this->product_filed, $product_id)->where('status', 1)->count();
        }
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}