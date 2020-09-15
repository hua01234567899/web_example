<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/7/25
     * Time: 21:27
     */

    namespace app\admin\controller;
    use app\common\controller\Backbase;

    use app\common\model\Webs as model;
    use app\common\model\WebscateClass as model_cateclass;
    use app\common\model\Webscate  as model_cate;


    class Webs extends Backbase
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
            "subtitle"=>"",
            "image_ids" => 0,
			"tags"=>"",
            "detail_image_ids"=>0,
            "product_detail_image_ids"=>"",
            "online_view_url"=>"",
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
            $data = $this->image_deal($data, "detail_image_ids");
            $data = $this->image_deal($data, "product_detail_image_ids");
            parent::opt_deal($data);
        }

    }