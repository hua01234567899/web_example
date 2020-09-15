<!doctype html>
<html lang="en">
<head>
    {include file="common:header"/} <!--引入顶部公共模板文件-->
    {block name="header_script"}  <!--引入头部的style 代码  script代码-->
    {/block}
</head>
<body>

<!---->
<div class="x-nav">
      <span class="layui-breadcrumb">
          <?php echo build_nav(); ?>
      </span>
    <a class="layui-btn layui-btn-primary layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"
       href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:38px"></i></a>
</div>

<div class="x-body">
    {block name="tips"}
    <blockquote class="layui-elem-quote">
        <p>说明内容：网站前台先按照排序大小，再按照添加时间进行先后显示，排序数字越小，显示靠前，排序一样情况下，添加时间越晚，显示靠前 </p>
    </blockquote>
    {/block}
    {block name="content"}

    <?php
        echo $left_content_nav;
    ?>
    {/block}

</div>


<!--引入底部的js开始-->
{block name="footer_script"} {/block}
<!--引入底部的js结束-->

<!--引体基本的js代码开始-->
<!--一些常用事件的定义，开始-->

{block name="table_toolbal_script"}
<script type="text/html" id="table_toolbar">

    <div>
        <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
        <button class="layui-btn"
                onclick="x_admin_show('新增<?php echo get_menu_title(MENU_ID); ?>','<?php echo url("add", ["m" => MENU_ID,"cid"=>CURRENT_CLASS_ID]); ?>')">
            <i class="layui-icon"></i>添加
        </button>
    </div>
    <div class="table_search" style="position:absolute;right: 125px;top: 10px;">
        <div class="layui-inline"><input style="width: 200px;" type="text" name="search"
                                         placeholder="<?php echo $search_tips; ?>"
                                         value="<?php ?>" autocomplete="off"
                                         class="layui-input">
        </div>
    </div>
</script>
{/block}

{block name="optTpl"}
<script type="text/html" id="optTpl">

    <button title="编辑" class="layui-btn layui-btn-sm layui-btn-normal" href="javascript:;"
            onclick="x_admin_show('修改<?php echo get_menu_title(MENU_ID); ?>','<?php echo url("edit", ["m" => MENU_ID]); ?>'+'?id='+'{{d.id}}')">
        编辑
    </button>

    <button title="删除" class="layui-btn layui-btn-sm layui-btn-danger" href="javascript:;"
            onclick="delOne(this,'{{d.id}}')">
        删除
    </button>

</script>

{/block}

{block name="statusTpl"}

<script type="text/html" id="statusTpl">
    <input type="checkbox" name="status" data-field="status" data-id="{{d.id}}" value="{{d.status}}"
           lay-filter="switch_status"
           lay-skin="switch"
           lay-text="启用|停用" {{
           d.status!=0 ? 'checked': ''}} >
</script>

{/block}

{block name="imgTpl"}
<script type="text/html" id="imgTpl">
    <img src="{{d.images}}">
</script>

{/block}

<script>

    function delOne(obj, id) {
        layui.use(['table', 'form', 'jquery'], function () {
            layer.confirm('确认要删除吗？', function (index) {
                var m = '<?php echo get_menu_id();?>';
                post_data({
                    url: '<?php echo url("delete");?>',
                    data: {m: m, ids: id},
                    callBack: function () {
                        $(obj).parents("tr").remove();
                    },
                });
            });
        });
    }


    function delAll() {
        var arr = [];
        layui.use(['table'], function () {
            var checkStatus = layui.table.checkStatus('xqy_datatable'), data = checkStatus.data;
            for (var i = 0; i < data.length; i++) {    //循环筛选出id
                arr.push(data[i].id);
            }
            if (arr.length == 0) {
                layer.msg("请至少选中一条记录", {"time": 800, "icon": 2});
                return true;
            } else {
                layer.confirm('确认要删除吗？', function (index) {
                    var ids = arr.toString();
                    var m = '<?php echo get_menu_id();?>';
                    post_data({
                        data: {m: m, ids: ids},
                        url: '<?php echo url("delete");?>',
                        callBack: function (res) {
                            $(".layui-form-checked").not('.header').parents('tr').remove();
                        }
                    });
                });
            }
        });
    }
