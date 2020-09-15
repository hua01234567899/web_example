{extend name="common:sigle"}

{block name='content'}
<div class="layui-form-item">
    <label class="layui-form-label">我的账号</label>
    <div class="layui-form-mid layui-word-aux"><?php echo $admin_info['username'] ?></div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">昵称</label>
    <div class="layui-input-inline">
        <input type="text" name="nickname" value="<?php echo $admin_info['nickname'] ?>"
               lay-verify="required" autocomplete="off"
               placeholder="请输入昵称" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">邮箱</label>
    <div class="layui-input-inline">
        <input type="text" name="email" value="<?php echo $admin_info['email']; ?>"
               autocomplete="off"
               class="layui-input">
    </div>
    <div class="layui-form-mid layui-word-aux">可用于找回密码</div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-block">
                                    <textarea name="remark" placeholder=""
                                              class="layui-textarea"><?php echo $admin_info['remark']; ?></textarea>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="setmyinfo">确认修改</button>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>
{/block}

{block name='footer_script'}

<script>
    layui.use(['form'], function () {
        var form = layui.form,
            layer = layui.layer;
        form.on('submit(setmyinfo)', function (data) {
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
