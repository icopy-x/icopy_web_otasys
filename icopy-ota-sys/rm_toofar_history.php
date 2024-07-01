<?php
include './tools/sqlconfig.php';
include './tools/utils.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 请求处理开始
	$aimsession_key = format_input($_POST["session_key"]);
	if($aimsession_key != "jrgfjrgf") {
		echo "无权访问";
		return TRUE;
	}
	// 获得要删除的范围
	// 这个值一般是个数字，
	// 我们需要尝试转换
	$delete_history_range = format_input($_POST["history_range"]);
	if (!is_numeric($delete_history_range)) {
		$delete_history_range = 0;
	}
	if ($delete_history_range < 0 || $delete_history_range > 10) {
		$delete_history_range = 3;
	}
	echo "开始进行删除任务......<br/>-><br/><br/>";
	$del_count = 0;
	// 使用此SQL语句可以查询所有在日期范围外的构建记录
	$sql = "SELECT * FROM `usr_ota_requests` WHERE datediff(now(), `usr_ota_requests`.`ADD_TIME`) >= '$delete_history_range'";
	$result = mysqli_query($con, $sql);
	if (!$result) {
	    echo "Error: " . $sql . "<br/>" . mysqli_error($con);
	    mysqli_close($con);
	    return FALSE;
	} else {
	    $result_row_count = mysqli_num_rows($result);
	    if ($result_row_count == 0) {
	        echo("没有指定范围内的记录<br/>");
	    } else {
	        while($row = mysqli_fetch_assoc($result)) {
				// 我们需要在此处进行迭代删除文件和SQL历史
				$hash = $row['HASH'];
				$rowid = $row['ID'];
				
				// 第一，先尝试删除文件
				$file_path = get_ipkfile($hash);
				// echo "删除文件: '$file_path'<br>";
				if (unlink($file_path)) {
					echo "删除文件成功: '$hash'<br/>";
				}
				
				// 第二，尝试删除SQL记录
				$sql = "DELETE FROM `usr_ota_requests` WHERE `usr_ota_requests`.`ID`='$rowid'";
				$del_ret = mysqli_query($con, $sql);
				if (!$result) {
				    echo "Error: " . $sql . "<br/>" . mysqli_error($con);
				    mysqli_close($con);
				    return FALSE;
				} else {
					echo "删除记录";
				    $result_row_count = mysqli_num_rows($result);
					if ($result_row_count == 0) {
					    echo "失败";
					} else {
						echo "成功";
						$del_count += 1;
					}
					echo ": '$rowid'<br/>";
				}
				
				echo "<hr/>";
			}
		}
	}
	echo "<br/>-><br/>删除完成，清除了'$del_count'条记录<br/>";
	mysqli_close($con);
	return;
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
	echo ("你在做什么？这个页面什么都没有");
}