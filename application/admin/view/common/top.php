<!-- 顶部开始 -->
<div class="container">
    <div class="logo"><a href="<?php echo url("index/index") ?>"><?php echo get_siteinfo("title"); ?></a></div>
    <div class="left_open">
        <i title="展开左侧栏" class="iconfont">&#xe699;</i>
    </div>
    <ul class="layui-nav left fast-add" lay-filter="">
    </ul>

    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;"><?php echo $admin_info['nickname'];?></a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a  class="_href" href="javascript:void(0);" _href="<?php echo url("index/userinfo");?>"  tab_id="88888" >基本资料</a></dd>
                <dd><a  class="_href" href="javascript:void(0);" _href="<?php echo url("index/password");?>"  tab_id="99999" >修改密码</a></dd>
                <dd><a href="<?php echo url("index/login_out"); ?>">退出</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item to-index"><a href="/" target="_blank">前台首页</a></li>
    </ul>
</div>
<!-- 顶部结束 -->