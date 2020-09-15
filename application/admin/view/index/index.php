<!doctype html>
<html lang="en">
<head>
    {include file="common:header"/}
</head>
<body>
{include file="common:top"/}
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
{include file="common:left_nav"/}
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
        <ul class="layui-tab-title">
            <li class="home"><i class="layui-icon">&#xe68e;</i>我的桌面</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src='<?php echo url("index/welcome"); ?>' frameborder="0" scrolling="yes"
                        class="x-iframe"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="page-content-bg"></div>
</body>
</html>

