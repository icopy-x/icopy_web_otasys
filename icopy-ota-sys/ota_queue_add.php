<?php
//接口输入只接受POST请求
//请求参数1是code，验证码，已经验证
//请求参数2是E-mail，已经验证
//请求参数3是serialnumber，已经验证

header ( "Content-type:text/html;charset=utf-8" );
include './tools/sqlconfig.php';
include './tools/utils.php';

$usr_email = $sn = $request_hash = "";
// 阻止GET操作
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	echo ("你在做什么？这个页面什么都没有");
	mysqli_close($con);
	return false;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 谨记启动会话！
	session_start();
	$code = format_input($_POST["code"]);
	if (is_code($code) != TRUE) {
		resp_client(3, "不是一个合法的验证码");
		mysqli_close($con);
		return FALSE;
	}
	$code_from_session = $_SESSION['authcode'];
	// 打乱随机数，避免被重复使用
	$_SESSION['authcode'] = get_captcha();
	// 然后我们就可以判断验证码是否正确了
	if ($code != $code_from_session) {
		resp_client(4, "验证码错误");
		mysqli_close($con);
		return FALSE;
	}
	
	//post请求处理开始
	$usr_email = strtolower(format_input($_POST["E-mail"]));
	if (is_email($usr_email) != TRUE) {
		resp_client(1, "不是一个正确的邮箱格式");
		mysqli_close($con);
		return FALSE;
	}
	$sn = format_input($_POST["serialnumber"]);
	if (is_sn($sn) != TRUE) {
		resp_client(2, "不是一个合法的sn");
		mysqli_close($con);
		return FALSE;
	}
	
	$request_hash = get_hash();
	// 生成hash
	// 到这里，用户输入的email和sn已经获得，并且为此次提交生成了hash
	//在数据库里查询该sn是否在今日已经提交过了
	$sql = "select * from usr_ota_requests where DateDiff(ADD_TIME,NOW())=0 AND DEVICE_SN = $sn";
	$result = mysqli_query($con,$sql);
	if(!$result) {
		// echo "Error: " . $sql . "<br>" . mysqli_error($con);
		resp_client(5, "数据库错误");
		mysqli_close($con);
		return FALSE;
	} else {
		$result_row_count = mysqli_num_rows($result);
		if($result_row_count==0) {
			// echo ("0行输出，当日未提交过<br>");
		} else {
			resp_client(6, "当日已经提交过");
			mysqli_close($con);
			return;
		}
	}
	//插入数据库
	$sql = "insert into usr_ota_requests (EMAIL,DEVICE_SN,HASH,ADD_TIME,ISWAITING)
	value ('$usr_email','$sn','$request_hash',NOW(),1)";
	$result = mysqli_query($con,$sql);
	if(!$result) {
		// echo "Error: " . $sql . "<br>" . mysqli_error($con);
		resp_client(7, "提交失败");
		mysqli_close($con);
		return FALSE;
	} else {
		resp_client(0, $request_hash);
	}
	//关闭数据库
	mysqli_close($con);
	return true;
}
?>