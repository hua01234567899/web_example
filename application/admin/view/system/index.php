{extend name="common:sigle"}


{block name='content'}

<div class="layui-form-item">
    <label class="layui-form-label">网站名称</label>
    <div class="layui-input-block">
        <input type="text" name="title" value="<?php echo $row['title']; ?>" class="layui-input"
               lay-verify="required">
    </div>
</div>

<?php
	upload_style("image_style", [
		"title" => "网站LOGO",
		"images" => $row['logo_id'],
		"field" => "logo_id",
	]);
?>

<div class="layui-form-item">
    <label class="layui-form-label">备案号</label>
    <div class="layui-input-block">
        <input type="text" name="beian" value="<?php echo $row['beian']; ?>" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">公安备案号</label>
    <div class="layui-input-block">
        <input type="text" name="ga_beian" value="<?php echo $row['ga_beian']; ?>" class="layui-input">
    </div>
</div>



<div class="layui-form-item">
    <label class="layui-form-label">服务热线</label>
    <div class="layui-input-block">
        <input type="text" name="service_hotline" value="<?php echo $row['service_hotline']; ?>" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">SEO标题</label>
    <div class="layui-input-block">
        <input type="text" name="seo_title" value="<?php echo $row['seo_title']; ?>" class="layui-input"
               lay-verify="required">
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">SEO关键字</label>
    <div class="layui-input-block">
        <textarea name="keywords" lay-verify="required" class="layui-textarea"
                  placeholder="多个关键词用英文状态,号分割"><?php echo $row['keywords']; ?></textarea>
    </div>
</div>





<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">SEO描述</label>
    <div class="layui-input-block">
        <textarea name="description" class="layui-textarea"><?php echo $row['description'] ?></textarea>
    </div>
</div>



<div class="layui-form-item layui-form-text">
    <label class="layui-form-label" for="tongji_code">百度统计代码</label>
    <div class="layui-input-block">
        <textarea name="tongji_code" id="tongji_code" class="layui-textarea"><?php echo $row['tongji_code'] ?></textarea>
    </div>
</div>


<div class="layui-form-item layui-form-text">
    <label class="layui-form-label" for="shangqiao_code">百度商桥代码</label>
    <div class="layui-input-block">
        <textarea name="shangqiao_code" id="shangqiao_code" class="layui-textarea"><?php echo $row['shangqiao_code'] ?></textarea>
    </div>
</div>




<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="post_opt">确认修改</button>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>


{/block}




