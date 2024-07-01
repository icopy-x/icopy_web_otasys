<?php
//维护模式控制
include './tools/sqlconfig.php';

function get_maintenance_state() {
	global $con;
	// 开始查询数据库，获得维护状态
	$sql = "select * from ota_sys_maintenance_state";
	$result = mysqli_query($con, $sql);
	if (!$result) {
		mysqli_close($con);
		return true;
	} else {
		$result_row_count = mysqli_num_rows($result);
		if ($result_row_count == 0) {
			mysqli_close($con);
			return true;
		}
		$row = mysqli_fetch_assoc($result);
		$state = $row["STATE"];
		return $state == 1;
	}
}

function set_maintenance_state($state) {
	global $con;
	$aimtype = $state;
	if (!is_maintenance_state($aimtype)) {
		return true;
	}
	$sql = "update ota_sys_maintenance_state 
					SET STATE = $aimtype,
					where id = '0'";
	$result = mysqli_query($con, $sql);
	if (!$result) {
		mysqli_close($con);
		return FALSE;
	} else {
		if (mysqli_affected_rows($con) == 0) {
			mysqli_close($con);
			return FALSE;
		} else {
			mysqli_close($con);
			return TRUE;
		}
	}
}

?>