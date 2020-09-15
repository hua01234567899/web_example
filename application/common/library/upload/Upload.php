<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/5/14
	 * Time: 14:40
	 */

	namespace app\common\library\upload;

	use app\common\model\Images;
	use app\common\model\Sysconfig;
	use think\Exception;
	use think\Image;
	use app\common\model\UploadTmp;
	use app\common\model\Upload as UploadBaseModel;
	use app\admin\model\Menu as ModelMenu;
	use app\common\model\WaterSet;
	use app\common\model\AdminConfig as model_adminconfig;

	class Upload
	{
		public $image_model = "";

		public $upload_url = "";

		public $fild_type_arr = []; //文件类型配置  读取 system_config 的  file_type字段


		public function __construct()
		{
			$this->image_model = new Images();
			$this->upload_url = url("upload/index");
			$model_sys = new model_adminconfig();
			$file_type_arr = $model_sys->config_value("file_type");
			$file_type_arr = json_decode($file_type_arr, true);
			$this->fild_type_arr = $file_type_arr;
			$this->cleantmpFile(); //清除临时文件
		}


		protected function MkDirs($dir)
		{
			return is_dir($dir) or ($this->MkDirs(dirname($dir)) and mkdir($dir, 0777));
		}

		protected function getFileName($filename = "")
		{
			$delimiter = "/";
			if (strrpos($filename, "\\") !== false) {
				$delimiter = "\\";
			}
			$filename = explode($delimiter, $filename);
			$filename = end($filename);
			$filename = str_replace(strrchr($filename, '.'), '', $filename);
			return $filename;
		}


		/**
		 * 清除临时文件
		 */
		protected function cleantmpFile()
		{
			$time = time();
			$dir = TMP_PATH;
			if (!is_dir($dir)) return false;
			$handle = @opendir($dir);
			if ($handle) {
				while (($fl = readdir($handle)) !== false) {
					$temp = $dir . $fl;
					if ($fl != '.' && $fl != '..' && is_file($temp)) {
						$filename = $this->getFileName($fl);
						if (strrpos($filename, "tmp") !== false) {
							$expire_time = explode("@", $filename);
							if (isset($expire_time[1])) {
								$expire_time = (int)$expire_time[1];
							} else {
								$expire_time = 0;
							}
							if ($time > $expire_time) {
								@unlink($temp);
							}
						}
					}
				}
				closedir($handle);
			}
		}


		protected function getExt($filename)
		{
			$ext = explode(".", $filename);
			$ext = end($ext); //文件后缀
			return $ext;
		}

		protected function getImageInfo($path)
		{
			$image = Image::open($path);
			$arr = [];
			$arr['width'] = $image->width();
			$arr['height'] = $image->height();
			$arr['size'] = filesize($path);
			return $arr;
		}

		protected function thumb($image_path, $thumb_path, $width, $height)
		{
			$image = Image::open($image_path);
			$image->thumb($width, $height)->save($thumb_path);
		}

		/**
		 * 获取临时上传的目录
		 */
		protected function getTmpUrl()
		{
			$user_url = str_replace(PUBLIC_PATH, "", TMP_PATH);
			$user_url = str_replace('\\', "/", $user_url);
			$user_url = trim($user_url, "/");
			$user_url = "/" . $user_url . "/";  //上传到的目录
			return $user_url;
		}

		/**
		 * 获取用户的数据目录
		 */
		public function getUserUrl()
		{

			$user_url = str_replace(PUBLIC_PATH, "", USER_DATA_PATH);
			$user_url = str_replace('\\', "/", $user_url);
			$user_url = trim($user_url, "/");
			$user_url = "/" . $user_url . "/";  //上传到的目录
			return $user_url;
		}

		/**
		 * @param $params 上传参数
		 * @param $back_arr 上传成功返回的信息
		 * @return bool|string 正确返回 trur 错误 返回错误信息
		 */
		public function filecurl($params, &$back_arr)
		{
			//参数示例列表
			$params['m'] = isset($params['m']) ? $params['m'] : 0; //menu_id
			$params['c'] = isset($params['c']) ? $params['c'] : "index"; //控制器
			$params['type'] = isset($params['type']) ? $params['type'] : "image"; //上传的类型
			if (isset($params['file_type'])) {
				$file_type_validate = $params['file_type'];
			} else {
				$file_type_validate = $this->fild_type_arr[$params['type']];
			}
			$user_url = $this->getUserUrl();
			$day_date = date("Ymd");
			$h_date = date("Hi");

			//应该要上传到的目录
			$upload_file_url = $user_url . $params['m'] . "/" . $day_date . "/" . $h_date . "/";
			$upload_path = USER_DATA_PATH . $params['m'] . DS . $day_date . DS . $h_date . DS;

			$time = time() + 3600; //临时文件有效时间 加上一天

			$data = [];

			$uniqid = uniqid();

			//重新命名文件
			$tmp_file_name = "tmp@" . $time . "@" . $uniqid;
			$thumb_tmp_file_name = "tmp@" . $time . "@" . $uniqid . "_s";

			$file = request()->file("file");

			$file_info = $file->getInfo();

			$upload_file_name = $file_info['name'];


			$ext = $this->getExt($upload_file_name);

			$tmp_file_name = $tmp_file_name . "." . $ext; //临时文件名

			$thumb_tmp_file_name = $thumb_tmp_file_name . "." . $ext;

			$upload_file_name = $this->getFileName($upload_file_name);//用户上传的文件名

			$file_size = $file_info['size']; //文件大小


			$info = $file->validate(['size' => $file_type_validate['size'], 'ext' => $file_type_validate['ext']])->move(TMP_PATH, $tmp_file_name);
			$real_path = TMP_PATH . $tmp_file_name;

			$thumb_real_path = TMP_PATH . $thumb_tmp_file_name;

			$tmp_url = $this->getTmpUrl();
			if ($info) {
				$data['upload_file_url'] = $upload_file_url;
				$data['mime'] = $file_info['type'];
				$data['image_title'] = $upload_file_name;
				$data['image_ext'] = $ext;
				$data['image_hash'] = hash_file("md5", $real_path);
				$data['original_path'] = $tmp_url . $tmp_file_name;
				$data['image_path'] = $tmp_url . $tmp_file_name;
				$data['image_size'] = $file_size;
				$data['image_width'] = 0;
				$data['image_height'] = 0;
				$data['thumb_path'] = $tmp_url . $tmp_file_name;
				$data['thumb_size'] = $file_size;
				$data['thumb_width'] = 0;
				$data['thumb_height'] = 0;
				$data['is_add_water'] = 0; //视频也可以打水印 视频水印功能  待开发
				$data['play_time'] = "00:00:00";
				$data['play_total_time'] = 0;
				//如果是图片的话 对图片进行处理
				if ($params['type'] == 'image') {
					$img_info = getimagesize($real_path);
					$data['image_width'] = $img_info[0];
					$data['image_height'] = $img_info[1];
					//生成小图
					if (isset($params['thumb_width']) && isset($params['thumb_height'])) {
						$this->thumb($real_path, $thumb_real_path, $params['thumb_width'], $params['thumb_height']);
						if (file_exists($thumb_real_path)) {
							$data['thumb_path'] = $tmp_url . $thumb_tmp_file_name;
							$thumb_info = $this->getImageInfo($thumb_real_path);
							$data['thumb_width'] = $thumb_info['width'];
							$data['thumb_height'] = $thumb_info['height'];
							$data['thumb_size'] = $thumb_info['size'];
						}
					} else {
						//按照menu_id 查找的的宽度尺度来进行计算
						$thumb_width = DEFAULT_THUMB_WIDTH;
						$thumb_height = DEFAULT_THUMB_HEIGHT;
						$menu_row = ModelMenu::get((int)$params['m']);

						if (!empty($menu_row)) {
							$menu_row = to_array($menu_row);
							$tmp_width = (int)$menu_row['thumb_width'];
							$tmp_height = (int)$menu_row['thumb_height'];
							if (!empty($tmp_width) || !empty($tmp_height)) {
								$thumb_width = $tmp_width;
								$thumb_height = $tmp_height;
							} else {
								$thumb_width = 0;
								$thumb_height = 0;
							}
						}

						if (!empty($thumb_width) || !empty($thumb_height)) {
							$min_thumb_height = ($thumb_width / $data['image_width']) * $data['image_height'];
							$min_thumb_width = ($thumb_height / $data['image_height']) * $data['image_width'];

							if ($min_thumb_height < DEFAULT_THUMB_HEIGHT && $min_thumb_width < DEFAULT_THUMB_WIDTH) {
								//不进行压缩处理
							} else {
								if ($min_thumb_height > DEFAULT_THUMB_HEIGHT&&$min_thumb_height<$data['image_height']) {
									$this->thumb($real_path, $thumb_real_path, $thumb_width, $min_thumb_height);
									if (file_exists($thumb_real_path)) {
										$data['thumb_path'] = $tmp_url . $thumb_tmp_file_name;
										$thumb_info = $this->getImageInfo($thumb_real_path);
										$data['thumb_width'] = $thumb_info['width'];
										$data['thumb_height'] = $thumb_info['height'];
										$data['thumb_size'] = $thumb_info['size'];
									}
								} elseif ($min_thumb_width > DEFAULT_THUMB_WIDTH&&$min_thumb_width<$data['image_width']) {
									$this->thumb($real_path, $thumb_real_path, $min_thumb_width, $thumb_height);
									if (file_exists($thumb_real_path)) {
										$data['thumb_path'] = $tmp_url . $thumb_tmp_file_name;
										$thumb_info = $this->getImageInfo($thumb_real_path);
										$data['thumb_width'] = $thumb_info['width'];
										$data['thumb_height'] = $thumb_info['height'];
										$data['thumb_size'] = $thumb_info['size'];
									}
								}
							}
							//按照系统默认的缩放尺寸进行处理
							//自动缩小图片尺寸处理  宽度600 的尺寸
						}
					}
					//水印处理开始
					try {
						$system_config = new  Sysconfig();
						$is_add = $system_config->get(["config_name" => "is_water"]);
						$water_id = $system_config->get(["config_name" => "water_id"]);
						$position = $system_config->get(["config_name" => "water_position"]);

						if (!empty($is_add) && !empty($water_id) && !empty($position)) {
							$is_add = to_array($is_add);
							$is_add = $is_add['config_value'];

							$water_id = to_array($water_id);
							$water_id = $water_id['config_value'];

							$position = to_array($position);
							$position = $position['config_value'];

							if (!empty($is_add) && !empty($water_id) && !empty($position)) {
								//判断是否在哪个menum 里面
								$waterSet = new WaterSet();
								$menu_id = $params['m'];
								$is_exist = $waterSet->where(["menu_id" => $menu_id, "status" => 1])->select();
								if (!empty($is_exist)) {

//                                    $water_img = get_cover_image($water_id);

									$water_info = $this->image_model->get($water_id);

									$water_info = to_array($water_info);
									$water_image_path = PUBLIC_PATH . $water_info['image_path'];
									$water_width = $water_info['image_width'];
									$water_height = $water_info['image_height'];
									$water_ext = $water_info['image_ext'];

									$image_info = $this->getImageInfo($real_path);


									$image_width = $image_info['width'];

									$percent_width = (int)($image_width * 0.2); //图片的20%

									$real_water_width = min($water_width, $percent_width, 200);

									if ((int)$water_width != (int)$real_water_width) {
										$tem_water = TMP_PATH . uniqid() . time() . "." . $water_ext;
										$this->thumb($water_image_path, $tem_water, $real_water_width, $water_height * ($real_water_width / $water_width));
										$image = Image::open($real_path);
										$image->water($tem_water, $position)->save($real_path);
										//删除临时水印文件
										@unlink($tem_water);
									} else {
										$tem_water = $water_image_path;
										$image = Image::open($real_path);
										$image->water($tem_water, $position)->save($real_path);
									}

									if (!empty($data['thumb_path']) && $data['thumb_path'] != $data['image_path'] && isset($thumb_real_path) && is_file($thumb_real_path)) {
										$thumb_image_info = $this->getImageInfo($thumb_real_path);
										$thumb_image_width = $thumb_image_info['width'];
										$thumb_percent_width = (int)($thumb_image_width * 0.2);
										$thumb_real_water_width = min($water_width, $thumb_percent_width, 200);
										if ((int)$water_width != (int)$thumb_real_water_width) {
											$tem_water = TMP_PATH . uniqid() . time() . "." . $water_ext;
											$this->thumb($water_image_path, $tem_water, $thumb_real_water_width, $water_height * ($thumb_real_water_width / $water_width));
											$image = Image::open($thumb_real_path);
											$image->water($tem_water, $position)->save($thumb_real_path);
											@unlink($tem_water);
										} else {
											$tem_water = $water_image_path;
											$image2 = Image::open($thumb_real_path);
											$image2->water($tem_water, $position)->save($thumb_real_path);
										}
									}
//
								}
							}
						}
					} catch (\Exception $e) {

					}
					//水印处理结束
				}

				//插入数据库
				$model_upload_tmp = new UploadTmp();
				$model_upload_tmp->data($data);
				$insert_result = $model_upload_tmp->save();
				if ($insert_result === false) {
					return "上传失败";
				}
				$insert_id = $model_upload_tmp->getLastInsID();
				$back_arr['id'] = $insert_id;
				$back_arr['path'] = $data['image_path'];
				$back_arr['name'] = $data['image_title'];
				$back_arr['size'] = $data['image_size'];
				$back_arr['ext'] = $data['image_ext'];
				$back_arr['show_size'] = show_file_size($data['image_size']);
				return true;
			} else {
				// 上传失败获取错误信息
				$error_msg = $file->getError();
				return $error_msg;
			}
		}


		/**
		 * @param string $field
		 * @param array $data_row 数据 //如果需要移除时候使用
		 */
		public function insert_data($field = "", $last_row = [])
		{
			//将多余的文件移出来
			$data = input("param." . $field . "/a");
			$title_field = $field . "_title";
			$title_data = input("param." . $title_field . "/a");
			$id_arr = [];
			if (!empty($data)) {
				foreach ($data as $file_key => $file_row) {
					//临时文件
					if (strrpos($file_row, "tmp") !== false) {
						$file_row = str_replace(["tmp", "_", "@", ",", "/", "+", "$"], "", $file_row); //将常见的分隔符去掉
						if (is_numeric($file_row)) {
							$file_row = (int)$file_row;
							//将文件移出来
							$model_upload_tmp = new UploadTmp();
							$tmp_file_row = $model_upload_tmp->get(["image_id" => $file_row]);
							if (!empty($tmp_file_row)) {
								$tmp_file_row = to_array($tmp_file_row);
								$new_data = $tmp_file_row;
								unset($new_data['image_id'], $new_data['move_id'], $new_data['upload_file_url'], $new_data['create_time'], $new_data['update_time']);
								if (!empty($title_data)) {
									$new_data['image_title'] = $title_data[$file_key];
								}
								$upload_dir = $tmp_file_row['upload_file_url'];
								$this->MkDirs(PUBLIC_PATH . $upload_dir);
								$image_path = $tmp_file_row['image_path']; //大图位置
								$ext = $this->getExt($image_path);
								$uniqid = uniqid();
								$new_file_name = $uniqid . "." . $ext;
								$new_ori_name = $uniqid . "_o" . "." . $ext;
								$new_thumb_name = $uniqid . "_s" . "." . $ext;

								if (file_exists(PUBLIC_PATH . $image_path)) {
									//移动
									@rename(PUBLIC_PATH . $image_path, PUBLIC_PATH . $upload_dir . $new_file_name);
									$new_data['image_path'] = $upload_dir . $new_file_name;
									if ($image_path != $tmp_file_row['original_path']) {
										@rename(PUBLIC_PATH . $tmp_file_row['original_path'], PUBLIC_PATH . $upload_dir . $new_ori_name);
										$new_data['original_path'] = $upload_dir . $new_ori_name;
									} else {
										$new_data['original_path'] = $new_data['image_path'];
									}
									if ($image_path != $tmp_file_row['thumb_path']) {
										@rename(PUBLIC_PATH . $tmp_file_row['thumb_path'], PUBLIC_PATH . $upload_dir . $new_thumb_name);
										$new_data['thumb_path'] = $upload_dir . $new_thumb_name;
									} else {
										$new_data['thumb_path'] = $new_data['image_path'];
									}
									try {
										$uploadbasemodel = new UploadBaseModel();
										$uploadbasemodel->data($new_data);
										$insert_result = $uploadbasemodel->save();
										if ($insert_result !== false) {
											$insert_id = $uploadbasemodel->getLastInsID();
											$id_arr[] = $insert_id;
											$model_upload_tmp->save(["move_id" => $insert_id], ["image_id" => $file_row]);
										}
									} catch (Exception $e) {
									}
								}
							}
						}
					} elseif (is_numeric($file_row)) {
						try {
							if (!empty($title_data)) {
								$uploadbasemodel = new UploadBaseModel();
								$file = $uploadbasemodel->get($file_row);
								$file->image_title = $title_data[$file_key];
								$file->save();
							}
						} catch (Exception $e) {

						}
						$file_row = (int)$file_row;
						$id_arr[] = $file_row;
					} else {
						continue;
					}
				}
			}
			if (empty($id_arr)) {
				return "";
			} else {
				return implode(",", $id_arr);
			}
		}

		public function deal_upload_url($post_url, $get_param_arr)
		{
			$get_param_arr = http_build_query($get_param_arr);
			$get_param_arr = think_encrypt($get_param_arr); //对参数进行加密
			$post_url .= "?params=" . $get_param_arr;
			return $post_url;
		}

		/**
		 * @param array $param
		 * @throws \think\exception\DbException
		 * param 参数个数 包括 m  c  num 上传数量 num  field 数据字段 用在 hidden 里面
		 */
		public function image_style($param = [])
		{
			$post_url = isset($param_arr['url']) && !empty($param_arr['url']) ? $param_arr['url'] : $this->upload_url;
			$get_param_arr = [];
			$get_param_arr['m'] = MENU_ID;
			$get_param_arr['c'] = CONTROLLER;
			$get_param_arr['type'] = "image";//上传类型 图片
			$is_input = isset($param['is_input']) ? 1 : 0; //是否还需要保留输入框
			$upload_nums = isset($param['num']) && !empty($param['num']) ? $param['num'] : 1; //上传文件数
			$title = isset($param['title']) && !empty($param['title']) ? $param['title'] : "封面图";
			$tips = ""; //提示信息
			$images_id_arr = isset($param['images']) && !empty($param['images']) ? (string)$param['images'] : [];

			if (is_string($images_id_arr)) {
				$images_id_arr = explode(",", $images_id_arr);
			}//原本已经成功上传的数组
			$fields = isset($param['field']) && !empty($param['field']) ? $param['field'] : "image_ids"; //需要上传到的字段
			$upload_container = $fields . "_upload_contain";//上传
			$upload_button = $fields . "_upload_button"; //上传按钮
			$upload_append_ul = $fields . "_upoad_append_ul"; //需要添加到的ul里面
			$post_url = $this->deal_upload_url($post_url, $get_param_arr);//需要上传到的url 里面

			$html = '<div class="layui-form-item image_upload_container"  id="' . ($upload_container) . '"><label class="layui-form-label">' . $title . '</label>';
			$html .= '<div class="layui-input-block">';
			$html .= '<div style="display:inline-block; vertical-align:top; width:100%;">
			<div class="layui-upload" style="float:left; margin-right:10px;">
				<button type="button" class="layui-btn" id="' . $upload_button . '"><i class="layui-icon"></i>点击上传
				</button>
			</div>
			<div style="float:left;">
				<button type="button" onclick="clear_image(this);" data-num="' . $upload_nums . '" class="layui-btn layui-btn-danger"><i class="layui-icon"></i>' . ($upload_nums > 1 ? "批量删除" : "单张删除") . '</button>
			</div>
		</div>';
			$html .= '<blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;"> 预览：<span class="upload_success_tip">已上传<span class="upload_success_nums">' . count($images_id_arr) . '</span>张图片,多图上传时可以拉动图片进行图片排序</span>';
			$html .= '<ul data-isinput="' . $is_input . '"  data-num="' . $upload_nums . '" class="image_ids_upoad_append_ul layui-upload-list layui-row flow-default layui-col-space20"  id="' . $upload_append_ul . '">';
			$li_html = "";

			foreach ($images_id_arr as $image_id) {
				$image_obj = $this->image_model->get(["image_id" => $image_id]);
				$src = $image_obj->image_path; //获取图片
				$image_title = $image_obj->image_title;
				$li_html .= ' <li class="layui-col-md3" style="transform: translate3d(0px, 0px, 0px);" draggable="false">
                    <div class="box">
                        <a href="' . $src . '" data-url="' . $src . '"
                           target="_blank" class="thumbnail  layui-upload-img" draggable="false"><img
                                src="' . $src . '"
                                onerror="this.src=\'/admin/ajax/icon?suffix=png\';this.onerror=null;" 
                                class="img-responsive" draggable="false"></a>';
				if ($upload_nums > 1) {
					$li_html .= '<input type="checkbox" name="img_del_check" class="img_check" lay-skin="primary" title="">';
				}
				$li_html .= '</div>';
				if ($is_input) {
					$li_html .= '<input type="text" value="' . $image_title . '" name="' . $fields . '_title' . '[]"  autocomplete="off" placeholder="请输入标题"
                           class="layui-input">';
				}
				$li_html .= '<input type="hidden" name="' . $fields . '[]" value="' . $image_id . '">';
				$li_html .= '</li>';
			}
			$html .= $li_html;
			$html .= '</ul>';
			$html .= '</blockquote>';
			$html .= '</div></div>';
			$script = '<script>
                if(typeof is_uploading=="undefined"){
                    var is_uploading = false;
                }
                layui.use(["upload","form"], function () {
                	var $ = layui.jquery
                    , upload = layui.upload;
                    var form = layui.form;
                    upload.render({
                    elem: "#' . $upload_button . '"
                    , url: "' . $post_url . '"
                    , multiple: ' . ($upload_nums > 1 ? 'true' : 'false') . '
                    , accept: "images"
                    ,size: 1024*2
                    , acceptMime: "image/*"
                    , before: function (obj) {
                    if(is_uploading==false){
                        is_uploading = true;
                        layer.load(); //上传loading
                    }
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                         });
                     }
                    ,allDone:function(obj){
                    var success_nums = obj.successful;
                    var error_nums = obj.aborted;
                     console.log(obj.total); //得到总文件数
                     console.log(obj.successful); //请求成功的文件数
                     console.log(obj.aborted); //请求失败的文件数
                       is_uploading = false;
                       if(error_nums>0){
                           layer.msg(error_nums+"个文件上传失败,请核对",{"time": 1500, icon: 2});
                      }
                     var has_upload = $("#' . $upload_append_ul . ' li").length;
                     $("#' . ($upload_container) . ' .upload_success_tip .upload_success_nums").html(has_upload);
                     form.render();
                     layer.closeAll("loading"); //关闭loading
                }
            , done: function (res,index,upload) {
                if(res.code==0){
                    var response_data = res.data;
                    var li_html = \'<li class="layui-col-md3" style="transform: translate3d(0px, 0px, 0px);" draggable="false">\';
                    li_html+=\'<div class="box">\';
                    li_html+= \'<a href="\'+response_data.path+\'"  data-url="\'+response_data.path+\'"  target="_blank" class="thumbnail  layui-upload-img" draggable="false">\';
                    li_html+=\'<img src="\'+response_data.path+\'"  onerror="this.src=\\\'/admin/ajax/icon?suffix=png\\\';this.onerror=null;" class="img-responsive"  draggable="false">\';
                    li_html+=\'</a>\';
                    var img_upload_num = $("#' . $upload_append_ul . '").data("num");
                    console.log(img_upload_num);
                    if(img_upload_num>1){
                   		li_html+=\'<input type="checkbox" name="img_del_check" class="img_check" lay-skin="primary" title="">\';     
                    }
                    li_html+=\'</div>\';
                    var is_input = $("#' . $upload_append_ul . '").data("isinput");
                    if(is_input==1){
                        li_html+= \'<input type="text"  class="layui-input" autocomplete="off" placeholder="请输入标题" name="' . ($fields) . '_title[]" value="\'+response_data.name+\'">\';
                    }       
                    li_html+= \'<input type="hidden" name="' . ($fields) . '[]" value="tmp_\'+response_data.id+\'">\';
                    li_html+= "</li>";
                    
                     if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                        $("#' . $upload_append_ul . '").html("");
                        $("#' . $upload_append_ul . '").append(li_html);
                     }else{
                     	$("#' . $upload_append_ul . '").append(li_html);
                     }
                }
                if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                    layer.closeAll("loading"); //关闭loading
                    if(res.code==0){
                        is_uploading = false; 
                         var has_upload = $("#' . $upload_append_ul . ' li").length;
                     	$("#' . ($upload_container) . ' .upload_success_tip .upload_success_nums").html(has_upload);
                    }else {
                        layer.msg("文件上传失败,请核对",{"time": 1500, icon: 2});
                    }
                }
                form.render();
            }, error: function () {
                if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                     layer.msg("文件上传失败,请核对",{"time": 1500, icon: 2});
                     layer.closeAll("loading"); //关闭loading
                }
                form.render();
            }
        });
    })</script>';
			$upload_html = $html . $script;
			$upload_html .= build_sort($upload_append_ul);
			echo $upload_html;
		}


		/**
		 * 文件上传的格式
		 * @param array $param
		 * @throws \think\exception\DbException
		 */
		public function file_style($param = [])
		{
			$post_url = isset($param_arr['url']) && !empty($param_arr['url']) ? $param_arr['url'] : $this->upload_url;
			$get_param_arr = [];
			$get_param_arr['m'] = MENU_ID;
			$get_param_arr['c'] = CONTROLLER;

			$get_param_arr['type'] = "file";//上传类型 图片

			$current_type_arr = $this->fild_type_arr[$get_param_arr['type']];
			$is_input = isset($param['is_input']) ? 1 : 0; //是否还需要保留输入框

			$upload_nums = isset($param['num']) && !empty($param['num']) ? $param['num'] : 1; //上传文件数
			$title = isset($param['title']) && !empty($param['title']) ? $param['title'] : "文件";

			$tips = ""; //提示信息
			$images_id_arr = isset($param['images']) && !empty($param['images']) ? (string)$param['images'] : [];
			if (is_string($images_id_arr)) {
				$images_id_arr = explode(",", $images_id_arr);
			}//原本已经成功上传的数组


			$fields = isset($param['field']) && !empty($param['field']) ? $param['field'] : "image_ids"; //需要上传到的字段

			$upload_container = $fields . "_upload_contain";//上传

			$upload_button = $fields . "_upload_button"; //上传按钮
			$upload_append_ul = $fields . "_upoad_append_ul"; //需要添加到的ul里面

			$post_url = $this->deal_upload_url($post_url, $get_param_arr);//需要上传到的url 里面

			$html = '<div class="layui-form-item image_upload_container"  id="' . ($upload_container) . '"><label class="layui-form-label">' . $title . '</label>';
			$html .= '<div class="layui-input-block">';
			$html .= '<div style="display:inline-block; vertical-align:top; width:100%;">
			<div class="layui-upload" style="float:left; margin-right:10px;">
				<button type="button" class="layui-btn layui-btn-normal" id="' . $upload_button . '"><i class="layui-icon"></i>点击上传
				</button>
			</div>
			<div style="float:left;">
				<button type="button" onclick="clear_file(this);" data-num="' . $upload_nums . '" class="layui-btn layui-btn-danger"><i class="layui-icon"></i>' . ($upload_nums > 1 ? "批量删除" : "删除文件") . '</button>
			</div>
		</div>';

			$html .= '<div class="layui-upload-list">
                        <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>文件名</th>
                                    <th>大小</th>
                                    <th>类型</th>
                                    <th>状态</th>
                                </tr>
                            </thead>
                            <tbody class="image_ids_upoad_append_ul" data-isinput="' . $is_input . '"  data-num="' . $upload_nums . '" id="' . $upload_append_ul . '">';


//            $html .= '<blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;"> 预览：<span class="upload_success_tip">已上传<span class="upload_success_nums">' . count($images_id_arr) . '</span>个文件,</span>';
//            $html .= '<ul data-isinput="' . $is_input . '"  data-num="' . $upload_nums . '" class="image_ids_upoad_append_ul layui-upload-list layui-row flow-default layui-col-space20"  id="' . $upload_append_ul . '">';

			$li_html = "";
//            return true;
			foreach ($images_id_arr as $image_id) {
				$image_obj = $this->image_model->get(["image_id" => $image_id]);
				$src = $image_obj->image_path; //获取图片
				$image_title = $image_obj->image_title;
				$image_size = $image_obj->image_size;
				$images_ext = $image_obj->image_ext;

				$li_html .= '<tr>';
				$li_html .= '<td>' . $image_title . '</td>';
				$li_html .= '<td>' . show_file_size($image_size) . '</td>';
				$li_html .= '<td>' . $images_ext . '</td>';
				$li_html .= '<td>' . '已上传' . '</td>';
				$li_html .= '<input type="hidden" name="' . $fields . '[]" value="' . $image_id . '">';
				$li_html .= '</tr>';
			}

			$html .= $li_html;

			$html .= '</tbody></table></div></div>';

			$script = '<script>
                if(typeof is_uploading=="undefined"){
                    var is_uploading = false;
                }
                layui.use(["upload","form"], function () {
                	var $ = layui.jquery
                    , upload = layui.upload;
                    var form = layui.form;
                    upload.render({
                    elem: "#' . $upload_button . '"
                    , url: "' . $post_url . '"
                    , multiple: ' . ($upload_nums > 1 ? 'true' : 'false') . '
                    , accept: "file"
                    ,acceptMime: "' . $current_type_arr['mime'] . '"
                    ,size: ' . $current_type_arr['size'] . '
                    ,exts:"' . implode("|", explode(",", $current_type_arr['ext'])) . '"
                    ,before: function (obj) {
                    if(is_uploading==false){
                        is_uploading = true;
                        layer.load(); //上传loading
                    }
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                         });
                     }
                    ,allDone:function(obj){
                    var success_nums = obj.successful;
                    var error_nums = obj.aborted;
                     console.log(obj.total); //得到总文件数
                     console.log(obj.successful); //请求成功的文件数
                     console.log(obj.aborted); //请求失败的文件数
                       is_uploading = false;
                       if(error_nums>0){
                           layer.msg(error_nums+"个文件上传失败,请核对",{"time": 1500, icon: 2});
                      }
                     form.render();
                     layer.closeAll("loading"); //关闭loading
                }
            , done: function (res,index,upload) {
                if(res.code==0){
                    var response_data = res.data;       
                    var li_html = \'<tr>\';
                    li_html+= \'<td>\'+response_data.name+\'</td>\';
                    li_html+= \'<td>\'+response_data.show_size+\'</td>\';
                    li_html+= \'<td>\'+response_data.ext+\'</td>\';
                    li_html+= \'<td>已上传</td>\';
                    li_html+= \'<input type="hidden" name="' . ($fields) . '[]" value="tmp_\'+response_data.id+\'">\';
                    li_html+= "</tr>";
                     if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                        $("#' . $upload_append_ul . '").html("");
                        $("#' . $upload_append_ul . '").append(li_html);
                     }else{
                     	$("#' . $upload_append_ul . '").append(li_html);
                     }
                }
                if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                    layer.closeAll("loading"); //关闭loading
                    if(res.code==0){
                        is_uploading = false; 
                    }else {
                        layer.msg("文件上传失败,请核对",{"time": 1500, icon: 2});
                    }
                }
                form.render();
            }, error: function () {
                if(' . ($upload_nums > 1 ? 'true' : 'false') . '==false){
                     layer.msg("文件上传失败,请核对",{"time": 1500, icon: 2});
                     layer.closeAll("loading"); //关闭loading
                }
                form.render();
            }
        });
    })</script>';

			$upload_html = $html . $script;
			$upload_html .= build_sort($upload_append_ul);
			echo $upload_html;
		}


	}