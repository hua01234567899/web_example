<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2019/12/5
     * Time: 12:53
     */

    namespace app\admin\model;

    use app\common\model\BaseModel;

    class Admin extends BaseModel
    {
        // 表名
        protected $name = 'user';

        public function roles()
        {
            return $this->hasOne('UserRole', 'user_id')->where("status", 1)->field("role_ids");
        }


    }