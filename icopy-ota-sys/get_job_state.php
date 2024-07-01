<?php

//接口输入只接受GET请求
//参数1为hash值，值已经校验

include './tools/sqlconfig.php';
include './tools/utils.php';
require_once './tools/aes.php';

// 返回code ：
// 0 两个表都没有查询到信息，也就是hash不存在
// 1 表1中查询到信息，表2中不存在，也就是任务要么没被处理，要么已经有结果了
// 2 表2中查询到信息，表1不在关注，任务开始排队
// 返回字典
// 0 不对应字典，内容为“hash不存在”
// 1 SN:设备sn，ADD_TIME:创建时间,OK_TIME:完成时间,CURRENT_STATE:完成状态
// 2 PROGRESS:进度值,POSITION:排队信息

//查询当前正在列表2的信息
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $aimhash = format_input($_GET["hash"]);

    if (!is_hash($aimhash)){
        resp_client(-1, "hash有误");
        mysqli_close($con);
        return FALSE;
    }

    $sql = "select * from ota_job_list
            where HASH = '$aimhash'";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        raise_sql_error($con, $sql);
        return FALSE;
    } else {
        $result_row_count = mysqli_num_rows($result);
        if ($result_row_count == 0) {
            //echo ("在列表2不存在");
            //此时要在1中查询
            $sql = "select * from usr_ota_requests
  		            where HASH = '$aimhash'";
            $result = mysqli_query($con, $sql);
            if (!$result) {
                raise_sql_error($con, $sql);
                return FALSE;
            }
            $result_row_count = mysqli_num_rows($result);
            if ($result_row_count == 0) {
                //表1不存在，返回hash不存在
                resp_client(0,"HASH不存在");
                mysqli_close($con);
                return FALSE;
            }
            $row = mysqli_fetch_assoc($result);
            // 1 SN:设备sn，ADD_TIME:创建时间,OK_TIME:完成时间,CURRENT_STATE:完成状态
            $api_result = array("SN" => $row["DEVICE_SN"],
                                "ADD_TIME" => $row["ADD_TIME"],
                                "OK_TIME" => $row["OK_TIME"],
                                "CURRENT_STATE" => $row["CURRENT_STATE"]);
            resp_client(1,$api_result);
        } else {//第二个表里有
            $row = mysqli_fetch_assoc($result);
            $api_result = array("PROGRESS" => $row["PROGRESS"],
                                "POSITION"=> ($row["ID"] - 1));
            resp_client(2,$api_result);
        }
    }
}
mysqli_close($con);
?>