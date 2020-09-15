<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>后台管理-登陆</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="__CDN__/lib/layui/css/layui.css" media="all">
    <!--[if lt IE 9]>
    <script src="__CDN__/js/html5.min.js"></script>
    <script src="__CDN__/js/respond.min.js"></script>
    <![endif]-->
    <style>
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #F2F2F2;
        }

        body:after {
            content: '';
            background-repeat: no-repeat;
            background-size: cover;
            -webkit-filter: blur(3px);
            -moz-filter: blur(3px);
            -o-filter: blur(3px);
            -ms-filter: blur(3px);
            filter: blur(3px);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }

        .layui-container {
            width: 100%;
            height: 100%;
            overflow: hidden
        }

        .admin-login-background {
            width: 360px;
            height: 300px;
            position: absolute;
            left: 50%;
            top: 36%;
            margin-left: -180px;
            margin-top: -100px;
        }

        .logo-title {
            text-align: center;
            letter-spacing: 2px;
            padding: 14px 0;
        }

        .logo-title h1 {
            color: #1E9FFF;
            font-size: 25px;
            font-weight: bold;
        }

        .login-form {
            background-color: #fff;
            border: 1px solid #fff;
            border-radius: 3px;
            padding: 14px 20px;
            box-shadow: 0 0 8px #eeeeee;
        }

        .login-form .layui-form-item {
            position: relative;
        }

        .login-form .layui-form-item label {
            position: absolute;
            left: 1px;
            top: 1px;
            width: 38px;
            line-height: 36px;
            text-align: center;
            color: #d2d2d2;
        }

        .login-form .layui-form-item input {
            padding-left: 36px;
        }

        .captcha {
            width: 60%;
            display: inline-block;
        }

        .captcha-img {
            display: inline-block;
            width: 34%;
            float: right;
        }

        .captcha-img img {
            height: 34px;
            border: 1px solid #e6e6e6;
            height: 36px;
            width: 100%;
        }

        .layui-form h2 {
            margin-bottom: 10px;
            font-weight: 300;
            font-size: 30px;
            color: #000;
        }

        .layadmin-link {
            color: #029789 !important;
            margin-top: 7px;
            float: right
        }
    </style>
</head>
<body>
<div class="layui-container">
    <div class="admin-login-background">
        <div class="layui-form login-form">
            <form class="layui-form">
                <div class="layui-form-item logo-title">
                    <h2><?php echo get_siteinfo("title"); ?>后台登录</h2>
                </div>
                <div class="layui-form-item">
                    <label class="layui-icon layui-icon-username" for="username"></label>
                    <input type="text" name="username" lay-verify="required" placeholder="用户名" lay-reqText="用户名不能为空"
                           autocomplete="off" class="layui-input" value="<?php echo $username; ?>">
                </div>
                <div class="layui-form-item">
                    <label class="layui-icon layui-icon-password" for="password"></label>
                    <input type="password" name="password" lay-verify="required" placeholder="密码"
                           autocomplete="off" class="layui-input" value="<?php echo $password; ?>" lay-reqText="密码不能为空">
                </div>
                <div class="layui-form-item">
                    <label class="layui-icon layui-icon-vercode" for="captcha"></label>
                    <input type="text" name="captcha" lay-verify="required" placeholder="图形验证码"
                           autocomplete="off" class="layui-input verification captcha" value="" lay-reqText="验证码不能为空">
                    <div class="captcha-img">
                        <img id="captchaPic" src="/index.php?s=/captcha"
                             onclick="this.src = '/index.php?s=/captcha&r=' + Math.random();">
                    </div>
                </div>
                <div class="layui-form-item" style="margin-bottom: 20px;">
                    <input type="checkbox" name="remember" lay-skin="primary"
                           title="记住密码" <?php if ($is_remember) echo " checked"; ?>>
                    <div class="layui-unselect layui-form-checkbox" lay-skin="primary"><span>记住密码</span><i
                                class="layui-icon layui-icon-ok"></i></div>
                    <a href="<?php echo url("index/forget");?>" class="layadmin-user-jump-change layadmin-link">忘记密码？</a>
                </div>
                <?php echo_token(); ?>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-btn layui-btn-fluid login_button" lay-submit=""
                            lay-filter="login">登 入
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="__CDN__/js/jquery.min.js" charset="utf-8"></script>
<script src="__CDN__/lib/layui/layui.js" charset="utf-8"></script>
<script src="__ASSETS__/js/xadmin.js" charset="utf-8"></script>


<script>
    layui.use(['form', 'jquery'], function () {
        var form = layui.form,
            layer = layui.layer;
        var $ = layui.$;
        form.on('submit(login)', function (data) {
            data = data.field;
            post_data({
                data: data,
                callBack: function (res) {
                    var url = res.data.url;
                    location.replace(url);
                    // location.href = url;
                },
                ecallBack: function () {
                    $('[name="captcha"]').val("");
                    $("#captchaPic").click();
                }
            });
        });
        $("[name='captcha']").keydown(function (e) {
            if (e.keyCode == 13) {
                $(".login_button").click();
            }
        });
    });
</script>


</body>
</html>