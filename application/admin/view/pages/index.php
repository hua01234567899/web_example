{extend name="common:sigle"}
{block name="header_script"}
<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>
{/block}

{block name='content'}

<div class="layui-form-item">
    <label for="status" class="layui-form-label">
        <span class="x-red">*</span>内容
    </label>
    <div class="layui-input-block editor_page" >
		<?php echo_editor("content", $row) ?>
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


<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="post_opt">确认修改</button>
        <button type="button" onclick="location.reload();" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>


{/block}





