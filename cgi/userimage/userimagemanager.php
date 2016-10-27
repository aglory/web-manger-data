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
		<title>图片管理</title>
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
		
		<link href="resource/userimage/userimagemanager.css"  rel="stylesheet"/>
		<script src="resource/userimage/userimagemanager.js"></script>
		
		<script src="resource/user/userdialog.js"></script>
		
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
					<form id="mainForm" class="form-inline" action="<?php ActionLink('userimage','userimagemanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="20" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<input id="User_Id" name="User_Id" type="hidden" value="<?php if(array_key_exists('User_Id',$_GET) && is_numeric($_GET['User_Id'])){ echo $_GET['User_Id'];}?>"/>
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-2 t_l">
										<div class="form-group">
											<button class="btn btn-sm btn-info" type="button" onclick="userImageEditor(this,0)">添加</button>
										</div>
									</div>
									<div class="col-sm-10 t_r">
										<div class="form-group">
											<input id="Name" name="Name" type="text" class="form-control input-sm wd120" placeholder="名字" />
										</div>
										<div class="form-group">
											<input id="NickName" name="NickName" type="text" class="form-control input-sm wd120" placeholder="昵称" />
										</div>
										<div class="form-group">
											<select id="Sex" name="Sex" class="form-control input-sm" placeholder="性别">
												<option value="">全部</option>
												<option value="0">女</option>
												<option value="1">男</option>
											</select>
										</div>
										<div class="form-group">
											<input id="DateTimeModifyStart" name="DateTimeModifyStart" class="form-control input-sm date Wdate wd100" placeholder="开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'DateTimeModifyEnd\')}'});" />
											<input id="DateTimeModifyEnd" name="DateTimeModifyEnd" class="form-control input-sm date Wdate wd100" placeholder="结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'DateTimeModifyStart\')}'});" />
										</div>
										<div class="form-group">
											<select id="Status" name="Status" class="form-control input-sm" placeholder="性别">
												<option value="">全部</option>
												<option value="1">启用</option>
												<option value="0">禁用</option>
											</select>
										</div>
										<div class="form-group">
											<select id="$User_Id" name ="$User_Id" class="form-control input-sm">
												<option value="">全部</option>
												<?php if(array_key_exists('User_Id',$_GET) && is_numeric($_GET['User_Id'])){ ?>
													<option selected="selected" value="1">已分配</option>
													<option value="-1">未分配</option>
												<?php }else{?>
													<option value="1">已分配</option>
													<option selected="selected" value="-1">未分配</option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group">
											<button type="submit" class="btn btn-info btn-sm btn-query">查询</button>
											<button type="button" class="btn btn-default btn-sm btn-refresh">刷新</button>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="tbUserInfo.Name"> 名称</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="tbUserImageInfo.IsDefault"> 是否为默认图片</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="tbUserImageInfo.OrderNumber"> 图片序号</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="tbUserImageInfo.DateTimeModify"> 时间（创建/修改）</a></th>
										</tr>
									</thead>
									<tbody id="recordList">
									</tbody>
									<tfoot id="recordStatic">
										<tr>
											<td colspan="4" class="t_r"></td>
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