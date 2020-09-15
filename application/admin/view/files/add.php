{extend name="common:opt_layout"}


{block name="header_script"}
<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>
{/block}


{block name="content"}
<?php
    $is_show_class = is_menu_class();
    if ($is_show_class) {
        ?>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>所属分类
            </label>
            <div class="layui-form-mid layui-word-aux" style="color: #000!important;">{$row['class_name']}</div>
            <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>"/>
        </div>
    <?php } ?>


<div class="layui-form-item">
    <label for="username" class="layui-form-label"> <span class="x-red">*</span>标题 </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="is_only_manager" class="layui-form-label">
        文件类型
    </label>
    <div class="layui-input-inline">
        <?php
            if ($row['type'] == 0) {
                $is_true_checked = "checked";
                $is_false_checked = "";
            } else {
                $is_true_checked = "";
                $is_false_checked = "checked";
            }
        ?>
        <input type="radio" name="type" value="0"
               title="图片" <?php echo $is_true_checked; ?>/>
        <input type="radio" name="type" value="1" title="ICON" <?php echo $is_false_checked; ?>/>
    </div>
</div>

<?php
    upload_style("image_style", [
        "title" => "封面图",
        "images" => $row['image_ids'],
        "field" => "image_ids"
    ]);
?>


<?php
    upload_style("file_style", [
        "title" => "文件",
        "images" => $row['file_id'],
        "field" => "file_id"
    ]);
?>

<?php
    upload_style("file_style", [
        "title" => "PSD文件",
        "images" => $row['psd_id'],
        "field" => "psd_id"
    ]);
?>


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