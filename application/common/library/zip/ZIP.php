<?php
    /**
     * Created by PhpStorm.
     * User: hua
     * Date: 2020/8/12
     * Time: 22:15
     */

    namespace app\common\library\zip;


    class ZIP
    {

        public function export_user_data($user_path, $data_path)
        {
            $zip = new \ZipArchive();
            if(file_exists($data_path)){
                @unlink($data_path);
            }
            if ($zip->open($data_path, \ZipArchive::CREATE) === TRUE) {
                $this->addUserFileToZip($user_path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
                $zip->close(); //关闭处理的zip文件
                if(filesize($data_path)>0){
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header('Content-disposition: attachment; filename='.basename($data_path)); //文件名
                    header("Content-Type: application/zip"); //zip格式的
                    header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
                    header('Content-Length: '. filesize($data_path)); //告诉浏览器，文件大小
                    @readfile($data_path);
                }
            }
            @unlink($data_path);
        }



        function addUserFileToZip($path, $zip)
        {
            $handler = opendir($path); //打开当前文件夹由$path指定。
            while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                    if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                        $this->addUserFileToZip($path . "/" . $filename, $zip);
                    } else { //将文件加入zip对象
                        $zip->addFile($path . "/" . $filename);
                        $filepath = $path . "/" . $filename;
                        $filepath = str_replace("\\", "/", $filepath);
                        $position = strrpos($filepath, "/user/") + strlen("/user/");
                        $real_path = substr($filepath, $position);
                        $real_path = ltrim($real_path, "/");
                        //截取啊个第一个 出来
                        $menu_id = substr($real_path, 0, strpos($real_path, "/"));
                        if ($menu_id > 0) {
                            $menu_title = get_menu_title($menu_id);
                            if (empty($menu_title)) {
                                $zip->renameName($path . "/" . $filename, $real_path);
                            } else {
                                $real_path = $menu_title . substr($real_path, strlen($menu_id));
                                $real_path = iconv('utf-8', 'gb2312', $real_path);
                                $zip->renameName($path . "/" . $filename, $real_path);
                            }
                        } else {
                            $zip->renameName($path . "/" . $filename, $real_path);
                        }
                    }
                }
            }
            @closedir($path);
        }
    }