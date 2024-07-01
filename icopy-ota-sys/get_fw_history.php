<!DOCTYPE html>
<?php
//接口输入只接受GET请求
//参数1为sn值，值已经校验
//参数2为E-mail值，值已经校验
include './tools/sqlconfig.php';
include './tools/utils.php';
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $aimsn = format_input($_GET["sn"]);
    $usr_email = strtolower(format_input($_GET["E-mail"]));
    if (is_email($usr_email) != TRUE) {
        echo "Email format error";
        mysqli_close($con);
        return FALSE;
    }
    if (is_sn($aimsn) != TRUE) {
        echo "S/N format error";
        mysqli_close($con);
        return FALSE;
	}

    $sql = "select  `ADD_TIME`,`OK_TIME`,`CURRENT_STATE`,`HASH` from usr_ota_requests where `DEVICE_SN` = '$aimsn' AND `EMAIL` = '$usr_email'";
    $result = mysqli_query($con,$sql);
    if(!$result) {
        echo "Data source error";
        mysqli_close($con);
        return FALSE;
    } else {
        $result_row_count = mysqli_num_rows($result);
        if($result_row_count == 0) {
            echo "No history";
            mysqli_close($con);
            return FALSE;
        }
    }
}
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>History</title>
	    <link rel="stylesheet" href="./css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/common.css"/>
	    <script src="./js/jquery.min.js"></script>
	    <script src="./js/bootstrap.min.js"></script>
		<style type="text/css">
			html, body { margin:0; padding:0; }
		</style>
	</head>
	<body>
		<div style="padding-top: 6px; padding-left: 36px; padding-right: 36px;">
			<h1><strong style="color: #333333;">Generate History List</strong></h1>
			<hr />
			<br/>
			<!-- 在此处显示相关的任务信息 -->
			<div>
				<table class="table table-bordered table-hover">
				  <caption><h3><strong>Table of task</strong></h3></caption>
				  <thead>
				    <tr>
				      <th>Start Time</th>
				      <th>End Time</th>
					  <th>Task HASH</th>
				      <th>Task Status</th>
					  <th>Download and details</th>
				    </tr>
				  </thead>
				  <tbody>
                  <?PHP
                      while ($row=mysqli_fetch_assoc($result)){
                          echo "<tr>";
                          echo "<td>".$row['ADD_TIME']."</td>";
                          echo "<td>".$row['OK_TIME']."</td>";
                          echo "<td>".$row['HASH']."</td>";
                          if ($row['CURRENT_STATE'] == 1) {
                              echo "<td>"."Success"."</td>";
                          } elseif ($row['CURRENT_STATE'] == 2) {
                              echo "<td>"."Failed"."</td>";
                          } else {
                              echo "<td>"."Processing"."</td>";
                          }
                          echo "<td>";
                          echo "<a href=\"go_queuing.php?hash=";
                          echo $row['HASH'];
                          echo "\">Download</a></td>";
                          echo "</tr>";
                      }
                  mysqli_close($con);
                  ?>
				  </tbody>
				</table>
			</div>
		</div>
	</body>
	
	<script>
	</script>
	
</html>
