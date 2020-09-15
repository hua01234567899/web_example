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
            {field: 'title', title: '<?php  echo_editable_icon('标题');?>', edit: 'text'},
            {field: 'images', title: '图片', templet:"#imgTpl"},
            {field: 'type', title: '类型', edit: 'text'},
            {field: 'add_time', title: '发布时间'},
            {field: 'status', title: '状态', templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>


{/block}