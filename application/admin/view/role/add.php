{extend name="common:opt_layout"}

{block name="content"}

<div class="layui-form-item">
    <label for="name" class="layui-form-label">
        <span class="x-red">*</span>角色名
    </label>
    <div class="layui-input-inline">
        <input type="text" id="name" name="name" lay-verify="required" lay-reqText="角色名不能为空"
               autocomplete="off" class="layui-input" value="{$row['name']}">
    </div>
</div>

<div class="layui-form-item">
    <label for="name" class="layui-form-label">
        <span class="x-red">*</span>权限分配
    </label>
    <div class="layui-input-block">
        <div id="privilege_tree" class="demo-tree"></div>
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label for="remark" class="layui-form-label">
        描述
    </label>
    <div class="layui-input-block">
        <textarea placeholder="请输入角色描述" id="remark" name="remark" class="layui-textarea">{$row['remark']}</textarea>
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

{block name="footer_script"}
<script>
    layui.use(['form', 'layer', 'tree', 'util'], function () {
        $ = layui.jquery;
        var form = layui.form
            , layer = layui.layer;
        var tree = layui.tree
            , layer = layui.layer
            , util = layui.util;
        var privilege_tree_data = eval(<?php echo json_encode($privilege_list);?>);

        tree.render({
            elem: '#privilege_tree'
            , id: 'privilege_tree'
            , data: privilege_tree_data
            , showCheckbox: true
        });

        if (typeof default_verify == 'undefined') {
            var default_verify = {
                orders: function (value) {
                    var reg = /^[0-9]+.?[0-9]*$/;
                    if (reg.test(value) == false) {
                        return "序号只能为整数";
                    }
                }
            }
        }
        if (typeof verify == 'undefined') {
            verify = default_verify;
        } else {
            verify = Object.assign(default_verify, verify);
        }
        form.verify(verify);
        //监听表单提交
        form.on('submit(post_opt)', function (data) {
            var privilege_data = tree.getChecked('privilege_tree'); //获取选中节点的数据
            var _post_data = data.field;
            if (typeof _post_data.status == "undefined") {
                _post_data.status = 0;
            } else {
                _post_data.status = 1;
            }
            _post_data.privilege_data = privilege_data;
            for(let _key  in _post_data){
                if(_key.indexOf("layuiTreeCheck")!=-1){
                    delete _post_data[_key];
                }
            }
            if (typeof s_callBack != 'undefined' && typeof s_callBack == "function") {
                var callBack = s_callBack;
            } else {
                var callBack = function (res) {
                    var index = parent.layer.getFrameIndex(window.name);
                    //关闭当前frame
                    parent.layer.close(index);
                    parent.location.reload();
                }
            }
            post_data({
                data: _post_data,
                callBack: callBack
            });
        });


    });
</script>
{/block}

