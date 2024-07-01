<?php
//接口输入只接受GET请求
//不需要参数
include './tools/sqlconfig.php';
require_once './tools/aes.php';

//查询当前正在列表1也就是正在等待的提交信息
  $sql = "select * from usr_ota_requests where ISWAITING = 1";
	$result = mysqli_query($con,$sql);
	if(!$result){
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
    mysqli_close($con);
		return FALSE;
	}
  else{
  	$result_row_count = mysqli_num_rows($result);
  	if($result_row_count==0){
  		//echo ("任务都完成了");
  		$list_data = array();
  		$enc = AES::encrypt(json_encode($list_data),'jrgfjrgfjrgfjrgf','qwertyuiasdfghjk');
      echo $enc;
      //echo AES::decrypt($enc,'jrgfjrgf','qwertyuiasdfghjk');
  	}
  	else{
  		//echo "有",$result_row_count,"个项目等待中";
  		$list_data = array();
  		while($row=mysqli_fetch_assoc($result)){
  			$subarr = array("EMAIL"=>$row["EMAIL"],
  			"DEVICE_SN"=>$row["DEVICE_SN"],
  			"HASH"=>$row["HASH"]);
  			array_push($list_data,$subarr);
      }
      $enc = AES::encrypt(json_encode($list_data),'jrgfjrgfjrgfjrgf','qwertyuiasdfghjk');
      echo $enc;
      
      //echo AES::decrypt($enc,'jrgfjrgf','qwertyuiasdfghjk');
  	}
  }

mysqli_close($con);
?>