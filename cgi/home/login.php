<?php
require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
$name = '';$password = '';
if(array_key_exists('name',$_POST)){
	$name = $_POST['name'];
}
if(array_key_exists('password',$_POST)){
	$password = $_POST['password'];
}
if(!empty($name) && !empty($password)){
	$result = array();
	header('Content-Type: application/json;');
	if(!isset($_SESSION)){ 
		session_start(); 
	} 
	$sth = $pdomysql -> prepare('select Id from tbUserInfo where Name = :Name and Password = md5(:Password) and RoleId = :RoleId');
	$sth -> execute(array('Name' => $name,'Password' => $password,'RoleId' => 0x7FFFFFFF));
	foreach($sth -> fetchAll() as $item){
		$result['value'] = CurrentUserId($item['Id']);
		$result['status'] = true;
		setcookie('UserName',$name,0,'/','*',false,true);
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
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		<script src="jquery/jquery.min.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
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
						if(rest && rest.status){
							window.location.href="<?php ActionLink('home','index')?>"
						}
					},complete:function(){
						bt.disabled=false;
					}
				});
			}
		</script>
	</head>
	<body>
	
    <div class="container">
		<form class="form-signin">
			<h2 class="form-signin-heading">登录</h2>
			<label for="name" class="sr-only">账号</label>
			<input type="text" id="name" name="name" class="form-control" placeholder="账号" required="required" autofocus="autofocus">
			<label for="password" class="sr-only">密码</label>
			<input type="password" id="password" name="password" class="form-control" placeholder="密码" required="required">
			<div class="checkbox hide">
				<label>
					<input type="checkbox" value="remember-me"> Remember me
				</label>
			</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit" onclick="return login(this);">Sign in</button>
		</form>
	</body>
</html>