<?php
	if(!defined('Execute')) exit(0);
	if(empty(CurrentUserId())){
		Render('home','login');
		exit(1);
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
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome.min.css" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		
		<script src="My97DatePicker/WdatePicker.js"></script>
		<script src="My97DatePicker/lang/zh-cn.js"></script>
		
		<link rel="stylesheet" href="pager/pager.css" />
		<script src="pager/pager.js"></script>
		
		<link rel="stylesheet" href="common/common.css" />
		<script src="common/common.js"></script>
		
		<script src="common/template.js"></script>
		
		<link rel="stylesheet" href="css/home/usermanger.css" />
		<script src="js/home/usermanger.js"></script>
		
		<script>
			$(function(){
				$("#mainForm button[type='submit']").click(function(e){
					e.preventDefault();
					doQuery(null,null,this);
				});
				$("#mainForm button[type='button']").click(function(e){
					e.preventDefault();
					doQuery(1,null,this);
				});
				
				doQuery();
			});
		</script>
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
		  <div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">菜单</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand" href="<?php ActionLink('home','index')?>">后台管理</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
			  <ul class="nav navbar-nav">
				<li <?php if(Model == 'home' && Action == 'usermanager') { ?>class="active"<?php }?>><a href="<?php ActionLink('home','usermanager')?>">用户管理</a></li>
				<li><a href="<?php ActionLink('home','logout')?>">注销</a></li>
			  </ul>
			</div>
		  </div>
		</nav>

		<div class="container">
			<div id="main">
				<div class="col-md-12">
					<form id="mainForm" class="form-inline" action="<?php ActionLink('home','usermanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="20" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-2 t_l">
										<div class="form-group">
											<button class="btn btn-sm btn-info" type="button" onclick="userEditor(this,0)">编辑</button>
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
											<input id="ModifyDateTimeStart" name="ModifyDateTimeStart" class="form-control input-sm date Wdate wd100" placeholder="开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'ModifyDateTimeEnd\')}'});" />
											<input id="ModifyDateTimeEnd" name="ModifyDateTimeEnd" class="form-control input-sm date Wdate wd100" placeholder="结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'ModifyDateTimeStart\')}'});" />
										</div>
										<div class="form-group">
											<button type="submit" class="btn btn-info btn-sm">查询</button>
											<button type="button" class="btn btn-default btn-sm">刷新</button>
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
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="ModifyDateTime"> 时间（创建/登录）</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="RoleId"> 角色</a></th>
											<th class="t_c">操作</th>
										</tr>
									</thead>
									<tbody id="recordList">
									</tbody>
									<tfoot id="recordStatic">
										<tr>
											<td colspan="9" class="pager"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<footer class="footer t_c">
		  <div class="container">
			<p class="text-muted">谢志丹同学的杰作</p>
		  </div>
		</footer>
	</body>
</html>