<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>后台管理</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="jquery/jquery.min.js"></script>
		
		<script src="jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="jquery/jquery-ui.theme.min.css" />
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap-switch.css" />
		<script src="bootstrap/js/bootstrap-switch.js"></script>
		
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
		
		<link href="resource/usermessage/usermessagemanager.css" rel="stylesheet" />
		<script src="resource/usermessage/usermessagemanager.js"></script>
		
		<script src="resource/user/userdialog.js"></script>
	</head>
	<body>
		<?php
			Render('header');
		?>

		<div class="container">
			<div id="main">
				<div class="col-md-12">
					<form id="mainForm" class="form-inline" action="<?php ActionLink('usermessage','usermessagemanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="20" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<input id="PageItems" name="PageItems" type="hidden" value="" />
						<input id="User_Id" name="User_Id" type="hidden" value="<?php if(array_key_exists('User_Id',$_GET) && is_numeric($_GET['User_Id'])){ echo $_GET['User_Id'];}?>"/>
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-2 t_l">
										<div class="form-group btn-group">
											<button class="btn btn-sm btn-info" type="button" onclick="userMessageEditor(this,<?php if(array_key_exists('User_Id',$_GET) && is_numeric($_GET['User_Id'])){ echo $_GET['User_Id'];}else{echo '0';} ?>)">发送消息</button>
											<button class="btn btn-sm btn-danger" type="button" onclick="userMessageDelete(this,0)">删除消息</button>
										</div>
									</div>
									<div class="col-sm-10 t_r">
										<div class="form-group">
											<input name="User_Name" type="text" class="form-control input-sm wd120" placeholder="收件人" />
										</div>
										<div class="form-group">
											<input name="Sender_Name" type="text" class="form-control input-sm wd120" placeholder="发件人" />
										</div>
										<div class="form-group">
											<select name="Flag" class="form-control input-sm" placeholder="标记">
												<option value="">全部</option>
												<option value="0">系统</option>
												<option value="1">用户</option>
											</select>
										</div>
										<div class="form-group">
											<input id="DateTimeCreateMin" name="DateTimeCreateMin" class="form-control input-sm date Wdate wd100" placeholder="发件开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'DateTimeCreateMax\')}'});" />
											<input id="DateTimeCreateMax" name="DateTimeCreateMax" class="form-control input-sm date Wdate wd100" placeholder="发件结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'DateTimeCreateMin\')}'});" />
										</div>
										<div class="form-group">
											<select name="Status_User" class="form-control input-sm" placeholder="状态">
												<option value="">全部</option>
												<option value="0">未读</option>
												<option value="1">已读</option>
												<option value="2">删除</option>
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
											<th class="t_c wd40"><input type="checkbox" onclick="toggleAll(this)" /></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="User_Name"> 收件人</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Sender_Name"> 发件人</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Flag"> 标记</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Message"> 消息</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="DateTimeCreate"> 创建时间</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Status"> 状态</a></th>
											<th class="t_c">操作</th>
										</tr>
									</thead>
									<tbody id="recordList">
									</tbody>
									<tfoot id="recordStatic">
										<tr>
											<td colspan="8" class="t_r"></td>
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