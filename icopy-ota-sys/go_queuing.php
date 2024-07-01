<?php 
	include './tools/utils.php';
	// 我们需要在此处先加载一些必要的变量！
	
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
	    $hash = format_input($_GET["hash"]);
		if (!is_hash($hash)) {
			resp_client(-1, "错误的HASH");
			return false;
		}
	} else {
		resp_client(-1, "未知的请求");
		return false;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>ICopy-X Update Queue</title>
	    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
	    <script src="./js/jquery.min.js"></script>
	    <script src="./js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/common.css"/>
		<style>
			.turn {
			      animation:turn 8s linear infinite;      
			    }
			    /* 
			      turn : 定义的动画名称
			      1s : 动画时间
			      linear : 动画以何种运行轨迹完成一个周期
			      infinite :规定动画应该无限次播放
			     */
			    @keyframes turn{
			      0%{-webkit-transform:rotate(0deg);}
			      25%{-webkit-transform:rotate(90deg);}
			      50%{-webkit-transform:rotate(180deg);}
			      75%{-webkit-transform:rotate(270deg);}
			      100%{-webkit-transform:rotate(360deg);}
			}
			
			.center-container {
				width: 1080px;
				left: auto;
				right: auto;
				margin: auto;
				background-color: #333333;
				border-radius: 16px;
				position: relative;
			}
		</style>
	</head>
	<body>
		<div class="center-container">
			<div style="margin-top: 40px; position: relative;">
				<br />
				<h1 id="id-title" style="color: #FFCC33; margin-left: 28px;"><strong>Your firmware is queuing...</strong></h1>
				<img src="./img/build.svg" class="turn" style="margin: 100px auto; margin-left: 48px; width: auto;"/>
				<p id="id-tips" style="text-indent: 2; color: #FFCC33; margin: 24px; font-size: large;">
					Firmware is building in progress. <br>The generating time will normally take about 30 seconds.<br/>
					If there is a queue due to the large volume, you might have to wait. <br/>
					You can close the browser and return to this page at any time. <br/>
					The system will save your firmware for a period of time, depending on the amount of access to the ota system..
				</p>
				<p id="id-msg" style="text-align: right; color: #FFCC33; bottom: 0px; right: 32px; font-size: large; position: absolute;"></p>
				<br />
			</div>
		</div>
	</body>
	
	<script>
		/**
		 * 在检查构建结果成功时的请求回应处理函数
		 * */
		function onChkBuildRequestSuc(res) {
			console.log("应答数据: ", res);
			var nextChk = true;
			
			// 如果返回码是2，说明返回了工作列表的数据，也就是说，该任务正在处理中或者排队中
			if (res.code == 2) {
				// 获取当前正在排队的状态信息
				var progress = res.msg.PROGRESS;
				var position = res.msg.POSITION;
				if (position > 0) {
					// 刷新显示到UI上 TODO
					$("#id-msg").text("Queuing now，have " + position + " task wait before you.");
					$("#id-title").text("Your firmware is queuing...");
				} else {
					$("#id-msg").text("Building, You can download it later.");
					$("#id-title").text("Your firmware is building...");
				}
			} else if (res.code == 1) {
				// 如果返回码是1，有两种情况
				// 第一种就是该任务已经处理完成，可以获得处理结果了
				// 第二种就是该任务刚被创建，还未成功加入到任务队列中，需要再等待下
				// 出现这种情况的时候，我们需要判断任务处于的等待队列的状态
				
				var add_time = res.msg.ADD_TIME;
				var state = res.msg.CURRENT_STATE;
				var ok_time = res.msg.OK_TIME;
				var sn = res.msg.SN;
				
				if (state == 1 || state == 2) {
					var nowtime = new Date().valueOf()
					var msg_download_html = `<a href="download_ipk.php?hash=<?php echo $hash ?>&_t=` + nowtime + `" style="color: #336699; text-decoration:underline;">Download File</a>`;
					var msg_str = `S/N: ${sn}` + "<br/>";
						msg_str += `Start Time: ${add_time}` + "<br/>";
						msg_str += `End Time  : ${ok_time}` + "<br/><br/>";
						msg_str += msg_download_html;
					
					$("#id-title").text("Build Finish");
					$("#id-msg").html(msg_str);
					$("#id-tips").hide()
					$("img").removeClass("turn");
					
					nextChk = false;
				} else {   // 这个时候还在等待加入列表2，也就是python端还在处理
					$("#id-msg").text("Queuing...");
					$("#id-title").text("Your firmware is queuing...");
				}
				
			} else if (res.code == 0) {  // 如果返回码是0，说明该HASH指向的任务不存在，可能是各种奇奇怪怪的问题
				alert("Hash error")
			} else {
				console.log("未知的返回码！");
			}
			
			if (nextChk) {  // 需要继续进行下一轮的查询
				setTimeout(startChkBuild, 2333)
			}
		}
		
		/**
		 * 开启定时查询的任务
		 * */
		 function startChkBuild() {
			 // 开启AJAX
			 $.ajax({
			 	url: 'get_job_state.php',
			 	type: 'get',                 // 获取数据方式:post/get          
			 	async: false,                  // 加载方式默认异步,true为同步
			 	dataType: 'json',            // 数据格式
			 	data: {
			 		"hash": "<?php echo $hash ?>",
                    _t: new Date().valueOf() //加时间戳，解决缓存问题
			 	},
			 	success: onChkBuildRequestSuc,
			 	error: function (obj) {
			 		alert("Reuqest failed");
			 	}
			 });
		 }
		 
		 // 在此处，我们直接开启定时查询任务！
		 startChkBuild();
	</script>
	
</html>