</script>
<!--一些常用事件的定义结束-->
<!--datatable 初始化-->
{block name="init_datatable"}
<script>


    var search_value = '';
    var menu_id = '<?php echo get_menu_id();?>';
    var class_id = '<?php echo get_class_id();?>';
    var default_table_config = {
        elem: "#xqy_datatable",
        url: '<?php echo url("index");?>',
        cols: [[]],
        page: true,
        toolbar: "#table_toolbar",
        limit: 20,
        limits: [20, 50, 100],
        id: 'xqy_datatable',
        done: function (res, curr, count) {
            if (search_value != '') {
                $('[name=search]').val(search_value);
            }
        },
        where: {
            m: menu_id,
            cid: class_id
        }
    };
    if (typeof tableConfig == "undefined") {
        var tableConfig = {}
    }
    tableConfig = Object.assign(default_table_config, tableConfig);
    var default_setText_url = '<?php echo url("setText");?>';
    var default_setStatus_url = '<?php echo url("setStatus");?>';
    var default_editable_check = {
        "orders": function (data) {
            console.log(data);
            var reg = /^[0-9]+.?[0-9]*$/;
            if (!reg.test(data)) {
                return "序号只能为整数";
            } else {
                return true;
            }
        }
    };
    if (typeof editable_check == 'undefined') {
        var editable_check = {};
    }
    if (typeof setText_url == 'undefined') {
        var setText_url = default_setText_url;
    }
    if (typeof setStatus_url == 'undefined') {
        var setStatus_url = default_setStatus_url;
    }
    editable_check = Object.assign(default_editable_check, editable_check);
    var default_editable_callback = function (obj) {
        var strValue = $(this).prev().text();
        var that = $(this);
        var value = obj.value,
            data = obj.data,
            field = obj.field; //得到字段
        if (editable_check.hasOwnProperty(field)) {
            var check_method = editable_check[field];
            var check_result = check_method(value);
            if (check_result !== true) {
                $(this).val(strValue);
                layer.msg(check_result);
                return false;
            }
        }
        post_data({
            async: false,
            url: setText_url,
            data: {m: '<?php echo get_menu_id();?>', id: data.id, field: field, value: value},
            callBack: function (res) {
            },
            ecallBack: function (res) {
                obj.update({'orders': strValue});
            }
        });
        return false;
    }

    var default_setstatus_callback = function (obj) {
        var id = $(obj.elem).data("id");
        var checked = obj.elem.checked; //开关是否开启，true或者false
        var checked2 = !checked;
        var value = (checked ? 1 : 0);
        var swt = $(obj.elem);
        var field = $(obj.elem).data("field");
        post_data({
            async: false,
            url: setStatus_url,
            data: {m: '<?php echo get_menu_id();?>', id: id, field: field, value: value},
            callBack: function (res) {
            },
            ecallBack: function (res) {
                swt.prop('checked', checked2); //修改switch开关
                layui.form.render();//刷新表格
            }
        })
    }
    layui.use(['table', 'form', 'jquery', 'tree', 'util'], function () {
        table = layui.table;
        var $ = layui.$;
        var form = layui.form;
        table.render(tableConfig);
        table.on('edit(xqy_datatable)', default_editable_callback);
        form.on('switch(switch_status)', default_setstatus_callback);
        $("body").on('keydown', '[name="search"]', function (e) {
            if (e.keyCode == 13) {
                search_value = $(this).val();
                search_value = search_value.trim();
                table.reload('xqy_datatable', {page: {curr: 1}, where: {search: search_value, m: menu_id}}, 'data');
            }
        });
    });

</script>
<!--引体基本的js代码结束-->
{/block}
</body>

