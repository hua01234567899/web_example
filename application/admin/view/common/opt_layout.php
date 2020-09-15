<!doctype html>
<html lang="en">
<head>
    {include file="common:header"/} <!--引入顶部公共模板文件-->
    {block name="header_script"}  <!--引入头部的style 代码  script代码-->
    {/block}
</head>

<body>

<div class="x-body">

    <form class="layui-form" method="post">
        <!--表单内容开始--->
        {block name="content"}
        {/block}
        <!--表单内容结束--->

        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button type="button" class="layui-btn" lay-filter="post_opt" lay-submit="" id="confirm_button">确定</button>
            <button type="reset" class="layui-btn layui-btn-primary" lay-filter="reset_button" id="reset_button">重置
            </button>
        </div>
    </form>
</div>

{block name="form_script"}

{/block}

{block name="footer_script"}
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
                if (_key == 'editorValue') {
                    delete _post_data[_key];
                }
            }
            if (typeof editor_list != 'undefined') {
                for (var editor_field in editor_list) {
                    var editor_content = editor_list[editor_field].getContent();
                    _post_data[editor_field] = editor_content;
                }
            }
            if (typeof s_callBack != 'undefined' && typeof s_callBack == "function") {
                var callBack = s_callBack;
            } else {
                var callBack = function (res) {
                    var index = parent.layer.getFrameIndex(window.name);
                    //关闭当前frame
                    parent.layer.close(index);
                    parent.location.reload();
                }
            }
            console.log(_post_data);
            post_data({
                data: _post_data,
                callBack: callBack
            });
        });


    });
</script>
{/block}


</body>

</html>