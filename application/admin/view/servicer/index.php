{extend name="common:list_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->

{/block}

{block name='footer_script'}

<script type="text/html" id="wechat_imgTpl">
    <img src="{{d.wechat_images_ids}}">
</script>


<script type="text/html" id="icon_images_ids">
    <img src="{{d.icon_images_ids}}">
</script>


<script>


    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'title', title: '<?php  echo_editable_icon('名称');?>', edit: 'text'},
            {field: 'icon_images_ids', title: '客服图标', templet:"#icon_images_ids"},
            {field: 'qq', title: '<?php  echo_editable_icon('qq');?>', edit: 'text'},
            {field: 'wechat_images_ids', title: '微信号', templet:"#wechat_imgTpl",class:"list-img"},
            {field: 'orders', title: '<?php  echo_editable_icon('排序');?>', edit: 'text'},
            {field: 'add_time', title: '发布时间'},
            {field: 'status', title: '状态', templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>


{/block}