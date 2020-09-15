{extend name="common:sigle"}


{block name='content'}


<div class="layui-form-item">
    <label for="is_only_manager" class="layui-form-label">
        <span class="x-red">*</span>是否使用水印
    </label>
    <div class="layui-input-inline">
        <?php
            if ($row['is_water'] != 0) {
                echo '<input lay-filter="switch_status" type="checkbox" checked="" id="is_water" name="is_water" lay-skin="switch"
               lay-text="是|否" class="layui-input">';
            } else {
                echo '<input lay-filter="switch_status" type="checkbox" id="is_water" name="is_water" lay-skin="switch" lay-text="是|否"
               class="layui-input">';
            }
        ?>
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">
        使用栏目
    </label>
    <input type="checkbox" id="select_all" name="select_all" lay-filter="selectAll" title="栏目全选" lay-skin="primary">

    <div class="layui-input-block">
        <table class="layui-table">
            <tbody>
            <tr>
                <td>
                    <div class="layui-input-block menu-box" style="margin-left: 0px;">
                        <?php
                            foreach ($menu_arr as $menu_row) {
                                $menu_id = $menu_row['id'];
                                if (in_array($menu_id, $check_id_arr)) {
                                    $is_check = " checked";
                                } else {
                                    $is_check = "";
                                }
                                echo '<input name="menu[]" lay-skin="primary" type="checkbox" title="' . $menu_row['title'] . '" value="' . $menu_id . '" ' . $is_check . '/>';
                            }
                        ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>


<?php
    upload_style("image_style", [
        "title" => "水印图片",
        "images" => $row['water_id'],
        "field" => "water_id",
    ]);
?>


<div class="layui-form-item">
    <label for="username" class="layui-form-label"> 位置 </label>
    <div class="layui-input-block">
        <table class="layui-table">
            <tbody>
            <?php
                $ii = 3;
                foreach ($position_arr as $position_key => $position_row) {
                    if ($ii % 3 == 0) {
                        echo '<tr>';
                    }
                    if ($position_row['id'] == $row['water_position']) {
                        $is_check = " checked";
                    } else {
                        $is_check = "";
                    }
                    echo '<td><input type="radio" name="water_position" value="' . $position_row['id'] . '" title="' . $position_row['title'] . '" ' . $is_check . '></td>';
                    if ($ii % 3 == 2) {
                        echo '</tr>';
                    }
                    $ii++;
                }
            ?>
            </tbody>
        </table>
    </div>
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
    var switch_arr = ["is_water"];
    var filter_arr = ["status","select_all"];
    layui.use(['form', 'layer'], function () {
        $ = layui.jquery;
        var form = layui.form
            , layer = layui.layer;
        form.on('checkbox(selectAll)', function (data) {
            var is_checked = data.elem.checked;
            var child = $(".menu-box input[type='checkbox']");
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });
</script>
{/block}






