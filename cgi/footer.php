<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
?>
<footer class="footer t_c">
  <div class="container">
	<p class="text-muted">谢志丹同学的杰作</p>
  </div>
</footer>