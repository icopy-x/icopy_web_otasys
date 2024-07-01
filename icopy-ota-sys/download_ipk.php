<?php
//接口输入只接受GET请求
//参数为hash值，值已经校验
include './tools/sqlconfig.php';
include './tools/utils.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $aimhash = format_input($_GET["hash"]);
    if (!is_hash($aimhash)) {
        header("http/1.1 404 not found");
        header("status: 404 not found");
        http_response_code(404);
        return false;
    }
    $file_path = get_ipkfile($aimhash);
    if(file_exists($file_path)){
        //文件存在，返回文件本身
        $device_sn = "";
        $version = "";
        //查询数据库得到用户提交的sn
        $sql = "select `DEVICE_SN` from usr_ota_requests where `HASH` = '$aimhash'";
        $result = mysqli_query($con,$sql);
        if(!$result) {
            resp_client(1, "查询数据失败");
            mysqli_close($con);
            return FALSE;
        } else {
            $result_row_count = mysqli_num_rows($result);
            if ($result_row_count == 0) {
                resp_client(2, "HASH不存在");
                return FALSE;
            }
            $row = mysqli_fetch_assoc($result);
            // 取出这两个数据，后续逻辑要用上
            $device_sn = $row["DEVICE_SN"];
        }
        //获取版本号
        $version = get_latest_version();

        //以只读和二进制模式打开文件
        $file = fopen ( $file_path, "rb" );
        //告诉浏览器这是一个文件流格式的文件
        Header ( "Content-type: application/octet-stream" );
        //请求范围的度量单位
        Header ( "Accept-Ranges: bytes" );
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header ( "Content-Length:" . filesize ( $file_path ) );
        Header ( "Content-Range: 0-".(filesize ( $file_path )-1)."/".filesize ( $file_path ));
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header ( "Content-Disposition: attachment; filename=".$device_sn.$version.".ipk");

        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $file_path ) );
        fclose ( $file );
        exit ();
    }
    else{
        //文件不存在，返回错误页面
        header("http/1.1 404 not found");
        header("status: 404 not found");
        http_response_code(404);
        return false;
    }
}

function get_latest_version(){
    $jsonfilename = "./changelog/changelog.json";
    if(file_exists($jsonfilename)) {
        $json_str = file_get_contents($jsonfilename);
        $json_obj = json_decode($json_str);
        $version_str = $json_obj[0]->version;
        if ($version_str != null && (strlen($version_str) > 0)) {
            return "_" . $json_obj[0]->version;
        }
    }
    return "latest";
}
?>