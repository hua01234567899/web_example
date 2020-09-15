{extend name="common:sigle"}

{block name='content'}
<div class="layui-form-item">
    <label class="layui-form-label">新密码</label>
    <div class="layui-input-inline">
        <input type="password" name="password" lay-verify="required"
               autocomplete="off" id="LAY_password" class="layui-input">
    </div>
    <div class="layui-form-mid layui-word-aux">6到30个字符，字符为字母或数字的组合</div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">确认新密码</label>
    <div class="layui-input-inline">
        <input type="password" name="repassword" lay-verify="required"
               autocomplete="off" class="layui-input">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="setmypass">确认修改</button>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>
{/block}

{block name='footer_script'}
<script>
    layui.use(['form'], function () {
        var form = layui.form,
            layer = layui.layer;
        form.on('submit(setmypass)', function (data) {
            data = data.field;
            post_data({
                data: data,
                callBack: function (res) {
                    location.reload();
                }
            });
        });
    });
</script>
{/block}
