<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 2020/3/31
     * Time: 14:47
     * 网站套餐选择配置文件
     */

    return [
        "meal"=>[
          ["meal_id"=>1,"title"=>"套餐一"],
          ["meal_id"=>2,"title"=>"套餐二"]
        ],
        "time"=>[
            ["time_id"=>1,"title"=>"一年无赠送"],
            ["time_id"=>2,"title"=>"二年（送三个月）"],
            ["time_id"=>3,"title"=>"三年（送六个月）"],
            ["time_id"=>4,"title"=>"五年（送一年）"]
        ],
        "space" => [
            ["space_id" => 1, "title" => "内地（阿里云空间，需备案）"],
            ["space_id" => 2, "title" => "香港（免备案）"]
        ],
        "source_code" => [
            ["code_id" => 0, "title" => "不需要"],
            ["code_id" => 1, "title" => "需要"]
        ],
        "support" => [
            ["support_id" => 0, "title" => "不删"],
            ["support_id" => 1, "title" => "删除"]
        ]
    ];