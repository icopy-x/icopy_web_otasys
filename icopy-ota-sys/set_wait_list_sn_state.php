<?php
//接口输入只接受POST请求
//请求参数1是hash，验证码，已经验证
//请求参数2是state，已经验证

include './tools/sqlconfig.php';
include './tools/utils.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo("你在做什么？这个页面什么都没有");
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //post请求处理开始
    $aimhash = format_input($_POST["hash"]);
    if (!is_hash(strtolower($aimhash))) {
        echo "hash有误";
        mysqli_close($con);
        return FALSE;
    }
    $aimstate = format_input($_POST["state"]);
    if (is_state($aimstate) == false) {
        echo "输入state错误";
        mysqli_close($con);
        return FALSE;
    }

    //在数据库里查询该hash是否已经是完成状态
    $sql = "select CURRENT_STATE from usr_ota_requests 
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
            if ($row["CURRENT_STATE"] != 0) {
                echo "已经修改过了，不再修改<br>";
                mysqli_close($con);
                return FALSE;
            } else {
                //在数据库里修改该hash的状态
                $sql = "update usr_ota_requests 
  				SET OK_TIME = NOW(),
  				CURRENT_STATE = $aimstate
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

/*
*@param: data 传入数据，用于进行输入state的判断
*@return: 是否为state类型
*/
function is_state($data)
{
    if (is_numeric($data)) {
        if (floor($data) == 3 || floor($data) == 4) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

?>