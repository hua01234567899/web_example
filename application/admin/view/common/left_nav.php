<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <?php
                foreach ($admin_menu as $vo) {
                    $menu_html = "";
                    if (isset($vo['child']) and count($vo['child']) > 0) {
                        if ($vo['is_active']) {
                            $menu_open = "open";
                        } else {
                            $menu_open = "";
                        }
                        $menu_html = '<li class="' . $menu_open . '">
                                        <a href="javascript:;">
                                            <i class="iconfont">'.$vo['icon'].'</i>
                                            <cite>' . $vo['title'] . '</cite>
                                            <i class="iconfont nav_right">&#xe6a7;</i>
                                        </a>
                                        <ul class="sub-menu">';
                        foreach ($vo['child'] as $children_row) {
                            if ($children_row['is_active']) {
                                $sub_menu_active = "menu-current";
                            } else {
                                $sub_menu_active = "";
                            }
                            $menu_html .= ' <li><a _href="' . url($children_row['controller'] . "/index", ['m' => $children_row['id']]) . '"><i class="iconfont">&#xe6a7;</i><cite>' . $children_row['title'] . '</cite></a></li>';
                        }
                        $menu_html .= '  </ul>
                                     </li>';

                    } else {
                        if ($vo['is_active']) {
                            $sub_menu_active = "menu-current";
                        } else {
                            $sub_menu_active = "";
                        }
//                        $menu_html='<li _href="'.url($vo['controller'] . "/index", ['m' => $vo['id']]).'"><a _href="'.url($vo['controller'] . "/index", ['m' => $vo['id']]).'"><i class="iconfont">'.$vo['icon'].'</i><cite>'.$vo['title'].'</cite></a></li>';
                        $menu_html='<li class="no_sub_menu"><a _href="'.url($vo['controller'] . "/index", ['m' => $vo['id']]).'"><i class="iconfont">'.$vo['icon'].'</i><cite>'.$vo['title'].'</cite></a></li>';
                    }
                    echo $menu_html;
                }
            ?>
        </ul>
    </div>
</div>