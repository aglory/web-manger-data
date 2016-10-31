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
		
		<link href="resource/user/usermanger.css" rel="stylesheet" />
		<script src="resource/user/usermanger.js"></script>
	</head>
	<body>
		<?php
			Render('header');
		?>

		<div class="container">
			<div id="main">
				<div class="col-md-12">
					<form id="mainForm" class="form-inline" action="<?php ActionLink('user','usermanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="20" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<input id="PageItems" name="PageItems" type="hidden" value="" />
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-2 t_l">
										<div class="form-group">
											<button class="btn btn-sm btn-info" type="button" onclick="userEditor(this,0)">添加</button>

										</div>
									</div>
									<div class="col-sm-10 t_r">
										<div class="form-group">
											<input id="Name" name="Name" type="text" class="form-control input-sm" placeholder="名字" />
										</div>
										<div class="form-group">
											<input id="NickName" name="NickName" type="text" class="form-control input-sm" placeholder="昵称" />
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
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Name"> 名称</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="NickName"> 昵称</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Score"> 积分</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Follow"> 关注者</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Sex"> 性别</a></th>
											<th class="t_c">头像</th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="DateTimeModify"> 时间（创建/登录）</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="RoleId"> 角色</a></th>
											<th class="t_c">操作</th>
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