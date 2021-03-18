<?php
session_start();
?>
<html>

<head>
	<meta charset="utf-8">
	<title>心智圖</title>

	<!-- 	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon"> -->

	<!-- bower:css -->
	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css" />
	<link rel="stylesheet" href="bower_components/codemirror/lib/codemirror.css" />
	<!--     <link rel="stylesheet" href="bower_components/hotbox/hotbox.css" /> -->
	<link rel="stylesheet" href="bower_components/kityminder-core/dist/kityminder.core.css" />
	<link rel="stylesheet" href="bower_components/color-picker/dist/color-picker.css" />
	<!-- endbower -->

	<link rel="stylesheet" href="dist/kityminder.editor.css">

	<style>
		html,
		body {
			margin: 0;
			padding: 0;
			height: 100%;
			overflow: hidden;
		}

		h1.editor-title {
			background: #393F4F;
			color: white;
			margin: 0;
			height: 40px;
			font-size: 14px;
			line-height: 40px;
			font-family: 'Hiragino Sans GB', 'Arial', 'Microsoft Yahei';
			font-weight: normal;
			padding: 0 20px;
		}
	</style>
</head>

<body ng-app="kityminderDemo" ng-controller="MainController">
	<kityminder-editor on-init="initEditor(editor, minder)"></kityminder-editor>
</body>

<!-- bower:js -->
<script src="bower_components/jquery/dist/jquery.js"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
<script src="bower_components/angular/angular.js"></script>
<script src="bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>
<script src="bower_components/codemirror/lib/codemirror.js"></script>
<script src="bower_components/codemirror/mode/xml/xml.js"></script>
<script src="bower_components/codemirror/mode/javascript/javascript.js"></script>
<script src="bower_components/codemirror/mode/css/css.js"></script>
<script src="bower_components/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="bower_components/codemirror/mode/markdown/markdown.js"></script>
<script src="bower_components/codemirror/addon/mode/overlay.js"></script>
<script src="bower_components/codemirror/mode/gfm/gfm.js"></script>
<script src="bower_components/angular-ui-codemirror/ui-codemirror.js"></script>
<script src="bower_components/marked/lib/marked.js"></script>
<script src="bower_components/kity/dist/kity.js"></script>
<script src="bower_components/hotbox/hotbox.js"></script>
<script src="bower_components/json-diff/json-diff.js"></script>
<script src="bower_components/kityminder-core/dist/kityminder.core.js"></script>
<script src="bower_components/color-picker/dist/color-picker.js"></script>
<script src="bower_components/seajs/dist/sea.js"></script>
<!-- endbower -->

<script src="ui/kityminder.app.js"></script>
<script src="ui/service/commandBinder.service.js"></script>
<script src="ui/service/config.service.js"></script>
<script src="ui/service/memory.service.js"></script>
<script src="ui/service/lang.zh-cn.service.js"></script>
<script src="ui/service/valueTransfer.service.js"></script>
<script src="ui/service/minder.service.js"></script>
<script src="ui/service/resource.service.js"></script>
<script src="ui/service/revokeDialog.service.js"></script>
<script src="ui/service/server.service.js"></script>
<script src="ui/filter/lang.filter.js"></script>
<!-- <script src="ui/dialog/imExportNode/imExportNode.ctrl.js"></script> -->
<script src="ui/directive/topTab/topTab.directive.js"></script> <!-- 上面那條功能 -->
<script src="ui/directive/undoRedo/undoRedo.directive.js"></script>
<script src="ui/directive/appendNode/appendNode.directive.js"></script>
<!-- <script src="ui/directive/arrange/arrange.directive.js"></script> 上移下移的功能-->
<script src="ui/directive/operation/operation.directive.js"></script>
<script src="ui/directive/noteEditor/noteEditor.directive.js"></script>
<script src="ui/directive/kityminderEditor/kityminderEditor.directive.js"></script>
<script src="ui/directive/fontOperator/fontOperator.directive.js"></script>
<script src="ui/directive/colorPanel/colorPanel.directive.js"></script>
<script src="ui/directive/navigator/navigator.directive.js"></script>
<script src="ui/directive/EnterButton/EnterButton.js"></script>

<script>
	// global var	
	var host = "http://localhost:8888";
	var endpoint = "/mind-back/public/api";
	angular.module('kityminderDemo', ['kityminderEditor'])
		.controller('MainController', function($scope) {
			$scope.initEditor = function(editor, minder) {
				//window.editor = editor;
				//window.minder = minder;
				var id = "<?php echo $_GET['id']; ?>";
				// 編輯
				if (id != "new") {
					$.ajax({
						type: "GET",
						url: host + endpoint + '/minds/' + id,
						cache: true,
						error: function() {
							alert("載入失敗");
						},
						success: function(data) {
							editor.minder.importJson(data.data);
						}
					});
				}
				//新增
				else {
					var mind = '{"root": {"data": {"text": "主題"}}}';
					mind = JSON.parse(mind);
					editor.minder.importJson(mind);
				}
			};
		});
	//儲存
	function xmind_sava() {
		var mindOut = editor.minder.exportJson(); //心智圖輸出
		var id = "<?php echo $_GET['id']; ?>";
		// 編輯
		if (id != "new") {
			$.ajax({
				type: "PUT",
				url: "http://localhost:8888/mind-back/public/api/minds/" + id,
				cache: true,
				data: {
					MindOut: mindOut,
				},
				error: function() {
					alert("載入失敗");
				},
				success: function(data) {
					alert("上傳成功");
					window.opener.location.href = ('../../index.php');
					window.close();
				}
			});

		}
		// 新增
		else {
			$.ajax({
				type: "POST",
				url: host + endpoint + '/minds',
				cache: true,
				data: {
					MindOut: mindOut,
				},
				error: function() {
					alert("載入失敗");
				},
				success: function(data) {
					alert("上傳成功");
					window.opener.location.href = ('../../index.php');
					window.close();
				}
			});
		}
	}
	// 關閉
	function xmind_close() {
		window.close();
	}
</script>

</html>