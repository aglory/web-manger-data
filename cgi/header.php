<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
?>
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
		<li <?php if(Model == 'user' && Action == 'usermanager') { ?>class="active"<?php }?>><a href="<?php ActionLink('user','usermanager')?>">用户管理</a></li>
		<li <?php if(Model == 'userimage' && Action == 'userimagemanager') { ?>class="active"<?php }?>><a href="<?php ActionLink('userimage','userimagemanager')?>">图片管理</a></li>
		<li <?php if(Model == 'usermessage' && Action == 'usermessagemanager') { ?>class="active"<?php }?>><a href="<?php ActionLink('usermessage','usermessagemanager')?>">消息管理</a></li>
		<li <?php if(Model == 'userscore' && Action == 'useruserscoremanager') { ?>class="active"<?php }?>><a href="<?php ActionLink('userscore','userscoremanager')?>">积分管理</a></li>
		
		<li><a href="<?php ActionLink('account','logout')?>">注销</a></li>
	  </ul>
	</div>
  </div>
</nav>