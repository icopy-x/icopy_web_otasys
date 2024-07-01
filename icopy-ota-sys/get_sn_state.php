<?php
header ( "Content-type:text/html;charset=utf-8" );
include './tools/sqlconfig.php';
include './tools/utils.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	echo ("你在做什么？这个页面什么都没有");
	mysqli_close($con);
	return false;
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//post请求处理开始
	if (!isset($_POST["hash"])) {
		echo "缺少hash参数";
		mysqli_close($con);
		return false;
	}
	// 取出数据
	$hash = $_POST["hash"];
	// 判断HASH是否是16进制字符串
	if (is_hash($hash)) {
		// 开始查询数据库，获得对应HASH的SN的状态
		$sql = "select `CURRENT_STATE`  from usr_ota_requests where `HASH` = '$hash'";
		$result = mysqli_query($con,$sql);
		if(!$result) {
			resp_client(1, "查询数据失败");
			mysqli_close($con);
			return FALSE;
		} else {
			$result_row_count = mysqli_num_rows($result);
			if($result_row_count == 0) {
				resp_client(2, "HASH不存在");
				return FALSE;
			}
			$row = mysqli_fetch_assoc($result);
			// 取出这两个数据，后续逻辑要用上
			$state = $row["CURRENT_STATE"];
			if ($state == 3) {  // 如果在数据库中的状态码为3，则SN无效
				// 此时我们需要自动删除这段HASH值所属于的行
				$sql = "delete from `usr_ota_requests` where `HASH` = '$hash'";
				$result = mysqli_query($con,$sql);
				if(!$result) {
					resp_client(5, "删除无效的SN数据失败");
					mysqli_close($con);
					return FALSE;
				}
				resp_client(0, $state);
			} else if ($state == 4 || $state == 2 || $state == 1 ) {
				// SN有效
				resp_client(0, $state);
			} else if ($state == 0) {
				resp_client(3, "继续等待SN校验");
			} else {
				// TODO 任务可能已经是构建好的了
				// 可以直接处理下这个任务
				resp_client(-1, $state);
			}
		}
	} else {
		resp_client(4, "HASH码不是有效的HEX字符串");
	}
	mysqli_close($con);
}