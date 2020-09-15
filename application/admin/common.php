<?php

	use app\admin\model\Menu as ModelMenu;
	use think\Db;

	if (!function_exists("build_nav")) {
		/**
		 * @return string
		 */
		function build_nav()
		{
			$menu_row = ModelMenu::get(MENU_ID);
			if (MENU_ID == 0 || empty($menu_row)) {
				$html = ' <a><cite>首页</cite></a>';
				return $html;
			}
			$parent_id = $menu_row->parent_id;
			if ($parent_id == 0) {
				$html = '<a href="javascript:void(0);">首页</a><a><cite>' . $menu_row->title . '</cite></a>';
				return $html;
			}
			$parent_row = ModelMenu::get($parent_id);
			$html = ' <a href="javascript:void(0);">首页</a><a>' . $parent_row->title . '</a><a href="javascript:void(0);"><cite>' . $menu_row->title . '</cite></a>';
			return $html;
		}
	}

	if (!function_exists("get_menu_id")) {
		function get_menu_id()
		{
			$menu_id = MENU_ID;
			return $menu_id;
		}
	}

	if (!function_exists("get_class_id")) {
		function get_class_id()
		{
			$class_id = CURRENT_CLASS_ID;
			return $class_id;
		}
	}

	if (!function_exists("get_controller")) {
		function get_controller()
		{
			$controller = CONTROLLER;
			return $controller;
		}
	}

	if (!function_exists("echo_editable_icon")) {
		function echo_editable_icon($title)
		{
			echo '<span title="该数据列可以直接在单元格完成文字编辑">' . $title . '<i class="layui-icon layui-icon-fonts-code table-editable-icon"></i></span>';
			return true;
		}
	}

	if (!function_exists("echo_editor")) {
		function echo_editor($field, $row)
		{
			$value = isset($row[$field]) ? $row[$field] : "";
			$menu_id = get_menu_id();
			$menu_row = ModelMenu::get($menu_id);
			$menu_row = to_array($menu_row);

			if (isset($menu_row['rich_tags'])) {
				$rich_tag = $menu_row['rich_tags'];
			} else {
				$rich_tag = "";
			}
			if (isset($menu_row['rich_css_link_url']) && !empty($menu_row['rich_css_link_url'])) {
				$css_linkurl_arr = explode("\n", $menu_row['rich_css_link_url']);
				foreach ($css_linkurl_arr as $_key => &$_css) {
					$_css = trim(str_replace(array(" ", ";", ","), "", $_css));
					if (empty($_css)) {
						unset($css_linkurl_arr[$_key]);
					}
				}
			} else {
				$css_linkurl_arr = array();
			}

			if (!empty($css_linkurl_arr)) {
				$css_linkurl_str = implode(",", $css_linkurl_arr);
			} else {
				$css_linkurl_str = "";
			}
			$html = '<textarea class="editor_textarea" id="' . $field . '"></textarea>
        <script>
            var obj = UE.getEditor("' . $field . '", {
                serverUrl: "' . url("upload/editorUpload", ["m" => $menu_id, "c" => get_controller()]) . '",
                autoFloatEnabled:false,
                 autoHeightEnabled:false,';
			if (!empty($rich_tag)) {
				$html .= 'rich_tag:"' . $rich_tag . '",';
			}
			if (!empty($css_linkurl_arr)) {
				$html .= 'cssurl:"' . $css_linkurl_str . '",';
			}
			$html .= '});
            if(typeof editor_list=="undefined" ){
            	var editor_list = {};
            }
            editor_list["' . $field . '"] = obj;
            editor_list["' . $field . '"].ready(function() {
                //设置编辑器的内容
                editor_list["' . $field . '"].setContent(`' . $value . '`);
                $(".edui-editor-messageholder.edui-default").css({ "visibility": "hidden" });
            });
        </script>';
			echo $html;
		}
	}


	if (!function_exists("get_date")) {
		function get_date($date = "")
		{
			if (empty($date)) {
				$date = date("Y-m-d H:i:s");
			}
			return $date;
		}
	}

	if (!function_exists("is_menu_class")) {
		function is_menu_class()
		{
			$menu_id = get_menu_id();//判断这个menu 是否需要
			try {
				$menu_row = ModelMenu::get($menu_id);
				if (empty($menu_row)) {
					return false;
				} else {
					if ($menu_row->is_show_class) {
						return true;
					} else {
						return false;
					}
				}
			} catch (Exception $exception) {
				return false;
			}
		}
	}


	if (!function_exists("left_content_nav")) {
		function left_content_nav($is_show_class, $table_name = "")
		{
			$class_deep = get_class_deep();
			if ($is_show_class) {
				$class_name = $table_name . "_class";
				$menu_id = get_menu_id();
				$class_id = get_class_id();
				if ($class_id > 0) {
					try {
						$class_row = Db::name($class_name)->where("menu_id", $menu_id)->where('class_id', $class_id)->where("status", '<>', 2)->find();
						if (!empty($class_row)) {
							$class_title = $class_row['title'];
						} else {
							$class_title = "未找到分类";
							$class_id = 0;
						}
					} catch (Exception $e) {
						$class_title = $e->getMessage();
						$class_id = 0;
					}
				} else {
					$class_title = "全部";
				}

				$open_arr = []; //需要open出来的数组
				if ($class_id > 0 && isset($class_row)) {
					$fid = $class_row['parent_id'];
					while ($fid > 0) {
						$open_arr[] = $fid;
						$parent_class_row = Db::name($class_name)->where("menu_id", $menu_id)->where('class_id', $fid)->where("status", "<>", 2)->find();
						$fid = $parent_class_row['parent_id'];
					}
				}
				//获取所有的分类
				if ($class_id > 0) {
					$open_arr2 = $open_arr;
					$open_arr2[] = $class_id;
				}
				//open_arr2 用来判断删除后 是否需要重新刷新那个
				$parent_class_row = Db::name($class_name)->where("menu_id", $menu_id)->where("status", '<>', 2)->order("parent_id asc,orders asc,add_time asc")->select();
				$zNodes = [];
				if (!empty($parent_class_row)) {
					foreach ($parent_class_row as $parent_row) {
						$tmp = [];
						$tmp['id'] = $parent_row['class_id'];
						$tmp['pId'] = $parent_row['parent_id'];
						if (CURRENT_CLASS_ID == $parent_row['class_id']) {
							$current_class = "current_class";
						} else {
							$current_class = "";
						}
						if ($parent_row['status'] == 0) {
							//需要画斜横向
							$tmp['name'] = "<span class='underline_red " . $current_class . "'>" . $parent_row['title'] . "</span>";
						} else {
							$tmp['name'] = "<span class='" . $current_class . "'>" . $parent_row['title'] . "</span>";
						}
						$tmp['realname'] = $parent_row['title'];
						if (in_array($parent_row['class_id'], $open_arr)) {
							$tmp['open'] = true;
						} else {
							$tmp['open'] = false;
						}
						if ($parent_row['class_id'] == $class_id) {
							$tmp['checked'] = true;
						} else {
							$tmp['checked'] = false;
						}

						$zNodes[] = $tmp;
					}
				}
				//输出树形结构
				$html2 = '<SCRIPT type="text/javascript">
		var currentTree = {};
		var zTree = {};
		var allow_deep = ' . $class_deep . '; 
        var setting = {
            view: {
                addHoverDom: addHoverDom,
                removeHoverDom: removeHoverDom,
                selectedMulti: false,
                nameIsHTML: true,
                showTitle:false
            },
            check: {
                enable: false
            },
            data: {
                simpleData: {
                    enable: true
                }
            },
            edit: {
                showRemoveBtn: false,
                showRenameBtn: false,
                drag: {
                    autoExpandTrigger: true,
                    isCopy:false,
                    isMove: true,
                    prev: true,
                    next: true,
                    inner: true,
                    borderMax: 10,
                    borderMin: -5,
                    minMoveSize: 5,
                    maxShowNodeNum: 5,
                    autoOpenTime: 500
                },
                enable: true,
            },
            callback: {
                beforeDrop: function (treeId, treeNodes, targetNode, moveType) {
                    var is_move  = false;
                    console.log(treeNodes);
                    var source_id = treeNodes[0].id;
                    var target_id = targetNode.id;
                    $.ajax({
                       type: "POST",
                       async:false,
                       url:"'.url("moveClass").'",
                       data: {source_id:source_id,target_id:target_id,type:moveType,m:'.(get_menu_id()).'},
                       success: function(res){
                          res = JSON.parse(res);
                          if(res.code==0){
                            layer.msg(res.msg, {"time": 1500, icon: 1});
                            is_move= true;
                          }else{
                            layer.msg(res.msg, {"time": 1500, icon: 2});
                            is_move= false;
                          }
                       },
                   });
                   return is_move;
                },
                onClick:function(event, treeId, treeNode){
                    var class_id = treeNode.id;
                    var url = "' . url("index", ["m" => MENU_ID]) . '";
                    url=url+"?cid="+class_id; 
                    location.href=url;
                }
            }
        };
        var zNodeAdd = function(treeNodeObj){
            console.log(treeNodeObj);
        };
        var zNodeEdit = function(treeNodeObj){
            console.log(treeNodeObj);
        };
        var zNodeDel = function(){
            
        }
        var zNodes = ' . json_encode($zNodes, JSON_UNESCAPED_UNICODE) . ';
        $(document).ready(function () {
            $.fn.zTree.init($("#classTree"), setting, zNodes);
        });
        var link_all = function(){
            var url = "' . url("index", ["m" => MENU_ID]) . '";
            location.href=url;
        }
        var add_first_class = function(){
              zTree = $.fn.zTree.getZTreeObj("classTree");
              currentTree= "add_first_class";
              x_admin_show("新增子分类","' . url("addClass", ["m" => MENU_ID]) . '"+"?cid=0&parent=0");
        }
        function addHoverDom(treeId, treeNode) {
            var sObj = $("#" + treeNode.tId + "_span");
             var node_deep = treeNode.getPath().length;
             var add_str = "<span  class=\'button add addBtn_"+treeNode.tId+" \' id=\'addBtn_" + treeNode.tId
                + "\' title=\'新增\' onfocus=\'this.blur();\'></span>"; 
            var edit_str = "<span  class=\'button edit EditBtn_"+ treeNode.tId+"\' id=\'EditBtn_" + treeNode.tId + "\' title=\'编辑\' onfocus=\'this.blur();\'></span>";
            var del_str = "<span  class=\'button remove DelBtn_"+treeNode.tId+"\' id=\'DelBtn_" + treeNode.tId + "\' title=\'删除\' onfocus=\'this.blur();\'></span>";
            var addBtn ="#addBtn_" + treeNode.tId;
            var editBtn = "#EditBtn_" + treeNode.tId;
            var delBtn = "#DelBtn_" + treeNode.tId;
            if(node_deep<allow_deep){
                if($(addBtn).length>0){                   
                  $(addBtn).show();
                }else{
                    sObj.append(add_str);
                    $(addBtn).bind("click",function(event){
                        event.stopPropagation();
                        zTree = $.fn.zTree.getZTreeObj("classTree");
                  	    var that = this;
                  	    var tree_id = treeNode.id;
                  	    var tree_title = treeNode.realname;
                  	    currentTree= treeNode;
                  	    x_admin_show("新增子分类:"+tree_title,"' . url("addClass", ["m" => MENU_ID]) . '"+"?cid=0&parent="+tree_id);
                    });
                }
            }
            if($(editBtn).length>0){
                $(editBtn).show();
            }else{
                 sObj.append(edit_str);
                 $(editBtn).bind("click",function(event){
                        event.stopPropagation();
                        zTree = $.fn.zTree.getZTreeObj("classTree");
                  	    var that = this;
                  	    var tree_id = treeNode.id;
                  	    var tree_title = treeNode.realname;
                  	    currentTree= treeNode;
                  	    x_admin_show("修改分类:"+tree_title,"' . url("editClass", ["m" => MENU_ID]) . '"+"?cid="+tree_id);
                 });
            }
            
            if($(delBtn).length>0){
                $(delBtn).show();
            }else{
                 sObj.append(del_str);
                 $(delBtn).bind("click",function(event){
                     event.stopPropagation();
                 	 var that = this;
                      layer.confirm("确认要删除分类吗？<br/>删除后分类下所有列表数据将删除",function(index){
                       var tree_id = treeNode.id;
                       zTree = $.fn.zTree.getZTreeObj("classTree");
                        post_data({
                            url:"' . url("delClass") . '",
                            data:{m:' . get_menu_id() . ',cid:tree_id},
                            callBack: function () {
                                zTree.removeNode(treeNode);
                            },
                        })
                    });
                 });
            }
        };
        
        function removeHoverDom(treeId, treeNode) {
            $(".addBtn_" + treeNode.tId).hide();
            $(".EditBtn_" + treeNode.tId).hide();
            $(".DelBtn_" + treeNode.tId).hide();
        };
    </SCRIPT>';

				$html = '';
				$html .= '<div class="layui-row">';
				$html .= '<div class="layui-col-md2">';
				$html .= "<div style='margin-bottom: 6px;'><button type='button' style='margin-left: 10px;padding: 0 18px;' class='layui-btn layui-btn-sm' onclick='link_all();'>全部</button><button class='layui-btn layui-btn-sm' type='button' onclick='add_first_class();'>新增一级分类</button></div>";
				$html2 .= '<ul id="classTree" class="ztree"></ul>';
				$html .= $html2;
				$html .= '</div>';
				$html .= '<div class="layui-col-md10 list-md10">';
				$html .= '<h3>当前：' . $class_title . '</h3>';
				$html .= '<table class="layui-table" id="xqy_datatable" lay-filter="xqy_datatable"></table>';
				$html .= '</div>';
				$html .= '</div>';
				return $html;
			} else {
				//直接输入table结构
				$html = ' <table class="layui-table" id="xqy_datatable" lay-filter="xqy_datatable"></table>';
				return $html;
			}

		}
	}


	if (!function_exists("get_time")) {
		function get_time()
		{
			$time = date("Y-m-d H:i:s");
			return $time;
		}
	}

	/**
	 * 可拖动排序
	 */
	if (!function_exists("build_sort")) {
		function build_sort($element_id)
		{
			return "<script>
                    Sortable.create(" . $element_id . ", {
                    group: 'words',
                    animation: 150,
                    onAdd: function (evt) {
                        console.log('onAdd.bar:', evt.item);
                    },
                    onUpdate: function (evt) {
                        console.log('onUpdate.bar:', evt.item);
                    },
                    onRemove: function (evt) {
                        console.log('onRemove.bar:', evt.item);
                    },
                    onStart: function (evt) {
                        console.log('onStart.foo:', evt.item);
                    },
                    onEnd: function (evt) {
                        console.log('onEnd.foo:', evt.item);
                    }
                });
            </script>";
		}
	}


	if (!function_exists("get_class_deep")) {
		function get_class_deep()
		{
			$menu_row = ModelMenu::get(MENU_ID);
			if(!empty($menu_row)){
				$menu_row = to_array($menu_row);
				if ($menu_row['is_show_class']) {
					$path = max(1, (int)$menu_row['class_deep']);
					return $path;
				} else {
					return 0;
				}
			}else{
				return 0;
			}
		}
	}
?>