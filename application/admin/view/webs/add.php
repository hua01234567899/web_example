{extend name="common:opt_layout"}


{block name="header_script"}
<!--引入后台配置文件-->
<script src="__CDN__/lib/xqy_ueditor/xqy_ueditor.admin.config.js" charset="utf-8"></script>
<script src="__CDN__/lib/xqy_ueditor/ueditor.all.js" charset="utf-8"></script>
{/block}


{block name="content"}


<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">
        分类
    </label>

    <div class="layui-input-block">
        <table class="layui-table">
            <tbody>
			<?php
				foreach ($class_arr  as $class_row){
					$parent_is_selected = " checked";
					if(!empty($class_row['child'])){
						foreach ($class_row['child'] as $child_row){
							$tag_id = $child_row['id'];
							if(!in_array($tag_id,$row['tags'])){
								$parent_is_selected = "";
								break;
							}
						}
					}else{
					    $parent_is_selected = "";
                    }
					?>
                    <tr>
                        <td><input type="checkbox" class="select_all" name="select_all" lay-filter="selectAll" title="一级分类：<?php echo $class_row['title'];?>"  <?php echo $parent_is_selected;?> lay-skin="primary">

                            <div class="tag-class layui-input-block" style="margin-left: 50px;">

								<?php
									foreach ($class_row['child'] as $child_row) {
										$tag_id = $child_row['id'];
										if (in_array($tag_id, $row['tags'])) {
											$is_check = " checked";
										} else {
											$is_check = "";
										}
										echo '<input name="tags[]" lay-filter="selectOne" lay-skin="primary" type="checkbox" title="' . $child_row['title'] . '" value="' . $tag_id . '" ' . $is_check . '/>';
									}
								?>
                            </div>
                        </td>

                    </tr>
					<?php
				}
			?>
            </tbody>
        </table>
    </div>

</div>



<div class="layui-form-item">
    <label for="number_code" class="layui-form-label"> <span class="x-red">*</span>编号 </label>
    <div class="layui-input-block">
        <input type="text" id="number_code" name="number_code" lay-verify="required" lay-reqText="编号不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['number_code']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="prices" class="layui-form-label"> <span class="x-red">*</span>价格 </label>
    <div class="layui-input-block">
        <input type="text" id="prices" name="prices"
               autocomplete="off" class="layui-input" value="<?php echo $row['prices']; ?>">
    </div>
</div>



<div class="layui-form-item">
    <label for="online_view_url" class="layui-form-label"> <span class="x-red"></span>在线预览地址 </label>
    <div class="layui-input-block">
        <input type="text" id="online_view_url" name="online_view_url"
               autocomplete="off" class="layui-input" value="<?php echo $row['online_view_url']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="title" class="layui-form-label"> <span class="x-red">*</span>标题 </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>

<div class="layui-form-item" style="display: none;">
    <label for="subtitle" class="layui-form-label"> <span class="x-red">*</span>副标题 </label>
    <div class="layui-input-block">
        <input type="text" id="subtitle" name="subtitle"
               autocomplete="off" class="layui-input" value="<?php echo $row['subtitle']; ?>">
    </div>
</div>


<?php
    upload_style("image_style", [
        "title" => "封面图",
        "images" => $row['image_ids'],
        "field" => "image_ids",
        "num" => 1
    ]);
?>




<?php
    upload_style("image_style", [
        "title" => "详情页面列表图",
        "images" => $row['detail_image_ids'],
        "field" => "detail_image_ids",
        "num" => 20
    ]);
?>


<div class="layui-form-item layui-form-text">
    <label class="layui-form-label" for="description">产品描述</label>
    <div class="layui-input-block">
        <textarea name="description" id="description" class="layui-textarea"><?php echo $row['description'] ?></textarea>
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label" for="content">产品详情</label>
    <div class="layui-input-block">
        <textarea name="content" id="content" class="layui-textarea"><?php echo $row['content'] ?></textarea>
    </div>
</div>


<?php
    upload_style("image_style", [
        "title" => "产品详情列表图",
        "images" => $row['product_detail_image_ids'],
        "field" => "product_detail_image_ids",
        "num" => 20
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


    layui.use(['form', 'layer'], function () {
        $ = layui.jquery;
        var form = layui.form
            , layer = layui.layer;
        form.on('checkbox(selectAll)', function (data) {
            var elem = data.elem;
            var is_checked = elem.checked;
            var child = $(elem).closest("tr").find(".tag-class input[type='checkbox']");
            child.each(function (index, item) {
                item.checked = elem.checked;
            });
            form.render('checkbox');
        });

        form.on('checkbox(selectOne)', function (data) {
            var elem = data.elem;
            var is_checked = elem.checked;
            if(is_checked){
                var is_un_checked = true;
                var child = $(elem).closest("tr").find(".tag-class input[type='checkbox']");
                child.each(function (index, item) {
                    var item_checked = item.checked ;
                    if(item_checked==false){
                        is_un_checked = false;
                        return false
                    }
                });
                var child = $(elem).closest("tr").find(".select_all[type='checkbox']");
                child.each(function (index, item) {
                    item.checked = is_un_checked;
                });
            }else{
                var child = $(elem).closest("tr").find(".select_all[type='checkbox']");
                child.each(function (index, item) {
                    item.checked = elem.checked;
                });
            }
            form.render('checkbox');
        });



    });

</script>
{/block} 