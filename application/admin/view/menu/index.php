{extend name="common:list_layout"}

{block name="tips"}
<blockquote class="layui-elem-quote">
    <p>说明内容：网站前台先按照排序大小，再按照添加时间进行先后显示，序号越小，显示靠前，序号一样情况下，添加时间越早，显示靠前 </p>
</blockquote>
{/block}
{block name='footer_script'}

<script type="text/html" id="iconTpl">
    <i class="iconfont">{{d.icon}}</i>
</script>


<script>
    //数据表格配置
    var tableConfig = {
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80, sort: false},
            {
                field: 'title', title: '标题', sort: false, templet: function (d) {
                    if (d.parent_id == 0) return d.title; else return '&nbsp;├ ' + d.title;
                }
            },
            {field: 'icon', title: '图标', width: 100, sort: false, templet: '#iconTpl'},
            {field: 'controller', title: '控制器', sort: false},
            {field: 'orders', title: '<?php echo_editable_icon("序号");?>', width: 100, edit: 'text', sort: false},
            {field: 'status', title: '状态', width: 100, sort: false, templet: '#statusTpl'},
            {title: '操作', templet: "#optTpl"}
        ]]
    }
</script>
{/block}