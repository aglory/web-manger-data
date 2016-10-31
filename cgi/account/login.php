<?php
require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
$Name = '';$Password = '';
if(array_key_exists('Name',$_POST)){
	$Name = $_POST['Name'];
}
if(array_key_exists('Password',$_POST)){
	$Password = $_POST['Password'];
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$result = array();
	header('Content-Type: application/json;');
	if(!empty($Name) && !empty($Password)){
		if(!isset($_SESSION)){ 
			session_start(); 
		} 
		$sth = $pdomysql -> prepare('select Id from tbAccountInfo where Account = :Account and Password = md5(concat(md5(:Password),Salt)) and Status = 1 and RoleId = :RoleId');
		$sth -> execute(array('Account' => $Name,'Password' => $Password,'RoleId' => 0x7FFFFFFF));
		foreach($sth -> fetchAll() as $item){
			$result['value'] = CurrentUserId($item['Id']);
			$result['status'] = true;
			setcookie('UserName',$Name,0,'/','*',false,true);
			echo json_encode($result);
			exit();
		}
		$result['status'] = false;
		$result['message'] = '用户名或密码错误';
		echo json_encode($result);
		exit();
	}
	$result['status'] = false;
	$result['message'] = '用户名或密码错误';
	echo json_encode($result);
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>登录</title>
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
		
		<link rel="stylesheet" href="common/common.css" />
		<script src="common/common.js"></script>
		
		<script>
			function login(bt){
				var form = bt.form;
				bt.disabled=true;
				$.ajax({
					url:form.action,
					type:'post',
					data:$(form).serialize(),
					dataType:'json',
					success:function(rest){
						if(!rest)return;
						if(!rest.status){
							UI_Tips('danger','错误',rest.message);
							return;
						}
						if(rest && rest.status){
							UI_Tips('success','成功');
							window.location.href="<?php ActionLink('home','index')?>";
							return;
						}
					},complete:function(){
						bt.disabled=false;
					}
				});
				return false;
			}
		</script>
	</head>
	<body>
	
    <div class="container">
		<form class="form-signin">
			<h2 class="form-signin-heading">登录</h2>
			<label for="Name" class="sr-only">账号</label>
			<input type="text" id="Name" name="Name" class="form-control" placeholder="账号" required="required" autofocus="autofocus">
			<label for="Password" class="sr-only">密码</label>
			<input type="password" id="Password" name="Password" class="form-control" placeholder="密码" required="required">
			<div class="checkbox hide">
				<label>
					<input type="checkbox" value="remember-me"> Remember me
				</label>
			</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit" onclick="return login(this);">Sign in</button>
		</form>
	</body>
</html>
