{extend name="common:class_opt_layout"}

{block name="header_script"}
<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>

{/block}

{block name="content"}

<div class="layui-form-item">
    <label for="username" class="layui-form-label"> <span class="x-red">*</span>标题 </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>


<?php
	upload_style("image_style", [
		"title" => "封面图",
		"images" => $row['image_ids'],
		"field" => "image_ids"
	]);
?>

<div class="layui-form-item">
    <label for="status" class="layui-form-label"> <span class="x-red">*</span>状态 </label>
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

<input name="parent_id" id="parent_id" class="layui-input" type="hidden" value="<?php echo $row['parent_id']; ?>"/>
{/block}
