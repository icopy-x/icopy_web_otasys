<?php
require_once './tools/aes.php';

/**
 * 回应客户端数据，自动转换为json流
 */
function resp_client($code, $msg) {
	header('Content-Type:application/json');
	$resp = array("code" => $code, "msg" => $msg);
	echo json_encode($resp,JSON_UNESCAPED_UNICODE);
}

/**
 * 判断是否为hash数据
 * 返回bool
 */
function is_hash($code) {
    return preg_match("/^[A-Fa-f0-9]+$/", $code) && (strlen($code) == 32);
}

/**
 * 输出sql错误
 */
function raise_sql_error($sqlcon,$sqlcode) {
    $err="sqlcode: " . $sqlcode . " error: " . mysqli_error($sqlcon);
    $err=AES::encrypt($err,'jrgfjrgfjrgfjrgf','qwertyuiasdfghjk');
    resp_client(-100,$err);
    mysqli_close($sqlcon);
}

/*
*@param: data 传入数据，用于进行用户输入数据格式整理
*@return: 经过格式化的数据，砍掉了/和制表符以及空格
*/
function format_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/*
*@param: email 输入一个email字符串
*@return: 返回字符串是否符合email格式
*/
function is_email($email) {
	$pattern = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/";
	if(preg_match($pattern, $email, $matches)) {
		return true;
	}
	return false;
}
/*
*@param: inputsn 输入一个sn字符串
*@return: 返回字符串是否全为数字
*/
function is_sn($inputsn) {
	if(is_numeric($inputsn)) {
		if(strlen($inputsn) == 8) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
/*
*@param: code 验证码字符串
*@return: 返回字符串是否全为数字
*/
function is_code($code) {
	if(is_numeric($code)) {
		if(strlen($code) == 4) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
/*
*@param: code 进度字符串
*@return: 返回字符串是否全为数字且为0-100
*/
function is_progress($code) {
	if(is_numeric($code)) {
		if(0 <= intval($code) && intval($code) <= 100) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/*
*@param:
*@return: 生成随机hash值，32位长度
*/
function get_hash() {
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
	$random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];
	$content = uniqid().$random;
	return MD5($content);
}

/**
 * 获取一个随机的验证码 
 * 这个验证码里面会携带一些特殊符号
 */
function get_captcha() {
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
	return $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];
}

/**
 * 加密任务HASH以此获得目标文件名
 */
function get_ipkfile($hash) {
	$enc_filename = AES::encrypt($hash.".ipk",'jrgfdxldxldxl',substr($hash,0,16));
	$file_path = "./upload/".$enc_filename;
	return $file_path;
}
?>