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
		<link rel="stylesheet" href="css/home/index.css" />
		
		<script>
		
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
				<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="panel-title">
								<label for="Name" class="control-label">名字</label><input id="Name" name="Name" type="text" class="form-control" />
								<label for="NickName" class="control-label">昵称</label><input id="NickName" name="NickName" type="text" class="form-control" />
							</div>
						</div>
						<div class="panel-body">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
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