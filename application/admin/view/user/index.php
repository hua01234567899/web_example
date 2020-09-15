{extend name="common:list_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->

{/block}


{block name="tips"}
<blockquote class="layui-elem-quote">
    <p>说明内容：每个用户可以拥有多个角色,不同的角色拥有不同的访问权限 </p>
</blockquote>
{/block}
{block name='footer_script'}

<script type="text/html" id="roleTpl">
    {{# layui.each(d.roles, function(index, item){ }}
    {{#  if(index==(d.roles.length-1)){ }}
    <span style="display: inline-block;">{{item.role_name}}</span>
    {{#  } else { }}
    <span style="display: inline-block;">{{item.role_name}}|</span>
    {{#  } }}
    {{# }); }}
</script>

<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'username', title: '登录账号'},
            {field: 'nickname', title: '<?php echo_editable_icon("昵称");?>', edit: 'text'},
            {field: 'email', title: '<?php echo_editable_icon("邮箱");?>', edit: 'text'},
            {field: 'roles', title: '角色组', templet: '#roleTpl'},
            {field: 'last_login_time', title: '最后登录'},
            {field: 'orders', title: '<?php echo_editable_icon("序号");?>', edit: 'text'},
            {field: 'status', title: '状态', templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>


{/block}