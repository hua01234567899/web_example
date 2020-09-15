<?php

	use think\Route;

	//前台路由构建
//	Route::rule('index', 'index/index/index');
//	Route::rule('message', 'index/index/message');
	Route::rule('about', 'index/index/about');
    Route::rule('team', 'index/index/team');
    Route::rule('price', 'index/index/price');


	Route::rule('contact', 'index/index/contact');

	Route::rule('customized', 'index/index/customized');

	Route::rule('customized_message', 'index/index/customized_message');
	Route::rule('down_load', 'index/index/down_load');
	Route::rule('login', 'index/index/login');

	Route::rule('news_detail/:id', 'index/index/news_detail');

//	Route::rule('news_list/[:page]/[:class_id]', 'index/index/news_list');
	Route::rule('news_list-<class_id>', 'index/index/news_list');
	Route::rule('news_list', 'index/index/news_list');
//	Route::rule('news_list', 'index/index/news_list');


	Route::rule('register', 'index/index/register');

	Route::rule('seo', 'index/index/seo');

//	Route::rule('small_web', 'index/index/small_web');

	Route::rule('web-<class_id>', 'index/index/web');

	Route::rule('web', 'index/index/web');


    Route::rule('small_web-<class_id>', 'index/index/small_web');

    Route::rule('small_web', 'index/index/small_web');

    Route::rule('web_detail/:id', 'index/index/web_detail');

	Route::rule('web_detail', 'index/index/web_detail');

	//用户登录路由
	Route::rule('login/index', 'index/login/index');
	Route::rule('login/invoice', 'index/login/invoice');
	Route::rule('login/msg', 'index/login/msg');
	Route::rule('login/news_detail', 'index/login/news_detail');
	Route::rule('login/news_list', 'index/login/news_list');
	Route::rule('login/order', 'index/login/order');
	Route::rule('login/replay', 'index/login/replay');
	Route::rule('login/service', 'index/login/service');
	Route::rule('login/template', 'index/login/template');


//	Route::miss('index/index/not_find');

//    Route::rule('__miss__', 'index/index/not_find');

	// +-------------------------------------------------
	//---------------------
	// | ThinkPHP [ WE CAN DO IT JUST THINK ]
	// +----------------------------------------------------------------------
	// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
	// +----------------------------------------------------------------------
	// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
	// +----------------------------------------------------------------------
	// | Author: liu21st <liu21st@gmail.com>
	// +----------------------------------------------------------------------
	//    return [
	//        '__pattern__' => [
	//            'name' => '\w+',
	//        ]
	//    ];
