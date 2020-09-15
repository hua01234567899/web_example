{extend name="common:list_no_nav_layout"}

{block name="header_script"}  <!--引入头部的style 代码  script代码-->

{/block}





{block name='footer_script'}

<script>
    //数据表格配置
    var tableConfig = {
        url:<?php echo url("access",['user_id'=>$user_id])?>
        cols: [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width: 80},
            {field: 'title', title: '用户名'},
            {field: 'login_time', title: '登录时间'},
            {field: 'login_ip', title: '登录IP'}
        ]]
    }

</script>


{/block}