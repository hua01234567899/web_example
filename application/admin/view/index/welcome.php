<!doctype html>
<html lang="en">
<head>
    {include file="common:header"/} <!--引入顶部公共模板文件-->
    {block name="header_script"}  <!--引入头部的style 代码 secret代码-->
    {/block}
</head>
<body>
<div class="x-body layui-anim layui-anim-up">
    <blockquote class="layui-elem-quote">欢迎管理员：
        <span><?php echo $admin_info['nickname'] . "（" . $admin_info['username'] . "）" ?></span>！当前时间:<?php echo get_current_time(); ?>
    </blockquote>
    <fieldset class="layui-elem-field">
        <legend>数据统计</legend>
        <div class="layui-field-box">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-carousel x-admin-carousel x-admin-backlog" lay-anim="" lay-indicator="inside"
                             lay-arrow="none" style="width: 100%; height: 90px;">
                            <div carousel-item="">
                                <ul class="layui-row layui-col-space10 layui-this">

                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>会员注册</h3>
                                            <p>
                                                <cite><?php echo get_table_counts("member"); ?></cite></p>
                                        </a>
                                    </li>

                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>新闻资讯</h3>
                                            <p>
                                                <cite><?php echo get_table_counts("news"); ?></cite></p>
                                        </a>
                                    </li>
                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>产品展示</h3>
                                            <p>
                                                <cite><?php echo get_table_counts("product") ?></cite></p>
                                        </a>
                                    </li>

                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>用户留言</h3>
                                            <p>
                                                <cite><?php echo get_table_counts(["message", "customized"]) ?></cite>
                                            </p>
                                        </a>
                                    </li>


                                    <li class="layui-col-xs2">
                                        <a  href="<?php echo url("export_web_log");?>" class="x-admin-backlog-body">
                                            <h3>网络日志</h3>
                                            <p>
                                                <cite>下载</cite>
                                            </p>
                                        </a>
                                    </li>


                                    <li class="layui-col-xs2">
                                        <a target="_blank" href="https://tongji.baidu.com/web/welcome/login"
                                           class="x-admin-backlog-body">
                                            <h3>百度统计</h3>
                                            <p>
                                                <cite style="font-size: 24px;">统计跳转</cite>
                                            </p>
                                        </a>
                                    </li>


                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>最新新闻</legend>
        <div class="layui-field-box">
            <table class="layui-table" lay-skin="line">
                <tbody>
                <?php
                    foreach ($new_list as $new_row) {
                        ?>
                        <tr>
                            <td>
                                <a class="x-a" href="/news_detail/<?php echo $new_row['id']; ?>.html"
                                   target="_blank"><?php echo $new_row['title']; ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
                </tbody>
            </table>
        </div>
    </fieldset>


    <fieldset class="layui-elem-field">
        <legend>空间信息</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <tbody>
                <tr>
                    <th>总空间</th>
                    <td><?php echo $space_info['total_space'] ?></td>
                </tr>

                <tr>
                    <th>已使用空间</th>
                    <td><?php echo $space_info['used_space'] ?> <a style="color: #ff5722;" href="<?php echo url("export_user_data");?>">（一键导出）</a></td>
                </tr>

                <tr>
                    <th>剩余空间</th>
                    <td><?php echo $space_info['remain_sapce'] ?></td>
                </tr>

                <tr>
                    <th>空间到期时间</th>
                    <td><?php echo $space_info['enddate_space'] ?></td>
                </tr>


                <tr>
                    <th>上传图片限制</th>
                    <td><?php echo $space_info['image_upload'] ?></td>
                </tr>

                <tr>
                    <th>上传附件限制</th>
                    <td><?php echo $space_info['file_upload'] ?></td>
                </tr>


                </tbody>
            </table>
        </div>
    </fieldset>


    <fieldset class="layui-elem-field">
        <legend>系统信息</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <tbody>
                <tr>
                    <th>开发版本</th>
                    <td><?php echo $system_info['version']; ?></td>
                </tr>
                <tr>
                    <th>开发语言</th>
                    <td><?php echo $system_info['lang']; ?></td>
                </tr>

                <tr>
                    <th>操作系统</th>
                    <td><?php echo $system_info['system']; ?></td>
                </tr>

                <tr>
                    <th>运行环境</th>
                    <td><?php echo $system_info['server'] ?></td>
                </tr>


                </tbody>
            </table>
        </div>
    </fieldset>


    <fieldset class="layui-elem-field">
        <legend>开发团队</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <tbody>
                <tr>
                    <th>版权所有</th>
                    <td><?php echo $system_info['support'] ?><a href="<?php echo $system_info['support_url'] ?>"
                                                                class='x-a' target="_blank">访问官网</a></td>
                </tr>
                <tr>
                    <th>技术支持、问题反馈</th>
                    <td><?php echo $system_info['developer']; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>

</div>
<script>
    function export_web_log() {

    }
</script>
</body>


</html>