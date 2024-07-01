<?php
//接口输入只接受POST请求
//请求参数需要是json数据


//todo：是否存在json注入的可能？


header ( "Content-type:text/html;charset=utf-8" );
include './tools/sqlconfig.php';
include './tools/utils.php';

$usr_email = $sn = $request_hash = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	echo ("你在做什么？这个页面什么都没有");
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql = "INSERT INTO ota_job_list (ID, HASH, PROGRESS) VALUES ";
	//post请求处理开始
	$aimjson = format_json($_POST["jsonstr"]);
	if($aimjson == "[]") {
		//执行清空表
		$sql_del ="DELETE FROM ota_job_list";
		$result = mysqli_query($con,$sql_del);
		if(!$result) {
			echo "Error: " . $sql_del . "<br>" . mysqli_error($con);
			mysqli_close($con);
			return false;
		}
		mysqli_close($con);
		echo "列表为空，清空成功";
		return true;
	}
	$obj = json_decode($aimjson);
	var_dump($obj);
	if($obj == NULL) {
		echo "json解析失败";
		mysqli_close($con);
		return FALSE;
	}
	$arrnum = count($obj);
	for ($i=0;$i<$arrnum;$i++) {
		$thishash = $obj[$i]->hash;
		$thisprog = $obj[$i]->progress;
		if(!is_hash($thishash)){
		echo "HASH有误";
		mysqli_close($con);
		return FALSE;	
		}
        if(!is_progress($thisprog)){
            echo "进度有误";
            mysqli_close($con);
            return FALSE;
        }
		if(empty($thishash) || !isset($thisprog)) {
			echo "参数错误";
			mysqli_close($con);
			return FALSE;
		}
		$sql .= "(".($i+1).",'".$thishash."',".$thisprog."),";
	}
	$sql = substr($sql,0,strlen($sql)-1);
	echo $sql;
	//执行清空表
	$sql_del ="DELETE FROM ota_job_list";
	$result = mysqli_query($con,$sql_del);
	if(!$result) {
		echo "Error: " . $sql_del . "<br>" . mysqli_error($con);
		mysqli_close($con);
		return false;
	}
	//执行添加
	$result = mysqli_query($con,$sql);
	if(!$result) {
		echo "Error: " . $sql . "<br>" . mysqli_error($con);
		mysqli_close($con);
		return FALSE;
	} else {
		if(mysqli_affected_rows($con)==0) {
			echo ("添加出错<br>");
		} else {
			echo ("添加成功<br>");
		}
	}
	//关闭数据库
	mysqli_close($con);
	echo "//TODO:返回提交成功页面，告知用户要关注邮箱了<br>";
	return;
}
/*
*@param: data 传入数据，用于进行用户输入数据格式整理
*@return: 经过格式化的数据，砍掉了/和制表符以及空格
*/
function format_json($data) {
	$data = trim($data);
	$data = stripslashes($data);
	return $data;
}
?>