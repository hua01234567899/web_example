{extend name="common:list_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->

{/block}

{block name='footer_script'}

<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'title', title: '操作描述'},
            {field: 'op_result_desc', title: '操作结果'},
            {field: 'op_user_name', title: '操作人'},
            {field: 'add_time', title: '操作时间'},
            {field: 'ip', title: 'IP地址'},
        ]]
    }
</script>


{/block}


{block name="tips"}
<blockquote class="layui-elem-quote">
    <p>说明内容：操作列表按照操作时间进行排序,添加时间越晚，显示靠前 </p>
</blockquote>
{/block}

{block name="table_toolbal_script"}
<script type="text/html" id="table_toolbar">

    <div class="table_search" style="position:absolute;right: 125px;top: 10px;">
        <div class="layui-inline"><input style="width: 200px;" type="text" name="search"
                                         placeholder="<?php echo $search_tips; ?>"
                                         value="<?php ?>" autocomplete="off"
                                         class="layui-input">
        </div>
    </div>
</script>
{/block}
