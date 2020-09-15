<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2020/3/3
	 * Time: 8:53
	 * 定义我们需要用到的常量
	 */
	//设置编码
	mb_internal_encoding("UTF-8");

//常量定义文件
	//定义 public 目录
//	defined('PUBLIC_PATH') or define('PUBLIC_PATH', ROOT_PATH . "public" . DS); // 环境变量的配置前缀

	defined('PUBLIC_PATH') or define('PUBLIC_PATH', ROOT_PATH . "htdocs" . DS); // 环境变量的配置前缀

	//定义 后台目录
	defined("ADMIN_PATH") or define("ADMIN_PATH", APP_PATH . "admin" . DS);

	//临时存放的目录
	defined("TMP_PATH") or define("TMP_PATH", PUBLIC_PATH . "tmp" . DS);

	defined("ACCESS_LOG_PATH") or define("ACCESS_LOG_PATH",ROOT_PATH . "access_log" . DS);

	//定义存放用户数据的目录
	defined("USER_DATA_PATH") or define("USER_DATA_PATH", PUBLIC_PATH . "user" . DS);

	$public_url_root = $_SERVER['DOCUMENT_ROOT'];
	$public_url_root = str_replace(array('\\\\', "\\"), "/", $public_url_root);
	$public_url_root = str_replace($public_url_root, "", str_replace(array('\\\\', "\\"), "/", PUBLIC_PATH));
	defined('PUBLIC_URL_PATH') or define('PUBLIC_URL_PATH', $public_url_root); // 环境变量的配置前缀

	//不同语言进行模板渲染
	define("LANG_MODE_AUTO", 1); //是否进行自动匹配语言状态
	define("LANG_MODE_DEFAULT", 2); //是否使用默认的语言状态
	define("LANG_MODEL_FALSE", 3); //不使用 语言


	//定义水印图片的位置
	defined("WATER_NORTHWEST") or define("WATER_NORTHWEST", 1);  //左上角 西北方向

	defined("WATER_NORTH") or define("WATER_NORTH", 2);        //上居中

	defined("WATER_NORTHEAST ") or define("WATER_NORTHEAST", 3); //右上角

	defined("WATER_WEST") or define("WATER_WEST", 4);  //左居中

	defined("WATER_CENTER") or define("WATER_CENTER", 5); //居中

	defined("WATER_EAST") or define("WATER_EAST", 6); //右居中

	defined("WATER_SOUTHWEST") or define("WATER_SOUTHWEST", 7);  //左下角

	defined("WATER_SOUTH") or define("WATER_SOUTH", 8); //下居中

	defined("WATER_SOUTHEAST") or define("WATER_SOUTHEAST", 9); //右下角


	defined("DEFAULT_THUMB_WIDTH") or define("DEFAULT_THUMB_WIDTH", 600); //图片缩略图宽度的尺寸

	defined("DEFAULT_THUMB_HEIGHT") or define("DEFAULT_THUMB_HEIGHT", 600); //图片缩略图宽度的尺寸

	defined("WEB_SORT") or define("WEB_SORT", "orders asc,add_time desc");

	defined("CLASS_WEB_SORT") or define("CLASS_WEB_SORT", "orders asc,add_time asc");

	defined("SYSTEM_VERSION") or define("SYSTEM_VERSION", "fs-xqy-2.0");




