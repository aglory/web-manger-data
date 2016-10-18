<?php
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
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		<script src="jquery/jquery.min.js"></script>
		<script src="jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="jquery/jquery-ui.theme.min.css" />
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="pager/pager.css" />
		<script src="pager/pager.js"></script>
		
		<link rel="stylesheet" href="css/common.css" />
		<script src="js/common.js"></script>
		
		<link rel="stylesheet" href="css/home/index.css" />
		
		<script>
			$(function(){
				$("#frmSubmit button[type='submit']").click(function(e){
					e.preventDefault();
					doSearch(null,this);
				});
				$("#frmSubmit button[type='button']").click(function(e){
					e.preventDefault();
					doSearch(1,this);
				});
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
				<li class="active"><a href="#">用户管理</a></li>
				<li><a href="<?php ActionLink('home','logout')?>">注销</a></li>
			  </ul>
			</div>
		  </div>
		</nav>

		<div class="container">
		  <div class="pd35 t_c">
			<h1>Bootstrap starter template</h1>
			<div class="col-md-12">
				<form id="frmSubmit" class="form-inline" action="<?php ActionLink('home','userinfolist')?>">
					<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
					<input id="PageSize" name="PageSize" type="hidden" value="20" />
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="panel-title t_r">
								<div class="form-group">
									<input id="Name" name="Name" type="text" class="form-control input-sm input-sm-4" placeholder="名字" />
									<input id="NickName" name="NickName" type="text" class="form-control input-sm input-sm-4" placeholder="昵称" />
									<button type="submit" class="btn btn-info btn-sm">查询</button>
									<button type="button" class="btn btn-default btn-sm">刷新</button>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>名称</th>
										<th>昵称</th>
										<th>性别</th>
										<th>头像</th>
										<th>时间（创建/登录）</th>
										<th>角色</th>
									</tr>
								</thead>
								<tbody id="recordList">
								</tbody>
								<tfoot id="recordStatic">
								</tfoot>
							</table>
						</div>
					</div>
				</form>
			</div>
		</div>
		<footer class="footer t_c">
		  <div class="container">
			<p class="text-muted">谢志丹同学的杰作</p>
		  </div>
		</footer>
	</body>
</html>