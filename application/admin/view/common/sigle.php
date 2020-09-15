<!doctype html>
<html lang="en">
<head>
    {include file="common:header"/} <!--引入顶部公共模板文件-->
    {block name="header_script"}  <!--引入头部的style 代码 secret代码-->
    {/block}
</head>
<body>

<div class="x-nav">
      <span class="layui-breadcrumb">
          <?php echo build_nav(); ?>
      </span>
    <a class="layui-btn layui-btn-primary layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"
       href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:38px"></i></a>
</div>


<!--<div class="layui-fluid"> -->
    <div class="layui-row">
        <!--<div class="layui-col-md12"> -->
            <div class="layui-card">
                <div class="layui-card-body" pad20="">
                    <form class="layui-form">
                        <div class="layui-form" lay-filter="">
                            {block name="content"}
                            {/block}
                        </div>
                    </form>
                </div>
            </div>
        <!--</div> -->
    </div>
<!--</div> -->

{block name="form_script"}
{/block}
{block name='footer_script'}
<script>
    layui.use(['form', 'layer'], function () {
        $ = layui.jquery;
        var form = layui.form
            , layer = layui.layer;
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
            var _post_data = data.field;
            if (typeof _post_data.status == "undefined") {
                _post_data.status = 0;
            } else {
                _post_data.status = 1;
            }
            if (typeof switch_arr != 'undefined' && switch_arr.length > 0) {
                for (let jj = 0; jj < switch_arr.length; jj++) {
                    var db_field = switch_arr[jj];
                    if (typeof _post_data[db_field] == 'undefined') {
                        _post_data[db_field] = 0;
                    } else {
                        _post_data[db_field] = 1;
                    }
                }
            }
            if(typeof filter_arr != 'undefined' && filter_arr.length > 0){
                for (let kk = 0; kk < filter_arr.length; kk++) {
                    var db_field = filter_arr[kk];
                    if (typeof _post_data[db_field] != 'undefined') {
                        delete _post_data[db_field];
                    }
                }
            }
            for (let _key  in _post_data) {
                if (_key == 'file') {
                    delete _post_data[_key];
                }
                if(_key=='editorValue'){
                    delete _post_data[_key];
                }
            }
            if(typeof editor_list!='undefined'){
                for (var editor_field in editor_list) {
                    var editor_content = editor_list[editor_field].getContent();
                    _post_data[editor_field] = editor_content;
                }
            }
            if (typeof s_callBack != 'undefined' && typeof s_callBack == "function") {
                var callBack = s_callBack;
            } else {
                var callBack = function (res) {
                    location.reload();
                }
            }
            console.log(_post_data);
            post_data({
                url: '<?php echo url("edit", ["m" => get_menu_id(), "id" => 0]);?>',
                data: _post_data,
                callBack: callBack
            });
        });
    });
</script>
{/block}

</body>

</html>
