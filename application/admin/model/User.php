<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/1/30
     * Time: 13:20
     */

    namespace app\admin\model;


    use app\common\model\BaseModel;

    class User extends BaseModel
    {
        protected $name = 'User';

        public function roles()
        {
            $roles = $this->hasOne('UserRole', 'user_id')->where("status", 1)->find();
            if (empty($roles)) {
                $roles = [];
            } else {
                $roles = $roles->toArray();
                $roles = explode(",", $roles['role_ids']);
            }
            return $roles;
        }
    }