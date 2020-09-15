<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/8/14
     * Time: 23:15
     */

    namespace app\admin\controller;
    use app\common\controller\Backbase;

    use app\common\model\Applets as model;
    use app\common\model\AppcateClass as model_cateclass;
    use app\common\model\Appcate as model_cate;
    use think\Exception;

    class Applets extends Backbase
    {
        protected $noNeedLogin = [];
        //不需要验证权限的方法
        protected $noNeedRight = ["editclass", "delClass"];

        protected $search_tips = "输入标题按回车搜索";

        protected $search_field_arr = ["id", "title"];


        // 默认选项 就是 用在opt 页面 新增那里
        protected $default_row = [
            "number_code"=>"",
            'class_id' => 0,
            'title' => "",
            "title_en"=>"",
            "subtitle"=>"",
            "image_ids" => 0,
            "tags"=>"",
            "scan_image_ids"=>"",
            "detail_image_ids"=>0,
            "product_detail_image_ids"=>"",
			"show_image_ids"=>0,
			"slide_image_ids"=>0,
            "online_view_url"=>"",
			"hot"=>0,
            "add_time" => "",
            "description"=>"",
            "content"=>"",
            "meal_content"=>"",
            "admin_content"=>"",
            "orders" => 0,
            "status" => 1,
            "class_name" => "无",
            "prices"=>""
        ]; //默认数据

        protected $cateclass = [];
        protected $cate = [];

        public function _initialize()
        {
            parent::_initialize();
            $this->model = new model();
            $this->cateclass = new model_cateclass();
            $this->cate = new model_cate();
        }


        protected function list_deal($rows)
        {
            $rows = parent::list_deal($rows);
            foreach ($rows as &$row) {
                if(!empty($row['tags'])){
                    $tags = explode(",",$row['tags']);
                    $tag_arr = [];
                    foreach ($tags as $tag_row){
                        $class_row = $this->cate->where("id",$tag_row)->find();
                        $tag_arr[] = $class_row['title'];
                    }
                    $row['tags'] = implode(" | ",$tag_arr);
                }else{
                    $row['tags'] = "";
                }
                $row['scan_image_ids'] = get_cover_image($row['scan_image_ids']);
            }
            return $rows;
        }

        protected function opt_assigned($row)
        {
            if(empty($row['tags'])){
                $row['tags'] = [];
            }else{
                $row['tags'] = explode(",",$row['tags']);
            }
            $class_arr = [];
            try{
                $class_arr = $this->cateclass->where("status","<>",2)->where("parent_id",0)->order(CLASS_WEB_SORT)->select();
                $class_arr = to_array($class_arr);
                if(!empty($class_arr)){
                    foreach($class_arr as &$class_row){
                        $class_row['child'] = [];
                        $child_arr = $this->cate->where("status","<>",2)->where("class_id",$class_row['class_id'])->order(CLASS_WEB_SORT)->select();
                        $child_arr = to_array($child_arr);
                        if(!empty($child_arr)){
                            $class_row['child'] = $child_arr;
                        }
                    }
                }
            }catch (Exception $e){
            }
            return ["row" => $row,"class_arr"=>$class_arr];
        }

        protected function opt_deal(&$data){
            if(isset($data['tags'])&&!empty($data['tags'])){
                $data['tags'] = implode(",",$data['tags']);
            }else{
                $data['tags'] = "";
            }
            $data = $this->image_deal($data, "scan_image_ids");
            $data = $this->image_deal($data, "detail_image_ids");
            $data = $this->image_deal($data, "show_image_ids");
            $data = $this->image_deal($data, "slide_image_ids");
            parent::opt_deal($data);
        }





    }