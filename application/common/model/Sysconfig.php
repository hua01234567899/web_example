<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/5/9
     * Time: 17:39
     */

    namespace app\common\model;

    class Sysconfig extends BaseModel
    {
        protected $name = 'system_config';

        public function config_value($config_name)
        {
            $row = self::get(["config_name" => $config_name]);
            $row = to_array($row);
            $vaule = (empty($row) ? "" : $row['config_value']);
            return $vaule;
        }

    }