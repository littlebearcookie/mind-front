<!DOCTYPE html>
<html lang="en">

<head>
	<title>心智圖</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="lib/bootstrap/css/bootstrap-3.4.0.min.css">
	<script src="lib/jquery-3.4.1.min.js"></script>
	<script src="lib/bootstrap/js/bootstrap-3.4.0.min.js"></script>
	<script src="lib/xmind/xmind.js"></script>
	<script type="text/javascript" src="lib/xmind2json/xml2json.js"></script>
	<script type="text/javascript" src="lib/xmind2json/jszip.js"></script>
	<style>
		h2 {
			text-align: center;
		}

		td,
		th {
			text-align: center;
		}

		td>button {
			margin-right: 5px;
		}
	</style>
	<script>
		$(document).ready(function() {
			getMinds();
		});
		var host = "http://localhost:8888";
		var endpoint = "/mind-back/public/api";
		// 獲取所有心智圖
		function getMinds() {
			$.ajax({
				type: "GET",
				url: host + endpoint + '/minds',
				cache: true,
				error: function() {
					alert("載入失敗");
				},
				success: function(data) {
					var minds = data.data;
					var html = "";
					data.data.forEach(mind => {
						html += "<tr style='height:40px;'>" +
							"<td style='text-align:left'>" + mind.text + "</td>" +
							"<td>" +
							"<button type='button' class='btn btn-xs btn-success' onclick='editMind(" + mind.id + ")'>編輯</button>" +
							"<button type='button' class='btn btn-xs btn-danger' onclick='delMind(" + mind.id + ")'>刪除</button>" +
							"<button type='button' class='btn btn-xs btn-primary' onclick='showMind(" + mind.id + ")'>查看</button>" +
							"<button type='button' class='btn btn-xs btn-info' onclick='exportMind(" + mind.id + ")'>匯出</button> " +
							"</td>" +
							"</tr>";
					});
					$("#mindBox").html(html);
				}
			});
		}
		// 新增心智圖
		function addMind() {
			window.open("xmind/kityminder-editor/index.php?id=new", "", config = 'height=800,width=1400');
		}
		// 編輯心智圖
		function editMind(id) {
			window.open('xmind/kityminder-editor/index.php?id=' + id, '', config = 'height=800,width=1400');
		}
		// 刪除心智圖
		function delMind(id) {
			$.ajax({
				type: "DELETE",
				url: host + endpoint + '/minds/' + id,
				cache: true,
				error: function() {
					alert("載入失敗");
				},
				success: function(data) {
					alert("刪除成功");
					getMinds();
				}
			});
		}
		// 查看心智圖
		function showMind(id) {
			window.open("xmind/kityminder-show/index.php?id=" + id, id, config = 'height=800,width=1200,left=150');
		}
		// 匯出心智圖
		function exportMind(id) {
			var workbook = new xmind.Workbook();
			var primarySheet = workbook.getPrimarySheet();
			var root = primarySheet.getRootTopic();
			var output = {};
			$.ajax({
				type: "GET",
				url: host + endpoint + '/minds/' + id + "/export",
				cache: true,
				error: function() {
					alert("載入失敗");
				},
				success: function(data) {
					root.setTitle(data.data[0].text);
					data.data.forEach(branch => {
						if (branch.level == 1) {
							output[branch.id] = root.addChild({
								id: branch.id,
								title: branch.text
							});
						} else if (branch.level > 1) {
							output[branch.id] = output[branch.parent].addChild({
								id: branch.id,
								title: branch.text
							});
						}
					});
					xmind.save(workbook, data.data[0].text + ".xmind");
				}
			});
		}
	</script>
</head>

<body>
	<div class="container" style="text-align:center;">
		<h2>心智圖</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th colspan="3" style="text-align:right;">
						<button type="button" class="btn btn-sm btn-success" onclick="addMind()">
							<span class="glyphicon glyphicon-plus"></span>新增心智圖
						</button>
					</th>
				</tr>
				<tr>
					<th colspan="3" style="text-align:left">
						匯入心智圖檔案：<input type="file" style="display:inline" id="xmind_file" name="xmind_file" multiple />
						<button type="button" class="btn btn-xs btn-info" onclick="importMind()">匯入</button>
						</td>
				</tr>
				<tr>
					<th style="width:60%;">名稱</th>
					<th style="width:40%;">功能</th>
				</tr>
			</thead>
			<tbody id="mindBox">
			</tbody>
		</table>
	</div>
	<!-- 匯入心智圖的 JavaScript -->
	<script type="text/javascript">
		var files = [];
		$("#xmind_file").on("change", function(evt) {
			files = evt.target.files;
		});

		function importMind() {
			for (var i = 0; i < files.length; i++) {
				handleFile(files[i]);
			}
		}

		function handleFile(f) {
			var dateBefore = new Date();
			var x2js = new X2JS();

			JSZip.loadAsync(f).then(function(zip) {
					var dateAfter = new Date();
					zip.forEach(function(relativePath, zipEntry) { // 2) print entries
						if (zipEntry.name == "content.xml") {
							zip.file(zipEntry.name).async('string').then(function success(text) {
									var input = x2js.xml_str2json(text);
									console.log(input);
									$.ajax({
										type: "POST",
										url: host + endpoint + '/minds/import',
										cache: true,
										data: {
											MindOut: input['xmap-content'].sheet.topic,
										},
										error: function() {
											alert("載入失敗");
										},
										success: function(data) {
											alert("匯入成功！");
											getMinds();
										},
										beforeSend: function() {
											// $('#loadingDiv').show();
										}
									});
								},
								function error(e) {
									alert("錯誤，請確認檔案是否正確。");
								});
						}
					});
				},
				function(e) {
					alert("錯誤，請確認檔案是否正確。");
				});
		}
	</script>
</body>

</html>