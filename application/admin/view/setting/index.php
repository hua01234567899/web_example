{extend name="common:sigle"}


{block name='content'}

<?php
    upload_style("image_style", [
        "title" => "前台默认头像",
        "images" => $row['default_cover_img_id'],
        "field" => "default_cover_img_id",
    ]);
?>

<?php
    upload_style("image_style", [
        "title" => "用户注册默认头像",
        "images" => $row['user_default_img_id'],
        "field" => "user_default_img_id",
    ]);
?>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit="" lay-filter="post_opt">确认修改</button>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
    </div>
</div>


{/block}




