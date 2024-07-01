<?php
//接口输入只接受POST请求
//请求参数1是hash，已经验证

include './tools/sqlconfig.php';
include './tools/utils.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo("你在做什么？这个页面什么都没有");
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //post请求处理开始
    $aimhash = format_input($_POST["hash"]);
    if (!is_hash(strtolower($aimhash))) {
        resp_client(-1, "hash有误");
        mysqli_close($con);
        return FALSE;
    }

    //在数据库里查询该hash是否已经是完成状态
    $sql = "select ISWAITING from usr_ota_requests 
  where HASH = '$aimhash'";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        mysqli_close($con);
        return FALSE;
    } else {
        $result_row_count = mysqli_num_rows($result);
        if ($result_row_count == 0) {
            echo("HASH不存在<br>");
        } else {
            $row = mysqli_fetch_assoc($result);
            if ($row["ISWAITING"] == 0) {
                echo "已经修改过了，不再修改<br>";
                mysqli_close($con);
                return FALSE;
            } else {
                //在数据库里修改该hash的状态
                $sql = "update usr_ota_requests 
  				SET ISWAITING = 0 
  				where HASH = '$aimhash'";
                $result = mysqli_query($con, $sql);
                if (!$result) {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                    mysqli_close($con);
                    return FALSE;
                } else {
                    if (mysqli_affected_rows($con) == 0) {
                        echo "修改失败，存在未知错误<br>";
                        mysqli_close($con);
                        return FALSE;
                    } else {
                        echo "hash存在,修改了", mysqli_affected_rows($con), "行<br>";
                        mysqli_close($con);
                        return TRUE;
                    }
                }
            }
        }
    }

    mysqli_close($con);
    return;
}
?>