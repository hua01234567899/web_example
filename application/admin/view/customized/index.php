{extend name="common:list_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->

{/block}

{block name="tips"}
<blockquote class="layui-elem-quote">
    <p>说明内容：列表按照显示提交时间降序显示，最近提交的显示在前</p>
</blockquote>
{/block}

{block name="table_toolbal_script"}
<script type="text/html" id="table_toolbar">

    <div>
        <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
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


{block name='footer_script'}


<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'type', title: '定制类型'},
            {field: 'type', title: '公司名称'},
            {field: 'name', title: '姓名'},
            {field: 'mobile', title: '手机号'},
            {field: 'remark', title: '<?php echo_editable_icon("系统备注");?>', edit: 'text'},
            {field: 'add_time', title: '提交时间'},
            {field: 'ip', title: 'IP地址'},
            {field: 'status', title: '状态', templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>


{/block}