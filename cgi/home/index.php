<?php
	if(empty(CurrentUserId())){
		Render('home','login');
		exit(1);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>首页</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		<script src="jquery/jquery.min.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="css/home/index.css" />
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
			  </ul>
			</div><!--/.nav-collapse -->
		  </div>
		</nav>

		<div class="container">

		  <div class="starter-template t_c">
			<h1>Bootstrap starter template</h1>
			<p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>
		  </div>

		</div><!-- /.container -->
	</body>
</html>