{extend name="common:list_layout"}

{block name="header_script"}{/block}

{block name='footer_script'}


<script type="text/html" id="hotTpl">
    <input type="checkbox" name="hot" data-field="hot" data-id="{{d.id}}" value="{{d.hot}}"
           lay-filter="switch_status"
           lay-skin="switch"
           lay-text="是|否" {{
           d.hot!=0 ? 'checked': ''}} >
</script>


<script type="text/html" id="scan_imgTpl">
    <img src="{{d.scan_image_ids}}">
</script>

<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'tags', title: '类别'},
            {field: 'title', title: '<?php  echo_editable_icon('标题');?>', edit: 'text'},
            {field: 'images', title: '图片', templet:"#imgTpl"},
            {field: 'scan_image_ids', title: '小程序码', templet:"#scan_imgTpl"},
            {field: 'orders', title: '<?php  echo_editable_icon('排序');?>', edit: 'text'},
            {field: 'add_time', title: '发布时间'},
            {field: 'status', title: '状态', templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>
{/block}