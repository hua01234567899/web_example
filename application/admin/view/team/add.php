{extend name="common:opt_layout"}


{block name="header_script"}
<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>
{/block}


{block name="content"}


<div class="layui-form-item">
    <label for="title" class="layui-form-label"> <span class="x-red">*</span>标题 </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="title_en" class="layui-form-label"> <span class="x-red">*</span>英文标题 </label>
    <div class="layui-input-inline">
        <input type="text" id="title_en" name="title_en" lay-verify="required" lay-reqText="英文标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title_en']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="position" class="layui-form-label"> <span class="x-red">*</span>职位 </label>
    <div class="layui-input-inline">
        <input type="text" id="position" name="position" lay-verify="required" lay-reqText="职位不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['position']; ?>">
    </div>
</div>




<?php
	upload_style("image_style", [
		"title" => "头像",
		"images" => $row['image_ids'],
		"field" => "image_ids"
	]);
?>


<div class="layui-form-item layui-form-text">
    <label class="layui-form-label" for="content">介绍</label>
    <div class="layui-input-block">
        <textarea name="content" id="content" class="layui-textarea"><?php echo $row['content'] ?></textarea>
    </div>
</div>

<div class="layui-form-item">
    <label for="orders" class="layui-form-label"> <span class="x-red">*</span>序号 </label>
    <div class="layui-input-inline">
        <input type="text" id="orders" name="orders" lay-verify="orders"
               autocomplete="off" class="layui-input" value="<?php echo $row['orders']; ?>">
    </div>
</div>
<div class="layui-form-item">
    <label for="add_time" class="layui-form-label"> 发布时间 </label>
    <div class="layui-input-inline">
        <input type="text" class="layui-input" id="add_time" name="add_time">
    </div>
</div>
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
{/block}

{block name="form_script"}
<script>
    layui.use('laydate', function () {
        var laydate = layui.laydate;
        //日期时间选择器
        laydate.render({
            elem: '#add_time'
            , type: 'datetime'
            ,zIndex: 99999999
            ,trigger: 'click'
            , value: '<?php echo get_date($row['add_time']);?>'
        });
    });

</script>
{/block} 