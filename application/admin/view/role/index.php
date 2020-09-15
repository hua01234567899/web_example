{extend name="common:list_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->
<style>

    .layui-table .layui-badge-rim {
        display: inline-block;
       height: auto;
        margin: 5px;
    }
    .layui-table .laytable-cell-1-0-3{
        line-height: 2.5;
    }
    .layui-table-cell {
        font-size: 14px;
        padding: 0 5px;
        height: auto;
        overflow: visible;
        text-overflow: inherit;
        white-space: normal;
        word-break: break-all;
    }
</style>
{/block}

{block name="tips"}
<blockquote class="layui-elem-quote">
    <p>说明内容：1个角色可以拥有多项权限 </p>
</blockquote>
{/block}
{block name='footer_script'}

<script type="text/html" id="roleTpl">
    <ul>
        {{# layui.each(d.privilege, function(index, item){ }}
        <li style="display:inline-block">

            {{#  if(item.parent_id>0){ }}
            <span class="layui-badge-rim">{{item.parent_title}}-{{item.title}}:{{item.txt}}</span>
            {{#  } else { }}
            <span class="layui-badge-rim">{{item.title}}:{{item.txt}}</span>
            {{#  } }}
            
        </li>
        {{# }); }}
    </ul>

</script>


<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'name', title: '<?php echo_editable_icon("角色名称");?>', edit: 'text'},
            {field: 'privilege', title: '拥有权限规则', templet: '#roleTpl'},
            {field: 'remark', title: '<?php echo_editable_icon("描述");?>', edit: 'text'},
            {field: 'orders', title: '<?php echo_editable_icon("序号");?>', width: 100, edit: 'text'},
            {field: 'status', title: '状态', width: 100, templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>

{/block}