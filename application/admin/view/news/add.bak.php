{extend name="common:opt_layout"}


{block name="header_script"}

<style>
    .layui-upload-img {
        width: 92px;
        height: 92px;
        margin: 0 10px 10px 0;
</style>

<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>
{/block}

{block name="content"}

<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        分类
    </label>
    <div class="layui-input-inline">
    </div>
</div>

<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        <span class="x-red">*</span>标题
    </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>


<?php
	upload_style("image_style", [
		"title" => "缩略图",
		"images" => $row['image_ids'],
		"field" => "image_ids",
	]);
?>

<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        内容
    </label>
    <div class="layui-input-block">
		<?php echo_editor("content", $row) ?>
        <div style="margin-top:5px;">
            <button type="button" class="layui-btn layui-btn-xs"><i class="layui-icon"></i> 检查内容是否有违禁词</button>
            <button type="button" class="layui-btn layui-btn-xs"><i class="layui-icon"></i> 提取关键字和描述</button>
            <button type="button" class="layui-btn layui-btn-xs" title="将提取内容第一张图作为缩略图"><i class="layui-icon"></i>
                提取缩略图
            </button>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <label for="seo_title" class="layui-form-label">
        SEO标题
    </label>
    <div class="layui-input-inline">
        <input type="text" id="seo_title" name="seo_title"
               autocomplete="off" class="layui-input" value="<?php echo $row['seo_title']; ?>" placeholder="为空时使用文档标题">
    </div>
</div>

<div class="layui-form-item">
    <label for="seo_keywords" class="layui-form-label">
        SEO关键字
    </label>
    <div class="layui-input-block">
        <input type="text" id="seo_keywords" name="seo_keywords" placeholder="少于60个字，每个关键词之间用英文半角状态的逗号“,”隔开"
               autocomplete="off" class="layui-input" value="<?php echo $row['seo_keywords']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="seo_description" class="layui-form-label">
        SEO描述
    </label>
    <div class="layui-input-block">
        <textarea name="seo_description" class="layui-textarea"
                  placeholder="少于200个字"><?php echo $row['seo_description'] ?></textarea>
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
    <label for="add_time" class="layui-form-label">
        发布时间
    </label>
    <div class="layui-input-inline">
        <input type="text" class="layui-input" id="add_time" name="add_time">
    </div>
</div>

<div class="layui-form-item">
    <label for="status" class="layui-form-label">
        <span class="x-red">*</span>状态
    </label>

    <div class="layui-input-inline">
		<?php
			if ($row['status'] != 1) {
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
            , value: '<?php echo get_date($row['add_time']);?>'
        });
    });
</script>

{/block}

