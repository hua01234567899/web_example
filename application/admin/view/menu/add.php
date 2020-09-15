{extend name="common:opt_layout"}

{block name="content"}
<div class="layui-form-item">
    <label class="layui-form-label" for="parent_id"><span class="x-red">*</span>父级</label>
    <div class="layui-input-inline">
        <select name="parent_id" id="parent_id">
            <option value="0">无</option>
			<?php
				foreach ($first_menu_list as $menu_item) {
					if ($menu_item['id'] != $row['parent_id']) {
						$is_selected = "";
					} else {
						$is_selected = 'selected';
					}
					echo '<option value="' . $menu_item['id'] . '" ' . $is_selected . '>' . $menu_item['title'] . '</option>';
				}
			?>
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="title" class="layui-form-label">
        <span class="x-red">*</span>标题
    </label>
    <div class="layui-input-inline">
        <input type="text" id="title" name="title" lay-verify="required" lay-reqText="标题不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['title']; ?>">
    </div>
</div>

<div class="layui-form-item">
    <label for="controller" class="layui-form-label">
        <span class="x-red">*</span>控制器名称
    </label>
    <div class="layui-input-inline">
        <input type="text" id="controller" name="controller" lay-verify="required" lay-reqText="控制器名称不能为空"
               autocomplete="off" class="layui-input" value="<?php echo $row['controller']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="status" class="layui-form-label">
        是否多图
    </label>

    <div class="layui-input-inline">
		<?php
			if ($row['is_many_photo'] != 0) {
				echo '<input lay-filter="switch_status" type="checkbox" checked="" id="is_many_photo" name="is_many_photo" lay-skin="switch"
               lay-text="是|否" class="layui-input">';
			} else {
				echo '<input lay-filter="switch_status" type="checkbox" id="is_many_photo" name="is_many_photo" lay-skin="switch" lay-text="是|否"
               class="layui-input">';
			}
		?>
    </div>
</div>


<div class="layui-form-item">
    <label for="controller" class="layui-form-label">
        图片缩略图尺寸
    </label>
    <div class="layui-inline">
        <label class="layui-form-label" id="thumb_width" style="padding-left: 0px;text-align: left;">宽度</label>
        <div class="layui-input-inline">
            <input type="text" name="thumb_width" id="thumb_width" autocomplete="off" class="layui-input"
                   value="<?php echo $row['thumb_width']; ?>"/>
        </div>
    </div>
    <div class="layui-inline">
        <label class="layui-form-label" for="thumb_height" style="padding-left: 0px;text-align: left;">高度</label>
        <div class="layui-input-inline">
            <input type="text" name="thumb_height" id="thumb_height" autocomplete="off" class="layui-input"
                   value="<?php echo $row['thumb_height']; ?>">
        </div>
    </div>
</div>


<div class="layui-form-item">
    <label for="is_only_manager" class="layui-form-label">
        水印栏目
    </label>
    <div class="layui-input-inline">
		<?php
			if ($row['is_add_water'] != 0) {
				echo '<input lay-filter="switch_status" type="checkbox" checked="" id="is_add_water" name="is_add_water" lay-skin="switch"
               lay-text="是|否" class="layui-input">';
			} else {
				echo '<input lay-filter="switch_status" type="checkbox" id="is_add_water" name="is_add_water" lay-skin="switch" lay-text="是|否"
               class="layui-input">';
			}
		?>
    </div>
</div>


<div class="layui-form-item">
    <label for="icon" class="layui-form-label">
        <span class="x-red">*</span>图标
    </label>
    <div class="layui-input-block">
        <div style="
    margin: 0px 0px 5px;"><a href="http://www.jq22.com/yanshi21263" target="_blank"><spn style="color:red">(注:所有图标出处 http://www.jq22.com/yanshi21263 基本元素=》图标字体,请拷贝图标字体过来)</spn></a> </div>

        <input type="text" id="icon" name="icon" lay-verify="required" lay-reqText="图标不能为空"
               autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['icon']); ?>">


    </div>
</div>


<div class="layui-form-item">
    <label for="is_only_manager" class="layui-form-label">
        <span class="x-red">*</span>分类列表
    </label>
    <div class="layui-input-inline">
		<?php
			if ($row['is_show_class'] == 1) {
				$is_true_checked = "checked";
				$is_false_checked = "";
			} else {
				$is_true_checked = "";
				$is_false_checked = "checked";
			}
		?>
        <input type="radio" name="is_show_class" id="is_show_class" value="1"
               title="有" <?php echo $is_true_checked; ?>/>
        <input type="radio" name="is_show_class" value="0" title="无" <?php echo $is_false_checked; ?>/>
    </div>

    <div class="layui-form-mid">分类层数</div>
    <div class="layui-input-inline">
        <input type="text" id="class_deep" name="class_deep" placeholder="分类层数"
               class="layui-input" value="<?php echo $row['class_deep']; ?>">
    </div>
</div>


<div class="layui-form-item">
    <label for="rich_tags" class="layui-form-label">
        富文本css外层标签
    </label>
    <div class="layui-input-inline">
        <input type="text" id="rich_tags" name="rich_tags"
               autocomplete="off" class="layui-input" value="<?php echo $row['rich_tags']; ?>">
    </div>
</div>


<div class="layui-form-item layui-form-text">
    <label for="rich_css_link_url" class="layui-form-label">富文本css链接</label>
    <div class="layui-input-block">
        <textarea name="rich_css_link_url" class="layui-textarea"
                  placeholder="一个链接一个换行"><?php echo $row['rich_css_link_url'] ?></textarea>
    </div>
</div>


<div class="layui-form-item">
    <label for="is_only_manager" class="layui-form-label">
        <span class="x-red">*</span>仅超级管理员可见
    </label>
    <div class="layui-input-inline">
		<?php
			if ($row['is_only_manager'] != 0) {
				echo '<input lay-filter="switch_status" type="checkbox" checked="" id="is_only_manager" name="is_only_manager" lay-skin="switch"
               lay-text="是|否" class="layui-input">';
			} else {
				echo '<input lay-filter="switch_status" type="checkbox" id="is_only_manager" name="is_only_manager" lay-skin="switch" lay-text="是|否"
               class="layui-input">';
			}
		?>
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
{/block}


{block name="form_script"}
<script>
    var switch_arr = ["is_many_photo", "is_only_manager", "is_add_water"];
</script>
{/block}




