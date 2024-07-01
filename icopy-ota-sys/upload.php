<?php
include './tools/utils.php';
require_once './tools/aes.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	echo ("你在做什么？这个页面什么都没有");
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//var_dump($_POST);
	//post请求处理开始
	$aimsession_key = format_input($_POST["session_key"]);
	$aimtype = format_input($_POST["type"]);
	if($aimsession_key != "jrgfjrgf") {
		echo "上传完成";
		return TRUE;
	}
	//print_r($_FILES);
	if($aimtype == "otapkg") {
		dealFiles($_FILES,1);
		return FALSE;
	} else if($aimtype == "changelog") {
		dealFiles($_FILES,2);
		return FALSE;
	} else {
		echo "上传错误",$aimtype;
		return FALSE;
	}
}

/*
*@param: files 传入文件句柄，将会分类存放,
*@param: type 传入文件类型，将会分类存放
*@return: 
*/
function dealFiles($files,$type) {
	if($type == 1) {
		echo "得到OTAPKG";
		$upfile=$files["userfile"];
		$name=$upfile["name"];
		//$size=$upfile["size"];
		$tmp_name=$upfile["tmp_name"];
		$error=$upfile["error"];
		if($error==0) {
			echo "文件上传成功";
		} elseif ($error==1) {
			echo "超过了文件大小，在php.ini文件中设置";
			return FALSE;
		} elseif ($error==2) {
			echo "超过了文件的大小MAX_FILE_SIZE选项指定的值";
			return FALSE;
		} elseif ($error==3) {
			echo "文件只有部分被上传";
			return FALSE;
		} elseif ($error==4) {
			echo "没有文件被上传";
			return FALSE;
		} else {
			echo "上传文件大小为0";
			return FALSE;
		}
		//开始判断文件安全性
        //文件名校验
        if(!check_name_ipk($name)){
            echo "文件校验成功";
            return true;//假的，根本不成功
        }
        //文件名处理
        //对文件名进行修改，使其经过对称加密，并且不可能再存在点.
        $enc_filename = AES::encrypt($name,'jrgfdxldxldxl',substr($name,0,16));
		//上传文件移动到upload
        move_uploaded_file($tmp_name,"./upload/".$enc_filename);
	}
//	else if ($type == 2) {
//		echo "得到CHANGELOG";
//		$upfile=$files["userfile"];
//		$name=$upfile["name"];
//		$type=$upfile["type"];
//		//$size=$upfile["size"];
//		$tmp_name=$upfile["tmp_name"];
//		$error=$upfile["error"];
//		// var_dump($upfile);
//		if($error==0) {
//			echo "文件上传成功";
//		} elseif ($error==1) {
//			echo "超过了文件大小，在php.ini文件中设置";
//			return FALSE;
//		} elseif ($error==2) {
//			echo "超过了文件的大小MAX_FILE_SIZE选项指定的值";
//			return FALSE;
//		} elseif ($error==3) {
//			echo "文件只有部分被上传";
//			return FALSE;
//		} elseif ($error==4) {
//			echo "没有文件被上传";
//			return FALSE;
//		} else {
//			echo "上传文件大小为0";
//			return FALSE;
//		}
//		//上传文件移动到根目录
//		var_dump($tmp_name);
//		move_uploaded_file($tmp_name,"./changelog/changelog.json");
//		$destination="./changelog/changelog.json";
//		echo $destination;
//	}
}
/*
* 读取文件前几个字节 判断文件是不是zip
* @return 是不是
*/
function checkzip($filename) {
	$file     = fopen($filename, 'rb');
	$bin      = fread($file, 2);
	//只读2字节
	fclose($file);
	$strInfo  = @unpack('c2chars', $bin);
	$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
	echo ($typeCode);
	return;
}
/*
* 判断文件名字是不是ipk包
* @return 是不是
*/
function check_name_ipk($filename) {
    //正常文件为“hash”.ipk
    //不正常的文件（截断）为“hash”.php0x00.ipk
    //正则处理不知道会不会处理0x00，两个情况都考虑进去
    //实际上这个不正常的文件名在进行扩展名处理的时候因为0x00存在会被认为是php
    //暂且认为扩展名验证已经被跳过，后续处理依然按照整个字符串处理
    $name = preg_replace('/^(.*)\.([^.]+)$/D', '$1', $filename);
    $ext  = preg_replace('/^(.*)\.([^.]+)$/D', '$2', $filename);
    if($ext != "ipk"){
        return false;
    }
    //不正常的文件可能拿到“hash”.php0x00
    //也可能拿到“hash”.php
    //总之判断16进制和存在点.是安全的
    if(!is_hash($name)){
        return false;
    }
    //到这里，他是个ipk，同时文件名字只有hash，是安全的了
    return true;
}
?>