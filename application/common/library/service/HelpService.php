<?php
	/**
	 * Created by PhpStorm.
	 * User: hua
	 * Date: 2020/3/14
	 * Time: 16:24
	 */
	
	namespace app\common\library\service;

	class HelpService
	{
		
		public static function getRandRum($num=4){
			$characters = '0123456789';
			$randomString = '';
			for ($i = 0; $i < $num; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
			return $randomString;
		}
		/**
		 * 过滤掉表情符号
		 * @param $str
		 * @return null|string|string[]
		 */
		public static function filterEmoji($str)
		{
			$str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
				'/./u',
				function (array $match) {
					return strlen($match[0]) >= 4 ? '' : $match[0];
				},
				$str);
			return $str;
		}
		
		public static function curlGet($url = '', $options = array())
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			if (!empty($options)) {
				curl_setopt_array($ch, $options);
			}
			//https请求 不验证证书和host
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		public static function curlPost($url = '', $postData = '', $options = array())
		{
			if (is_array($postData)) {
				$postData = http_build_query($postData);
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
			if (!empty($options)) {
				curl_setopt_array($ch, $options);
			}
			//https请求 不验证证书和host
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		
		
	}