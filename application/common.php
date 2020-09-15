<?php
// 公共助手函数
    use app\common\controller\Tool;
    use app\common\model\ToolModel;
    use xqy\Token;
    use xqy\Check;
    use app\index\library\Language;
    use app\common\model\db\Btype;
    use app\common\model\Site;
    use app\common\library\upload\Upload;
    use app\common\model\Images;
    use app\admin\library\itxq\IP as IpAddress;
    use think\Db;
    use app\common\model\Sysconfig as model_sysconfig;

    use app\common\model\Servicer as model_servicer;
    use app\common\model\Links as model_links;
    use app\common\model\AdminConfig as model_adminconfig;

    if (!function_exists("think_encrypt")) {
        /**
         * 对字符串简单的加密
         * @param $string
         * @param string $key
         * @return bool|mixed|string
         */
        function think_encrypt($string, $key = '')
        {
            $key = md5($key);
            $key_length = strlen($key);
            $operation = "E";
            $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
            $string_length = strlen($string);
            $rndkey = $box = array();
            $result = '';
            for ($i = 0; $i <= 255; $i++) {
                $rndkey[$i] = ord($key[$i % $key_length]);
                $box[$i] = $i;
            }
            for ($j = $i = 0; $i < 256; $i++) {
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
            }
            for ($a = $j = $i = 0; $i < $string_length; $i++) {
                $a = ($a + 1) % 256;
                $j = ($j + $box[$a]) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
            }
            if ($operation == 'D') {
                if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                    return substr($result, 8);
                } else {
                    return false;
                }
            } else {
                $str = str_replace('=', '', base64_encode($result));
                $str = str_replace("+", "@_123_@", $str);
                return $str;
            }
        }
    }


    if (!function_exists("think_decrypt")) {
        /**
         * @param $string
         * @param string $key
         * @param string $default
         * @return bool|mixed|string
         */
        function think_decrypt($string, $key = '', $default = "")
        {
            $string = str_replace("@_123_@", "+", $string);
            $key = md5($key);
            $key_length = strlen($key);
            $operation = "D";
            $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
            $string_length = strlen($string);
            $rndkey = $box = array();
            $result = '';
            for ($i = 0; $i <= 255; $i++) {
                $rndkey[$i] = ord($key[$i % $key_length]);
                $box[$i] = $i;
            }
            for ($j = $i = 0; $i < 256; $i++) {
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
            }
            for ($a = $j = $i = 0; $i < $string_length; $i++) {
                $a = ($a + 1) % 256;
                $j = ($j + $box[$a]) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
            }
            if ($operation == 'D') {
                if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                    return substr($result, 8);
                } else {
                    return $default;
                }
            } else {
                return str_replace('=', '', base64_encode($result));
            }
        }
    }


    if (!function_exists("array_filter_not_int")) {
        function ids_filter_not_int($ids, $is_return_string = false, $separator = ",")
        {
            if (is_string($ids)) {
                $id_array = explode($separator, $ids);
            } else {
                $id_array = (array)$ids;
            }
            $new_array = array();
            foreach ($id_array as $key => $item) {
                if (is_numeric($item)) {
                    $new_array[] = (int)$item;
                }
            }
            if ($is_return_string) {
                return implode($separator, $new_array);
            } else {
                return $new_array;
            }
        }
    }

    if (!function_exists("cut_str")) {
        function cut_str($content, $lenth = 130)
        {
            $content = trim($content);
            $content = trim($content, "　");
            $content_01 = $content;  //从数据库获取富文本content
            $content_02 = htmlspecialchars_decode($content_01);  //把一些预定义的 HTML 实体转换为字符
            $content_03 = str_replace("&nbsp;", "", $content_02); //将空格替换成空
            $contents = strip_tags($content_03);  //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
            $con = mb_substr($contents, 0, $lenth, "utf-8");  //返回字符串中的前100字符串长度的字符
            return $con;
        }
    }


    if (!function_exists("to_array")) {
        function to_array($arr)
        {
            if (empty($arr)) {
                $arr = [];
            } else {
                if (gettype($arr) == "object") {
                    $arr = $arr->toArray();
                } else {
                    $arr = collection($arr)->toArray();
                }
            }
            return $arr;
        }
    }

    if (!function_exists("first_child_id")) {
        /**
         * 获取数组第一个元素的 id
         */
        function first_child_id($arr, $field)
        {
            $id = 0;
            if (is_array($arr) && count($arr) > 0) {
                $row = $arr[0];
                $id = $row[$field];
            }
            return $id;
        }
    }

    if (!function_exists("not_ajax")) {
        /**
         * 不是ajax请求就直接跳到404
         */
        function not_ajax()
        {
            if (defined("IS_AJAX") && !IS_AJAX) {
                $c_tool = new Tool();
                $c_tool->page_404();
                exit;
            }
        }
    }


    if (!function_exists("token_expired")) {
        /**
         * 检查token 是否已经过了有效期
         */
        function token_expired()
        {
            $token_check_result = Token::check_token();//先验证TOKEN
            if (!$token_check_result) {
                $c_tool = new Tool();
                echo $c_tool->failJsonResponse("token过期请刷新页面重试");
                exit;
            } else {
                return true;
            }
        }
    }


    if (!function_exists("mobile_vcode_start")) {
        function mobile_vcode_start()
        {
            $error_msg = Check::MobileVcode();
            if ($error_msg !== true) {
                post_fail($error_msg);
                exit;
            } else {
                return true;
            }
        }
    }
    if (!function_exists("mobile_vcode_end")) {
        function mobile_vcode_end()
        {
            Check::clearMobileVode();
        }
    }
    if (!function_exists("get_ip")) {
        function get_ip()
        {
            $ip = request()->ip();
            return $ip;
        }
    }


    if (!function_exists("auto_mid")) {
        /**
         * 自动将前台传过去的mid 传过来 加解密
         */
        function auto_mid()
        {
            $menu_id = input('post.mid/s');
            $menu_id = (int)(think_decrypt($menu_id));
            return $menu_id;
        }
    }


    if (!function_exists("post_hash_start")) {
        /**
         *使用假设成功插入法进行判断
         * @param $model
         * @param $hash
         */
        function post_hash_start($table, $hash)
        {
            $time = date("Y-m-d H:i:s", strtotime('-1minute'));
            $ip = get_ip();
            $hash = $table . $hash;
            $hash = md5($hash);
            $data = [];
            $data['type'] = $table;
            $data['hash'] = $hash;
            $data['ip'] = $ip;
            try {
                $tool_model = new ToolModel();
                $tool_model->name("table_hash");
                $c_tool = new Tool();
                //20秒内不允许有提交
                $min_second = date("Y-m-d H:i:s", strtotime('-' . (5) . 'second'));
                $min_counts = $tool_model->where("hash", $hash)->where("create_time", ">", $min_second)->count();
                if ($min_counts > 0) {
                    post_fail("请不要重复提交数据");
//					echo $c_tool->failJsonResponse("请不要重复提交数据");
                    exit;
                }
                $counts = $tool_model->where("ip", $ip)->where("create_time", ">", $time)->count();
                if ($counts > 5) {
                    //一分钟不要提交5次
                    post_fail("请不要频繁提交数据");
//					echo $c_tool->failJsonResponse("请不要频繁提交数据");
                    exit;
                }
                $hash_data = $data;
                return $hash_data;
            } catch (Exception $e) {
                post_fail("请不要重复提交数据");
//				echo $c_tool->failJsonResponse("请不要重复提交数据");
                exit;
            }
        }
    }

    if (!function_exists("post_hash_end")) {
        function post_hash_end($data)
        {
            $tool_model = new ToolModel();
            $tool_model->name("table_hash");
            $tool_model->data($data);
            $tool_model->save();
        }
    }


    if (!function_exists("post_success")) {
        function post_success($tips_arr = [])
        {
            $tips = "";
            $c_tool = new Tool();
            $lang = Language::getInstance()->get();
            if (!empty($tips_arr)) {
                if (isset($tips_arr[$lang])) {
                    $tips = $tips_arr[$lang];
                }
            } else {
                if (empty($lang) || $lang == "cn") {
                    $tips = "提交成功";
                } else {
                    $tips = "SUCCESS";
                }
            }
            $token = Token::refresh_token();
            echo $c_tool->successJsonResponse($tips, $token);
            exit;
        }
    }

    if (!function_exists("post_fail")) {
        function post_fail($tips_arr = "")
        {
            $c_tool = new Tool();
            if (is_string("tips_arr")) {
                $tips = $tips_arr;
            } else {
                $lang = Language::getInstance()->get();
                if (!empty($tips_arr)) {
                    if (isset($tips_arr[$lang])) {
                        $tips = $tips_arr[$lang];
                    }
                } else {
                    if (empty($lang) || $lang == "cn") {
                        $tips = "提交失败";
                    } else {
                        $tips = "FAIL";
                    }
                }
            }
            $token = Token::refresh_token();
            echo $c_tool->failJsonResponse($tips, $token);
            exit;
        }
    }

    if (!function_exists("auto_page")) {
        function auto_page()
        {
            $page = input('param.page/d');
            $page = max(1, $page);
//			if (IS_AJAX) {
//				//如果是加载更多的话就自动加 +1
//				$page = $page + 1;
//			}
            return $page;
        }
    }

    if (!function_exists("type_arr")) {
        function type_arr($table)
        {
            $btype = new Btype();
            $type_arr = $btype->getTypeArr($table);
            return $type_arr;
        }
    }


    if (!function_exists("one_arr")) {
        function one_arr($table, $conArr = ["status" => 1])
        {
            $class = new ToolModel();
            $arr = $class->getRowArr($table, $conArr);
            return $arr;
        }
    }


    if (!function_exists("total_list_arr")) {
        function total_list_arr($table, $conArr = ["status" => 1], $orderby = "", $start, $length)
        {
            $data = ["total" => 0, "list" => []];
            if (empty($orderby)) {
                $orderby = WEB_SORT;
            }
            $class = new ToolModel();
            $data = $class->getListArr($table, $conArr, $orderby, $start, $length);
            return $data;
        }
    }

    if (!function_exists("check_token_expire")) {
        function check_token_expire()
        {
            $token_check_result = Token::check_token();//先验证TOKEN
            if (!$token_check_result) {
                return false;
            } else {
                return true;
            }
        }
    }
    if (!function_exists("create_token")) {
        function create_token()
        {
            Token::create_token(); //产生token
        }
    }

    if (!function_exists("echo_token")) {
        function echo_token()
        {
            Token::echo_token();
        }
    }


    if (!function_exists("create_url")) {
        /**
         * @param string $method
         * @param string $controller
         * @param array $param
         */
        function create_url($method = "", $param = [], $controller = "index", $module = "index")
        {

            if (!empty($param) && is_string($param)) {
                $param2 = $param;
                parse_str($param, $param);
            } elseif (empty($param) || !is_array($param)) {
                $param = [];
            }
            $url = url($module . "/" . $controller . "/" . $method, $param);
            //禁止出现这种访问链接
            if (strpos($url, $module . "/" . $controller . "/" . $method) !== false) {
                $url = "/index.php?s=" . $module . "/"; //暂时这样写先
                $method = (string)$method;
                $controller = strtolower($controller);
                $method = strtolower($method);
                $url = $url . $controller . "/" . $method;
                if (is_array($param) && !empty($param)) {
                    $param = http_build_query($param);
                    $url .= "&" . $param;
                }
                return $url;
            } else {
                //判断是否有使用了路由
                //这里就是使用了路由
                if ($url == 'index.php' || $url == 'index.php/') {
                    $url = "/";
                } elseif (strrpos($url, "index.php/") !== false) {
                    //出现了
                    $position = strpos($url, "index.php/");
                    $position = $position + strlen("index.php/");
                    $url = substr($url, $position);
                }
            }
            return $url;
        }
    }


    if (!function_exists("get_siteinfo")) {
        /**
         * 获取网站信息
         * $column 指定获取的某个信息 all 就是获取全部信息
         */
        function get_siteinfo($column = "all")
        {
            $model_site = new Site();
            $info = $model_site->get(["status" => 1]);
            $info = to_array($info);
            if ($column == "1" || $column == "all" || $column == "ALL") {
                return $info;
            } else {
                if ($column == 'logo') {
                    $value = $info['logo_id'];
                    $value = get_cover_image($value);
                } else {
                    if (!isset($info[$column])) {
                        exception("get_siteinfo参数错误");
                        exit;
                    }
                    $value = $info[$column];
                }
                return $value;
            }
        }
    }


    if (!function_exists("get_user_config")) {
        function get_user_config($column)
        {
            $model_sysconfig = new model_sysconfig();
            $value = $model_sysconfig->config_value($column);
            return $value;
        }
    }


    if (!function_exists("get_admin_config")) {
        function get_admin_config($column)
        {
            $model_sysconfig = new model_adminconfig();
            $value = $model_sysconfig->config_value($column);
            return $value;
        }
    }


    if (!function_exists("upload_style")) {
        function upload_style($method, $params = [])
        {
            $upload = new Upload();
            call_user_func_array(array($upload, $method), [$params]);
        }
    }


    if (!function_exists("get_cover_image")) {
        /**
         * 获取图片
         * @param $image_id
         * @param int $is_size
         * is_size 就是 0 默认缩略图    1代表 原图 2 代表全部都要
         */
        function get_cover_image($image_id, $default_img = "", $is_size = 0)
        {
            if (!empty($image_id)) {
                $image_id = explode(",", $image_id);
                $image_id = $image_id[0]; //获取第一张图片
                $img_row = Images::get(['image_id' => $image_id, "status" => 1]);
                if ($is_size === 0) {
                    $images = $img_row->thumb_path;
                } elseif ($is_size === 1) {
                    $images = $img_row->image_path;
                } else {
                    $images = array("images" => $img_row->image_path, "thumb" => $img_row->thumb_path);
                }
            } else {
                if (!empty($default_img)) {
                    $images = $default_img;
                } else {
                    if (!defined("IS_ADMIN_REQUEST")) {
                        $images = get_user_config("default_cover_img_id");
                    } else {
                        $images = get_user_config("admin_user_default_img_id");
                    }
                    if (!empty($images)) {
                        $images = Images::get(['image_id' => $images, "status" => 1]);
                        $images = $images->image_path;
                    } else {
                        $images = "";
                    }

                }
                if ($is_size === 2) {
                    $images = array("images" => $images, "thumb" => $images);
                }
            }
            return $images;
        }
    }

    if (!function_exists("get_much_images")) {
        function get_much_images($images_ids, $template = "")
        {
            $html_arr = [];
            $arr = [];
            $images_id_arr = explode(",", $images_ids);
            foreach ($images_id_arr as $images_id) {
                $image = get_cover_image($images_id);
                $arr[] = $image;
                if (!empty($template)) {
                    $html_arr[] = str_replace("#src#", $image, $template);
                }
            }
            if (!empty($template)) {
                return implode("", $html_arr);
            } else {
                return $arr;
            }
        }
    }

    if(!function_exists("get_menu_row")){
    	function get_menu_row($menu_id){
			$menu_id = (int)$menu_id;
			try {
				$menu_row = \think\Db::name("menu")->find($menu_id);
				if (!empty($menu_row)) {
					return $menu_row;
				} else {
					return [];
				}
			} catch (Exception $e) {
				return [];
			}
		}
	}

    if (!function_exists("get_menu_title")) {
        function get_menu_title($menu_id)
        {
            $menu_id = (int)$menu_id;
            try {
                $menu_row = \think\Db::name("menu")->find($menu_id);
                if (!empty($menu_row)) {
                    return $menu_row['title'];
                } else {
                    return "";
                }
            } catch (Exception $e) {
                return "";
            }
        }
    }

    if (!function_exists("ip_to_address")) {
        function ip_to_address($ip, $mode = 1)
        {
            $IP = new IpAddress();
            $ipRange = $IP->getIpInfo($ip);
            if (!empty($ipRange)) {
                $address = $ipRange['address'] . $ipRange['area'];
            } else {
                $address = $ip;
            }
            return $address;
        }
    }

    if (!function_exists("time_show")) {
        function time_show($time)
        {
            if (empty($time)) {
                return "";
            }
            if (date("Y-m-d H:i:s", strtotime($time)) != $time) {
                return $time;
            }
            $timestamp = strtotime($time);
            if (date('Y-m-d') == date('Y-m-d', $timestamp)) {
                return "今天 " . date('H:i:s', $timestamp);
            } elseif (date('Y-m') == date('Y-m', $timestamp)) {
                return "本月 " . date('d号 H:i', $timestamp);
            } elseif (date('Y') == date('Y', $timestamp)) {
                return date('m月d号 H:i', $timestamp);
            } else {
                return date('Y-m-d H:i', $timestamp);
            }
        }
    }


    if (!function_exists("show_file_size")) {
        function show_file_size($size)
        {
            if (is_numeric($size)) {

            } elseif (is_file($size)) {
                $size = filesize($size);
            } else {
                return 0;
            }
            // 单位自动转换函数
            $kb = 1024;         // Kilobyte
            $mb = 1024 * $kb;   // Megabyte
            $gb = 1024 * $mb;   // Gigabyte
            $tb = 1024 * $gb;   // Terabyte

            if ($size < $kb) {
                return $size . " B";
            } else if ($size < $mb) {
                return round($size / $kb, 2) . " KB";
            } else if ($size < $gb) {
                return round($size / $mb, 2) . " MB";
            } else if ($size < $tb) {
                return round($size / $gb, 2) . " GB";
            } else {
                return round($size / $tb, 2) . " TB";
            }
        }
    }

    if (!function_exists("get_dir_size")) {
        // 单位自动转换函数
        function get_dir_size($dir)
        {
            $sizeResult = 0;
            $handle = opendir($dir);
            while (false !== ($FolderOrFile = readdir($handle))) {
                if ($FolderOrFile != "." && $FolderOrFile != "..") {
                    if (is_dir("$dir/$FolderOrFile")) {
                        $sizeResult += get_dir_size("$dir/$FolderOrFile");
                    } else {
                        $sizeResult += filesize("$dir/$FolderOrFile");
                    }
                }
            }
            closedir($handle);
            return $sizeResult;
        }

    }

    if (!function_exists("get_current_time")) {
        function get_current_time()
        {
            return date("Y-m-d H:i:s");
        }
    }


    if (!function_exists("get_system_version")) {
        function get_system_info()
        {
            $row = [];
            $row['company'] = get_siteinfo("title");
            $row['version'] = SYSTEM_VERSION;
            $row['system'] = PHP_OS;
            $row['lang'] = 'PHP+MySQL';
            $row['server'] = $_SERVER["SERVER_SOFTWARE"];
            $row['php_version'] = PHP_VERSION;
            $row['support'] = get_siteinfo("support_name");
            $row['support_url'] = get_siteinfo("support_url");
            $row['developer'] = get_siteinfo("developer");
            return $row;
        }
    }


    if (!function_exists("get_space_info")) {
        /**
         * 获取空间信息
         */
        function get_space_info()
        {
            $row = [];
            $model = new model_adminconfig();
            $total_space = 0;
            try {
                $total_space = $model->get(["config_name" => "total_space"]);
                if (!empty($total_space)) {
                    $total_space = to_array($total_space);
                    $total_space = (int)$total_space['config_value'];
                } else {
                    $total_space = 0;
                }
            } catch (Exception $e) {
            }

            $user_size = get_dir_size(USER_DATA_PATH);
            $row['total_space'] = show_file_size($total_space);
            $row['used_space'] = show_file_size($user_size);
            $row['remain_sapce'] = show_file_size($total_space - $user_size);

            $row['image_upload'] = "";
            $row['file_upload'] = "";
            $row['enddate_space'] = "";
            try {
                $file_row = $model->get(["config_name" => "file_type"]);
                if (!empty($file_row)) {
                    $file_set = to_array($file_row);
                    if (!empty($file_set['config_value'])) {
                        $file_set = $file_set['config_value'];
                        $file_set = json_decode($file_set, true);
                        $row['image_upload'] = show_file_size($file_set['image']['size']);
                        $row['file_upload'] = show_file_size($file_set['file']['size']);
                    }
                }
            } catch (Exception $e) {
            }

            try {
                $enddate_space_row = $model->get(["config_name" => "enddate_space"]);
                if (!empty($enddate_space_row)) {
                    $enddate_space_row = to_array($enddate_space_row);
                    if (!empty($enddate_space_row['config_value'])) {
                        $row['enddate_space'] = $enddate_space_row['config_value'];
                    }
                }
            } catch (Exception $e) {
                $row['enddate_space'] = "";
            }
            return $row;
        }
    }


    if (!function_exists("get_table_counts")) {
        /**
         * 统计table里面所有除了 status = 2的数据
         * @param $table
         */
        function get_table_counts($table)
        {
            $count = 0;
            if (!is_array($table)) {
                $table = [$table];
            }
            foreach ($table as $table_row) {
                try {
                    $count += Db::name($table_row)->where('status', "<>", 2)->count();
                } catch (Exception $e) {
                }
            }
            return $count;
        }
    }

    if (!function_exists("get_servicers")) {
        function get_servicers()
        {
            try {
                $model_servicer = new model_servicer();
                $servicer = $model_servicer->where(["status" => 1])->order(WEB_SORT)->select();
                $servicer = to_array($servicer);
            } catch (Exception $e) {
                $servicer = [];
            }
            return $servicer;
        }
    }


    if (!function_exists("get_outlinks")) {
        function get_out_links()
        {
            try {
                $model_links = new model_links();
                $links = $model_links->where(["status" => 1])->order(WEB_SORT)->select();
                $links = to_array($links);
            } catch (Exception $e) {
                $links = [];
            }
            return $links;
        }
    }

    /**
     * 判断是否是json字符串
     * 如果是返回一个数组 不是就是空
     */
    if (!function_exists("is_join")) {
        function is_json($string)
        {
            $result = json_decode($string, true);
            $result2 = (json_last_error() == JSON_ERROR_NONE);
            if ($result2) {
                return $result;
            } else {
                return false;
            }

        }
    }









