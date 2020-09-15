{extend name="common:sigle"}


{block name='content'}


<div class="layui-form-item">
    <label for="status" class="layui-form-label"> <span class="x-red">*</span>调试开关 </label>
    <div class="layui-input-inline">
		<?php
			if ($row['app_debug'] != 0) {
				echo '<input lay-filter="switch_status" type="checkbox" checked="" id="app_debug" name="app_debug" lay-skin="switch"
               lay-text="启用|停用" class="layui-input">';
			} else {
				echo '<input lay-filter="switch_status" type="checkbox" id="app_debug" name="app_debug" lay-skin="switch" lay-text="启用|停用"
               class="layui-input">';
			}
		?>
    </div>
    <div class="layui-form-mid layui-word-aux">生产环境下一定要关闭</div>
</div>



<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="post_opt">确认修改</button>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>

{/block}

{block name="form_script"}
<script>
    var switch_arr = ["app_debug"];
</script>
{/block}






