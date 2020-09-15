{extend name="common:opt_layout"}

{block name="content"}
<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        <span class="x-red">*</span>登录账号
    </label>
    <div class="layui-input-inline">
        <input type="text" id="username" name="username" lay-verify="username"
               autocomplete="off" class="layui-input" value="{$row['username']}">
    </div>
    <div class="layui-form-mid layui-word-aux">只能为A-Z，a-z，0-9，下划线组合</div>
</div>


<div class="layui-form-item">
    <label for="password" class="layui-form-label">
        <span class="x-red">*</span>登录密码
    </label>
    <div class="layui-input-inline">
        <input type="password" id="password" name="password" lay-verify="password"
               autocomplete="off" class="layui-input" value="{$row['password']}">
    </div>
    <div class="layui-form-mid layui-word-aux">6-20个字符（只能为A-Z，a-z，0-9，下划线组合）</div>
</div>


<div class="layui-form-item">
    <label for="password2" class="layui-form-label">
        <span class="x-red">*</span>确认密码
    </label>
    <div class="layui-input-inline">
        <input type="password" id="password2" name="password2" lay-verify="password2"
               autocomplete="off" class="layui-input" value="">
    </div>
</div>


<div class="layui-form-item">
    <label for="nickname" class="layui-form-label">
        <span class="x-red">*</span>昵称
    </label>
    <div class="layui-input-inline">
        <input type="text" id="nickname" name="nickname" lay-verify="required" lay-reqText="昵称不能为空"
               autocomplete="off" class="layui-input" value="{$row['nickname']}">
    </div>
</div>


<div class="layui-form-item">
    <label for="email" class="layui-form-label">
        邮箱
    </label>
    <div class="layui-input-inline">
        <input type="text" id="email" name="email"
               autocomplete="off" class="layui-input" value="{$row['email']}" lay-verify="email2">
    </div>
</div>


<div class="layui-form-item" pane="">
    <label class="layui-form-label">
        角色组
    </label>
    <div class="layui-input-block" style="width: 100%;">
        <?php
        foreach ($role_list as $role_item){
            if(in_array($role_item['id'],$row['roles'])){
                $checked = ' checked="" ';
            }else{
                $checked = '';
            }
            echo '<input type="checkbox" name="roles[]" lay-skin="primary" value="'.$role_item['id'].'" title="'.$role_item['name'].'" 
            '.$checked.'>';
        }
        ?>
    </div>
</div>


<div class="layui-form-item">
    <label for="orders" class="layui-form-label">
        <span class="x-red">*</span>序号
    </label>
    <div class="layui-input-inline">
        <input type="text" id="orders" name="orders" lay-verify="orders"
               autocomplete="off" class="layui-input" value="<?php echo $row['orders']; ?>">
    </div>
</div>

<div class="layui-form-item">
    <label for="status" class="layui-form-label">
        <span class="x-red">*</span>状态
    </label>

    <div class="layui-input-inline">
        <?php
            if ($row['status'] != 0) {
                echo '<input lay-filter="switch_status" type="checkbox" checked="" id="status" name="status" lay-skin="switch"
               lay-text="启用|停用" class="layui-input">';
            } else {
                echo '<input lay-filter="switch_status" type="checkbox" id="status" name="status" lay-skin="switch" lay-text="启用|停用"
               class="layui-input">';
            }
        ?>
    </div>
</div>
{/block}


{block name="form_script"}
<script>
    var verify = {
        'username': function (value) {
            if (value == "") {
                return "登录账号不能为空";
            }
            var reg = /^[_0-9a-zA-Z]+$/
            if (reg.test(value) == false) {
                return "账号只能为字母,数字,下划线的组合";
            }
        },
        'password': function (value) {
            if (value == "") {
                return "登录密码不能为空";
            }
            var reg = /^[_0-9a-zA-Z]+$/
            if (reg.test(value) == false) {
                return "密码只能为字母,数字,下划线的组合";
            }
            if (value.length < 6 || value.length > 20) {
                return "密码长度不能低于6位，高于20位";
            }
        },
        'password2': function (value) {
            if (value == "") {
                return "确认密码不能为空";
            }
            var p = $("#password").val();
            if (p != value) {
                return "两次密码输入不一致";
            }
        },

        email2: function (value) {
            if (value != '') {
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                if (reg.test(value) == false) {
                    return "邮箱格式不正确";
                }
            }
        },
    }
</script>
{/block}

