<?php
	include './tools/maintenance.php';
	if (get_maintenance_state()) {
		// 当前处于维护状态
		// 我们跳转到告知的页面
		// 服务器维护页面
		echo <<<EOF
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset="utf-8">
					<title></title>
					<style>
						.coninter {
							width: 800px; 
							height: 250px; 
							background-color: #0E84B5; 
							color: white; 
							text-align: center; 
							border-radius: 16px;
							margin-left: auto;
							margin-right: auto;
							margin-top: 250px;
						}
					</style>
				</head>
				<body style="text-align: center;">
					<div class="coninter">
						<h1 style="margin-top: 100px; display: inline-block;">
							<strong>
								Website is Maintaining, please visit later!
							</strong>
						</h1>
						<p>网站维护中, 请稍等片刻，我们会为您尽快恢复服务!</p>
					</div>
				</body>
			</html>
EOF;
		return false;
	}
	
	// 定义要用到的语言
	$language_title = "iCopy-X Update";
	$language_nav_titile = "iCopy-X";
	$language_nav_item1 = "Task Main";
	$language_nav_item2 = "Update Logs";
	$language_nav_button = "Switch";
	$language_changelog_title = "iCopy-X firmware update logs";
	$language_btn_create_task = "Create Generate Task";
	$language_btn_query_histroy = "Query History";
	
	$language_create_task_input1 = "Device S/N";
	$language_create_task_input2 = "Email";
	$language_create_task_input3 = "Captcha";
	$language_create_task_input1_tips = "Enter your serial number";
	$language_create_task_input2_tips = "Enter your Email";
	$language_create_task_input3_tips = "Fill in captcha";
	
	$language_new = "New";
	$language_help = "Update Instruction";
	$language_generate = "Generate";
	$language_query = "Query";
	$language_requesting = "Requesting";
	
	$language_update_help_title = "iCopy-X Update Help";
	$language_update_help_items = array(
		"Step 1: Enter the device S/N (found under the “About” menu) on the website and download the upgrade package to your PC.",
		"Step 2: Connect the iCopy-X to your computer using the supplied USB TYPE C cable and delete any files that end in “.ipk” from the root directory.",
		"Step 3: Copy the newly downloaded upgrade package to the root directory.",
		'Step 4: Press "Ok" on the second page of the "About" menu on the iCopy-X to start the automatic upgrade.',
		"TIP: Ensure that the serial number has been entered correctly before starting as this could cause the upgrade to fail."
	);
	
	$language_close = "Close";
	
	$language_msg_log_request_failed = "Get update logs failed.";
	$language_msg_query_data_failed = "Query data failed";
	$language_msg_hash_no_exists = "Hash no exists";
	$language_msg_hash_code_error = "Hash code error";
	$language_msg_sn_clear_error = "S/N clear failed";
	$language_msg_unknown_response = "Unknown response";

	$language_msg_invalid_email = "Invalid email";
	$language_msg_invalid_sn = "Invalid S/N";
	$language_msg_invalid_captcha = "Invalid captcha";
	$language_msg_captcha_error = "Captcha error";
	$language_msg_server_error = "Server error";
	$language_msg_repeat_submit = "Request submitted, Do not do repeat request on the same day.";
	$language_msg_request_failed = "Request failed";
	$language_msg_request_sn_status_except = "Exception occurred when requesting S/N status!";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $language_title;?></title>
	    <link rel="stylesheet" href="./css/bootstrap.min.css">
	    <script src="./js/jquery.min.js"></script>
	    <script src="./js/bootstrap.min.js"></script>
		<style>
			.btn-captcha {
				margin-top: 15px; 
				display: inline-block; 
				margin-left: 8px;
			}
		</style>
	</head>
	<body>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
			    <div class="navbar-header">
			        <button type="button" class="navbar-toggle" data-toggle="collapse"
			                data-target="#page-index-navbar-collapse">
			            <span class="sr-only"><?php echo $language_nav_button;?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
			        </button>
			        <a class="navbar-brand" href="https://icopy-x.com/"><?php echo $language_nav_titile;?></a>
			    </div>
			    <div class="collapse navbar-collapse" id="page-index-navbar-collapse">
					<!-- 这个是一个神奇的标签页 -->
					<ul class="nav navbar-nav" role="tablist">
					    <li role="presentation" class="active"><a href="#tab-main" role="tab" data-toggle="tab"><?php echo $language_nav_item1;?></a></li>
					    <li role="presentation"><a href="#tab-logs" role="tab" data-toggle="tab"><?php echo $language_nav_item2;?></a></li>
					</ul>
			    </div>
			</div>
		</nav>
		<div class="tab-content">
			<!-- 日志刷新区域 -->
			<div class="panel panel-default tab-pane" style="box-shadow: none; border: none;" role="tabpanel" id="tab-logs">
				<h1 style="margin-top: 30px; margin-left: 30px;">
					<?php echo $language_changelog_title;?>
					<hr/>
				</h1>
				<div id="container_logs" class="panel-body" style="margin-top: 0px; margin-left: 48px;"></div>
			</div>
			<!-- 信息输入区域 -->
			<div id="tab-main" class="tab-pane active" role="tabpanel" style="width: 347px; margin:88px auto;">
				<!-- 这个是一个神奇的标签页 -->
				<ul class="nav nav-pills" role="tablist">
				    <li role="presentation" class="active">
						<a href="#tab1" role="tab" data-toggle="tab" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
							<?php echo $language_btn_create_task;?>
						</a>
					</li>
				    <li role="presentation">
						<a href="#tab2" role="tab" data-toggle="tab" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
							<?php echo $language_btn_query_histroy;?>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<!-- 这是两个普通的表单 -->
					<div class="panel panel-default tab-pane active" role="tabpanel" id="tab1" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
						<div class="panel-body" style="padding-bottom: 0px;">
							<form class="form-horizontal" role="form" action="ota_queue_add.php" method="post">
							  <div class="form-group">
							    <label for="firstname" class="col-sm-2 control-label"><?php echo $language_create_task_input1;?></label>
							    <div class="col-sm-10">
							      <input id="serialnumber" type="text" class="form-control" name="serialnumber" placeholder="<?php echo $language_create_task_input1_tips;?>">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="lastname" class="col-sm-2 control-label"><?php echo $language_create_task_input2;?></label>
							    <div class="col-sm-10">
							      <input id="E-mail" type="text" class="form-control" name="E-mail" placeholder="<?php echo $language_create_task_input2_tips;?>">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="lastname" class="col-sm-2 control-label"><?php echo $language_create_task_input3;?></label>
							    <div class="col-sm-10">
									<input id="code" type="text" class="form-control" name="code" placeholder="<?php echo $language_create_task_input3_tips;?>">
									<div style="margin-top: 6px;">
										<img id="captcha_img" border='1' src='./tools/captcha.php?' style="width:100px; height:30px;"/>
										<a onclick="document.getElementById('captcha_img').src='./tools/captcha.php'" class="btn-captcha" id="btn-captcha" style="cursor:pointer">
											<?php echo $language_new;?>
										</a>
									</div>
							    </div>
							  </div>
							  <div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10" style="text-align: right; margin-top: 38px;">
									<!-- 按钮触发模态框 -->
									<button class="btn btn-default" data-toggle="modal" data-target="#page-index-dialog" type="button"><?php echo $language_help;?></button>
									<button class="btn btn-default" type="button" id="submit" style="margin-left: 16px;"><?php echo $language_generate;?></button>
							    </div>
							  </div>
							</form>
						</div>
					</div>
					
					<div class="panel panel-default tab-pane" role="tabpanel" id="tab2" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
						<div class="panel-body" style="padding-bottom: 0px;">
							<form class="form-horizontal" role="form" action="get_fw_history.php" method="get">
							  <div class="form-group">
							    <label for="firstname" class="col-sm-2 control-label"><?php echo $language_create_task_input1; ?></label>
							    <div class="col-sm-10">
							      <input id="serialnumber" type="text" class="form-control" name="sn" placeholder="<?php echo $language_create_task_input1_tips;?>">
							    </div>
							  </div>
							  <div class="form-group">
							    <label for="lastname" class="col-sm-2 control-label"><?php echo $language_create_task_input2; ?></label>
							    <div class="col-sm-10">
							      <input id="E-mail" type="text" class="form-control" name="E-mail" placeholder="<?php echo $language_create_task_input2_tips;?>">
							    </div>
							  </div>
							  <div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10" style="text-align: right; margin-top: 38px;">
									<button class="btn btn-default" type="submit" id="query"><?php echo $language_query; ?></button>
							    </div>
							  </div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- 模态框（Modal） -->
		<div class="modal fade" id="page-index-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">
							<?php echo $language_update_help_title; ?>
						</h4>
					</div>
					<div class="modal-body">
						<?php 
							foreach($language_update_help_items as $item) {
							    echo "<p>" . $item . "</p>";
							}
						?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $language_close; ?></button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal -->
		</div>
	</body>
	
	<script>
		/*
		 * 加载日志到页面中
		 */
		function loadLogs() {
		    var timestamp = new Date().getTime();
		    var req_url = 'get_changelog.php' + '?ts=' + timestamp;
			$.ajax({
				url: req_url,
				type: 'get',                 // 获取数据方式:post/get          
				async: false,                  // 加载方式默认异步,true为同步
				dataType: 'json',            // 数据格式
				cache:false,                //这里
                ifModified :true ,           //这里
				success: function (res) {
					// 取出数据
					var data = res;// 后台返回数据
					var logs = "";
					// 迭代器
					$.each(data, function (i, item) {//进行循环
						// console.log(item)
						logs += 
							// 先拼接头部
							"<h3>" + item.version + " <code> " + item.date + "</code></h3>" + "<ul>";
							// 二次迭代,迭代每个更新的项目
							$.each(item.item, function (i, msg) {
								logs += "<li>" + msg + "</li>"
							});
							logs += "</ul> ";
					});
					$('#container_logs').html(logs);
				},
				error: function (obj) {
					alert("<?php echo $language_msg_log_request_failed; ?>");
				},
			});
		}
		
		/*
		 * 根据返回的HASH查询SN是否存在这个最终的结果
		 * 如果成功，则说明SN存在，否则说明SN不存在
		 * @param onSnChk: 回调函数，回调SN存在或者不存在, 带有一个参数 true or false;
		 * @param onErr: 回调函数，在请求异常时回调，带有一个参数obj
		 */
		function waitSNCheck(hash, onSnChk, onErr) {
			// 内部闭包，直接无参递归调用SN状态检查函数
			function waitSNCheck_closePack() {
				waitSNCheck(hash, onSnChk, onErr);
			}
			
			// 一定要异步请求	
			$.ajax({
				url: 'get_sn_state.php',
				type: 'post',                 // 获取数据方式:post/get          
				async: false,                  // 加载方式默认异步,true为同步
				dataType: 'json',            // 数据格式
				data: { "hash": hash },
				success: function (obj) {
					if (obj.code == 0) { // 该HASH指向的任务已经出结果
						if (obj.msg == 3) {  // 状态码为3，SN不存在
							onSnChk(false);
						} else {
							onSnChk(true);
						}
					} else if (obj.code == 1) {
						alert("<?php echo $language_msg_query_data_failed; ?>");
					} else if (obj.code == 2) {
						alert("<?php echo $language_msg_hash_no_exists; ?>");
					} else if (obj.code == 3) {
						// 继续等待SN处置的状态
						setTimeout(waitSNCheck_closePack, 1024);
					} else if (obj.code == 4) {
						alert("<?php echo $language_msg_hash_code_error; ?>");
					} else if (obj.code == 5) {
						// alert("删除无效的SN数据失败");
						alert("<?php echo $language_msg_sn_clear_error; ?>")
					} else {
						alert("<?php echo $language_msg_unknown_response; ?>");
					}
				},
				error: onErr
			});
		}
		
		
		/**
		 * 启用更新按钮
		 * 
		 * */
		function enableUpdateBtn(enable){
			if (enable) {
				// 此处我们自动刷新验证码
				$("#btn-captcha").click()
				$("#submit").text("<?php echo $language_generate;?>")
			} else {
				$("#submit").text("<?php echo $language_requesting;?>")
			}
			$("#submit").attr('disabled', !enable);
		}
		
		/**
		* 处理更新固件的实际交互逻辑的函数 
		* */
		function ajaxOnUpdateSuccess(res) {
			console.log(res);
			var ret_code = res.code;
			var ret_msg = res.msg;
			var btn_enable = true;
			
			if (ret_code == 1) {
				alert("<?php echo $language_msg_invalid_email; ?>");
			} else if (ret_code == 2) {
				alert("<?php echo $language_msg_invalid_sn; ?>");
			} else if (ret_code == 3) {
				alert("<?php echo $language_msg_invalid_captcha; ?>");
			} else if (ret_code == 4) {
				alert("<?php echo $language_msg_captcha_error; ?>");
			} else if (ret_code == 5) {
				alert("<?php echo $language_msg_server_error; ?>");
			} else if (ret_code == 6) {
				alert("<?php echo $language_msg_repeat_submit; ?>");
			} else if (ret_code == 7) {
				alert("<?php echo $language_msg_request_failed; ?>");
			} else if (ret_code == 0) {
				btn_enable = false;
				console.log("任务提交成功，HASH码：", ret_msg)
				// 提交任务成功了，我们还需要继续检测一下SN是否有效
				// 如果无效，我们应当直接提醒用户当前SN无效
				// 如果有效,我们则直接跳转到一个页面，告知用户开始生产
				// 并且需要显示当前用户排队的进度，就是排到了第几位，前面还有几位。。。
				waitSNCheck(
					ret_msg,
					function (ret) {
						if (ret) {
							// SN有效，那么任务就开始了，此时我们根据HASH跳转到排队页面
							window.location.href = `go_queuing.php?hash=${ret_msg}`;
						} else {
							// SN无效，此时应当提醒用户，SN无效这个事情
							alert("<?php echo $language_msg_invalid_sn; ?>");
						}
						enableUpdateBtn(true);
					},
					function (obj) {
						alert("<?php echo $language_msg_request_sn_status_except; ?>");
						enableUpdateBtn(true);
					}
				);
			} else {
				alert("<?php echo $language_msg_unknown_response; ?>");
			}
			
			if (btn_enable) {
				enableUpdateBtn(true);
			}
		}
		
		/**
		 * 基础输入校验
		 * */
		 function chkInput() {
			 // 校验结果
			 var ret = true;
			 // 取出用户输入的数据
			 var sn = $("#serialnumber").val()
			 var email = $("#E-mail").val()
			 var code = $("#code").val()
			 
			 if (sn.length != 8){
			 	alert("<?php echo $language_msg_invalid_sn; ?>");
			 	ret = false;
			 } else
			 if (email.length == 0){
			 	alert("<?php echo $language_msg_invalid_email; ?>");
			 	ret = false;
			 } else
			 if (code.length != 4){
			 	alert("<?php echo $language_msg_invalid_captcha; ?>");
			 	ret = false;
			 }
			 return  {
				 "sn": sn,
				 "email": email,
				 "code": code,
				 "ret": ret,
			 };
		 }
		
		/**
		 * 设置点击开始更新的按键操作
		 * */
		function setOnUpdate() {
			$("#submit").click(function() {
				var chkDict = chkInput()
				if (chkDict['ret']) {
					// 取出数据
					sn = chkDict['sn'];
					email = chkDict['email'];
					code = chkDict['code'];
					
					// 禁用UI
					enableUpdateBtn(false);
					
					// 开启AJAX
					$.ajax({
						url: 'ota_queue_add.php',
						type: 'post',                 // 获取数据方式:post/get          
						async: false,                  // 加载方式默认异步,true为同步
						dataType: 'json',            // 数据格式
						data: {
							"serialnumber": sn,
							"E-mail": email,
							"code": code
						},
						success: ajaxOnUpdateSuccess,
						error: function (obj) {
							alert("<?php echo $language_msg_request_failed; ?>");
							enableUpdateBtn(true);
						}
					});
				}
				return false;
			});
			
		}
		
		// 加载日志
		loadLogs();
		// 设置开始更新的事件
		setOnUpdate();
	</script>
	
</html>
