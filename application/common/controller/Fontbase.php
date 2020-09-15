<?php
	/**
	 * Created by PhpStorm.
	 * User: admin
	 * Date: 2019/12/4
	 * Time: 17:13
	 */

	namespace app\common\controller;
	require_once LIB_PATH . 'tencentcloud/TCloudAutoLoader.php';

	use app\common\library\service\HelpService;
	use think\Controller;
	use think\Session;
	use TencentCloud\Common\Credential;
	use TencentCloud\Common\Profile\ClientProfile;
	use TencentCloud\Common\Profile\HttpProfile;
	use TencentCloud\Common\Exception\TencentCloudSDKException;
	use TencentCloud\Captcha\V20190722\CaptchaClient;
	use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;

	use TencentCloud\Sms\V20190711\SmsClient;
	use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
	use xqy\Check;
	use xqy\Token;

	use app\common\controller\Base;

	use app\index\library\Language;

	class Fontbase extends Base
	{

		public $default_order = "orders asc,add_time asc"; //默认的排序方式 先按序号进行排序

		/**
		 * 自动设置语言版本
		 */
		protected function setLang()
		{
			$lang_class = Language::getInstance();
			$lang = $lang_class->autoLang();
			$lang_class->set($lang);
		}


		/**
		 * @param string $view 渲染的模本目录
		 * @param int $lang_mode 语言默认 默认自动自动获取到 LANG 里面的 lang变量
		 * @return mixed
		 */
		protected function xfetch($view = "index", $lang_mode = LANG_MODE_AUTO, $display = true)
		{
			$language = Language::getInstance();
			if ($lang_mode == LANG_MODE_AUTO) {
				$lang = $language->get();
			} elseif ($lang_mode == LANG_MODE_DEFAULT) {
				$lang = $language->getDefault(); //获取默认的语言版本
			} else {
				$lang = "";
			}
			//这里定义好 访问路径
			defined('VIEW_PATH') or define('VIEW_PATH', ROOT_PATH . "application" . DS . "index" . DS . 'view' . DS . ($lang ? $lang . DS : "")); // 环境变量的配置前缀
			//这里要加翻个语言的判断
			$template = ($lang ? $lang . "/" : "") . $view;
			if ($display) {
				return $this->fetch($template);
			} else {
				return $template;
			}
		}

		/**
		 * 重定向
		 */
		protected function xRedirect($param, $lang_mode = LANG_MODE_AUTO)
		{
			if (isset($param['url'])) {
				$url = $param['url'];
			} else {
				$method = isset($param['m']) ? $param['m'] : "index";
				$controller = isset($param['c']) ? $param['c'] : "index";
				$param = isset($param['d']) ? $param['d'] : [];
				$url = create_url($method, $param, $controller);
			}
			return redirect($url, 302);
		}

		protected function xview($path, $data = [])
		{
			if (!empty($data)) {
				return view($path, $data);
			} else {
				return view($path);
			}
		}


		public function _initialize()
		{
			parent::_initialize();
			$this->setLang();
		}


		public function check_sms()
		{
			$mobile = "";
			$mobile_check = Check::Mobile($mobile);
			if ($mobile_check !== true) {
				return $this->failJsonResponse($mobile_check);
			}
			$check_token = Token::check_token();
			if ($check_token === false) {
				return $this->failJsonResponse("token错误 请重新刷新页面");
			}
			$is_pop = Check::isPopSlideVcode(); //是否需要弹出图形验证码
			$token = Token::refresh_token();
			$data = array_merge(["is_pop" => $is_pop], $token);
			return $this->successJsonResponse("", $data);
		}


		protected function tc_sendSms($mobile = "", $code = "", $company = "")
		{
			try {
				//腾讯云key secret
				$cred = new Credential("", "");
				$httpProfile = new HttpProfile();
				$httpProfile->setEndpoint("sms.tencentcloudapi.com");
				$clientProfile = new ClientProfile();
				$clientProfile->setHttpProfile($httpProfile);
				$client = new SmsClient($cred, "", $clientProfile);
				$req = new SendSmsRequest();
				$params = '{"PhoneNumberSet":["+86' . $mobile . '"],"TemplateID":"550704","Sign":"' . $company . '","TemplateParamSet":["' . $code . '"],"SmsSdkAppid":"1400328858"}';
				$req->fromJsonString($params);
				$resp = $client->SendSms($req);
				$resp = @json_decode($resp->toJsonString(), true);
				if (!empty($resp) && isset($resp['SendStatusSet']) && $resp['SendStatusSet'][0]['Code'] == 'Ok') {
					return true;
				} else {
					return false;
				}
			} catch (TencentCloudSDKException $e) {
				return false; //短信发送失败
			}

		}

		/*
		 * 检验用户滑动验证码是否滑动了
		 */
		public function slide_code($slide_code = [])
		{
			if (!config("is_slide_code")) {
				return $this->successJsonResponse("");
			}
			$check_token = Token::check_token();
			if ($check_token === false) {
				return $this->failJsonResponse("token错误 请重新刷新页面");
			}
			if (empty($slide_code['ticket']) || empty($slide_code['randstr'])) {
				return $this->failJsonResponse("滑动验证失败请重试");
			}
			$ticket = (string)$slide_code['ticket'];
			$randstr = (string)$slide_code['randstr'];
			$ip = $this->request->ip();
			try {
				$cred = new Credential("AKIDOYR3PXn0MUAbmQfvmMetsgCUXJp5eFyR", "LQEDwXrviDvNcZFenhLRHq9BP7bqLaXC");
				$httpProfile = new HttpProfile();
				$httpProfile->setEndpoint("captcha.tencentcloudapi.com");
				$clientProfile = new ClientProfile();
				$clientProfile->setHttpProfile($httpProfile);
				$client = new CaptchaClient($cred, "", $clientProfile);
				$req = new DescribeCaptchaResultRequest();
				$params = '{"CaptchaType":9,"Ticket":"' . $ticket . '","UserIp":"' . $ip . '","Randstr":"' . $randstr . '","CaptchaAppId":2073153289,"AppSecretKey":"0oNY_mvSNCqU_Fd6q6jqliA**"}';
				$req->fromJsonString($params);
				$resp = $client->DescribeCaptchaResult($req);
				$resp = @json_decode($resp->toJsonString(), true);
				if (!empty($resp) && isset($resp['CaptchaCode']) && $resp['CaptchaCode'] == 1) {
					$slide_code['check'] = true;
					$slide_code['remain'] = 3;
					Session::set("slide_code", $slide_code);
					$token = Token::refresh_token();
					return $this->successJsonResponse("", $token); //滑动成功 更新token
				}
			} catch (TencentCloudSDKException $e) {
				return $this->successJsonResponse("滑动验证失败 请重试");
			}
		}

		/**
		 * 发送短信验证码
		 * @param string $mobile
		 * @return string
		 */
		public function send_sms()
		{
			$mobile = "";
			$mobile_check = Check::Mobile($mobile);
			if ($mobile_check !== true) {
				return $this->failJsonResponse($mobile_check);
			}
			$check_token = Token::check_token();
			if ($check_token === false) {
				return $this->failJsonResponse("token错误 请重新刷新页面");
			}
			$check_slide = Check::SlideVcode();
			if ($check_slide !== true) {
				return $this->failJsonResponse($check_slide);
			}
			$tel_code = HelpService::getRandRum(4);//四位验证码
			$sms_send_result = $this->tc_sendSms($mobile, $tel_code);
			/**无论发送验证码是否成功 都记录好验证码先 防止因为运营商问题 导致发送出去但是我们这边没记录好**/
			if (Session::has("mobile_vcode")) {
				$mobile_vcode = Session::get("mobile_vcode");
				if ($mobile_vcode['mobile'] != $mobile) {
					$mobile_vcode = [];
				}
			} else {
				$mobile_vcode = [];
			}
			$mobile_vcode['mobile'] = $mobile; //设定手机号
			$mobile_vcode['code'][] = $tel_code;
			Session::set("mobile_vcode", $mobile_vcode);
			/**无论发送验证码是否成功 都记录好验证码先 防止因为运营商问题 导致发送出去但是我们这边没记录好**/
			if ($sms_send_result) { //发送成功
				return $this->successJsonResponse("验证码发送成功", ["code" => $tel_code]);
			} else {
				return $this->failJsonResponse("验证码发送失败");
			}
		}

		public function check_token_expire()
		{
//			$token = Token::refresh_token();
//			return $this->failJsonResponse("", $token);
			$result = check_token_expire();
			if ($result) {
				return $this->successJsonResponse("");
			} else {
				$token = Token::refresh_token();
				return $this->failJsonResponse("", $token);
			}
		}

	}