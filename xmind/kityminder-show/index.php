<?php
session_start();
?>
<html>

<head>
	<meta charset="utf-8">
	<title>心智圖</title>
	<link rel="stylesheet" href="src/kityminder.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			height: 100%;
		}

		#minder-view {
			position: absolute;
			border: 1px solid #ccc;
			left: 10px;
			top: 10px;
			bottom: 10px;
			right: 10px;
			height: 90%;
			margin-top: 40px;
		}

		#controllerDiv {
			margin-top: 5px;
			margin-left: 10px;
			width: 98%;
			height: 40px;
			background: #AAFFEE;
			padding-top: 7px;
			padding-left: 5px;
		}
	</style>
	<script type="text/javascript" src='../js/jquery-1.12.1.min.js'></script>
	<script type="text/javascript" src="bower_components/kity/dist/kity.min.js"></script>

</head>

<body>
	<script id="minder-view" type="application/kityminder" minder-data-type="json">
		{
        "root": {
            "data": {
                "text": "主題"
            }
        }
    }
    </script>
	<div id="controllerDiv">
		移動
		<button type="button" class="btn btn-sm btn-default" onclick="move('up')"><span class="glyphicon glyphicon-arrow-up"></span></button>
		<button type="button" class="btn btn-sm btn-default" onclick="move('down')"><span class="glyphicon glyphicon-arrow-down"></span></button>
		<button type="button" class="btn btn-sm btn-default" onclick="move('left')"><span class="glyphicon glyphicon-arrow-left"></span></button>
		<button type="button" class="btn btn-sm btn-default" onclick="move('right')"><span class="glyphicon glyphicon-arrow-right"></span></button>
		&nbsp;縮放
		<button type="button" class="btn btn-sm btn-primary" onclick="zoom('ZoomIn')"><span class="glyphicon glyphicon-zoom-in"></span></button>
		<button type="button" class="btn btn-sm btn-primary" onclick="zoom('ZoomOut')"><span class="glyphicon glyphicon-zoom-out"></span></button>
		&nbsp;展開
		<button type="button" class="btn btn-sm btn-success" onclick="openUp()"><span class="glyphicon glyphicon-resize-full"></span></button>
		&nbsp;收起
		<button type="button" class="btn btn-sm btn-success" onclick="packUp()"><span class="glyphicon glyphicon-resize-small"></span></button>
		&nbsp;主題
		<button type="button" class="btn btn-sm btn-warning" onclick="changeTemplate('default')">心智圖</button>
		<button type="button" class="btn btn-sm btn-warning" onclick="changeTemplate('right')">靠右</button>
	</div>
</body>
<script type="text/javascript" src="dist/kityminder.core.js"></script>
<script>
	// global var	
	var host = "http://localhost:8888";
	var endpoint = "/mind-back/public/api";
	// 创建 km 实例
	/* global kityminder */
	var km = new kityminder.Minder();

	km.setup('#minder-view');
	var mindArray = [];

	id = "<?php echo $_GET['id']; ?>";
	$.ajax({
		type: "GET",
		url: host + endpoint + '/minds/' + id,
		cache: true,
		error: function() {
			alert("載入失敗");
		},
		success: function(data) {
			console.log(data.data);
			// mindArray = JSON.parse(data);
			document.title = data.data.root.data.text;
			km.importData('json', JSON.stringify(data.data));
			// //km.disable();
			km.execCommand('Hand');
		}
	});

	function openUp() {
		km.execCommand('ExpandToLevel', 100);
	} // 展開
	function packUp() {
		km.execCommand('ExpandToLevel', 1);
	} // 收起
	function move(dir) {
		km.execCommand('Move', dir, 1000);
	}

	function zoom(dir) {
		km.execCommand(dir);
	}

	function changeTemplate(template) {
		km.execCommand('Template', template);
	}
</script>

</html>