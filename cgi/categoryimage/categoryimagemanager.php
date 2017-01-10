<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>相册管理</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="jquery/jquery.min.js"></script>
		
		<script src="jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="jquery/jquery-ui.theme.min.css" />
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome.min.css" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		
		<script src="My97DatePicker/WdatePicker.js"></script>
		<script src="My97DatePicker/lang/zh-cn.js"></script>
		
		<script src="common/pager.js"></script>
		
		<link rel="stylesheet" href="common/common.css" />
		<script src="common/common.js"></script>
		
		<script src="common/template.js"></script>

		<script src="common/config.js"></script>
		
		<link href="resource/categoryimage/categoryimagemanager.css"  rel="stylesheet"/>
		<script src="resource/categoryimage/categoryimagemanager.js"></script>
		
		<script>
			$(function(){
				doQuery();
			});
		</script>
	</head>
	<body>
		<?php
			Render('header');
		?>
		<div class="container">
			<div id="main">
				<div class="col-md-12">
					<form id="mainForm" class="form-inline" action="<?php ActionLink('categoryimage','categoryimagemanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="20" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<input id="PageTemplate" type="hidden" value="categoryimagelist" />
						<input id="PageItems" name="PageItems" type="hidden" value="Id,Title,Tag,Img,Src,Level,Status,DateTimeCreate,DateTimeModify" />
		
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-12 clearfix">
										<div class="btn-group f_l">
											<button class="btn btn-sm btn-info" type="button" onclick="categoryImageEditor(this,0)">添加</button>
											<button class="btn btn-sm btn-success" type="button" onclick="categoryImageChangeStatus(this,0,1)">启用</button>
											<button class="btn btn-sm btn-warning" type="button" onclick="categoryImageChangeStatus(this,0,0)">禁用</button>
											<button class="btn btn-sm btn-danger" type="button" onclick="categoryImageDelete(this,0)">删除</button>
										</div>
										<div class="f_r">
											<div class="form-group">
												<input id="Id" name="Id" type="text" class="form-control input-sm wd120" placeholder="编号" />
											</div>
											<div class="form-group">
												<input id="Title" name="Title" type="text" class="form-control input-sm wd120" placeholder="标题" />
											</div>
											<div class="form-group">
												<input id="Tag" name="Tag" type="text" class="form-control input-sm wd120" placeholder="标签" />
											</div>
											<div class="form-group">
												<select id="Level" name="Level" class="form-control input-sm" placeholder="等级">
													<option value="">全部</option>
													<script type="text/javascript">
													var categoryImageLevel = EnumConfig().CategoryImageLevel;
													for(var i in categoryImageLevel){
														var o = categoryImageLevel[i];
														document.write('<option value="'+o.Key+'">'+o.Value+'</option>');
													}
													</script>
												</select>
											</div>
											<div class="form-group">
												<input id="DateTimeModifyMin" name="DateTimeModifyMin" class="form-control input-sm date Wdate wd100" placeholder="开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'DateTimeModifyMax\')}'});" />
												<input id="DateTimeModifyMax" name="DateTimeModifyMax" class="form-control input-sm date Wdate wd100" placeholder="结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'DateTimeModifyMin\')}'});" />
											</div>
											<div class="form-group">
												<select id="Status" name="Status" class="form-control input-sm" placeholder="状态">
													<option value="">全部</option>
													<option value="1">启用</option>
													<option value="0">禁用</option>
												</select>
											</div>
											<div class="form-group">
												<select id="Scrawled" name="Scrawled" class="form-control input-sm" placeholder="采集状态">
													<option value="">全部</option>
													<option value="0">未采集</option>
													<option value="1">已采集</option>
												</select>
											</div>
											<div class="form-group">
												<button type="submit" class="btn btn-info btn-sm btn-query">查询</button>
												<button type="button" class="btn btn-default btn-sm btn-refresh">刷新</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<thead id="recordHead">
										<tr>
											<th class="t_c wd40"><input type="checkbox" onchange="changeAllCheckBoxStatus(this);" /></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Title"> 标题</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Tag"> 标签</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Level"> 等级</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Img"> 采集图片</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Src"> 本地图片</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Status"> 状态</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="DateTimeModify"> 时间（创建/修改）</a></th>
											<th class="t_c wd200"><button type="button" class="btn btn-sm btn-default" onclick="changePageTemplate(this,event);"><span class="glyphicon glyphicon-th-list"></span>图块</button></th>
										</tr>
									</thead>
									<tbody id="recordList">
									</tbody>
									<tfoot id="recordStatic">
										<tr>
											<td colspan="9" class="t_r"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
			Render('footer');
		?>
	</body>
</html>