<?php
$database = "rrgadmin_ICOPY_OTA_DATABASE";
$dataname = "rrgadmin_icpyota";
$datapasswd = "jrgfota1234";
$datalocal = "localhost:3306";
//$dataport = "3306";
$con = mysqli_connect($datalocal,$dataname,$datapasswd,$database);
if(!$con){
    echo "数据库连接异常";
}
//设置查询数据库编码
mysqli_query($con,'set names utf8');
?>