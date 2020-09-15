{extend name="common:opt_layout"}

{block name="content"}
<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        姓名
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo $row['name']; ?></div>
    </div>
</div>


<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        手机号
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo $row['mobile']; ?></div>
    </div>
</div>


<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        邮箱
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo $row['email']; ?></div>
    </div>
</div>

<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        内容
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo $row['content']; ?></div>
    </div>
</div>


<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        系统备注
    </label>
    <div class="layui-input-block">
        <textarea id="remark" name="remark" class="layui-textarea">{$row['remark']}</textarea>
    </div>
</div>


<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        提交时间
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo time_show($row['add_time']); ?></div>
    </div>
</div>

<div class="layui-form-item">
    <label for="username" class="layui-form-label">
        IP地址
    </label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-word-aux"><?php echo ip_to_address($row['ip']); ?></div>
    </div>
</div>


<div class="layui-form-item">
    <label for="status" class="layui-form-label">
        <span class="x-red">*</span>状态
    </label>

    <div class="layui-input-inline">
		<?php
			if ($row['status'] != 0) {
				echo '<input lay-filter="switch_status" type="checkbox" checked="" id="status" name="status" lay-skin="switch"
               lay-text="启用|停用" class="layui-input">';
			} else {
				echo '<input lay-filter="switch_status" type="checkbox" id="status" name="status" lay-skin="switch" lay-text="启用|停用"
               class="layui-input">';
			}
		?>
    </div>
</div>




{/block}



