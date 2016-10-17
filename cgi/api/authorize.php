<?php
if(!defined('Execute')){ exit();}
header('Content-Type: application/json;');
require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

$name = '';$password = '';$sex = 0;
if(array_key_exists('name',$_POST)){
	$name = $_POST['name'];
}
if(array_key_exists('password',$_POST)){
	$password = $_POST['password'];
}
if(array_key_exists('sex',$_POST)){
	$sex = $_POST['sex'];
}


function IsLoginSuccess($pdomysql,$name,$password){
	$sth = $pdomysql -> prepare('select Id,Password from tbUserInfo where Name = :Name');
	$sth -> execute(array('Name' => $name));
	foreach($sth -> fetchAll() as $item){
		if($item['Password'] == md5($password)){
			return CurrentUserId($item['Id']); 
		}
		return 0;
	}
	return -1;
}

function IsRegisterSuccess($pdomysql,$name,$password,$sex){
	$timespan = date('Y-m-d H:i:s',time());
	$sth = $pdomysql -> prepare('insert into tbUserInfo(`Name`,`Password`,`NickName`,`Sex`,`CreateDateTime`,`ModifyDateTime`)values(:Name,:Password,:NickName,:Sex,:CreateDateTime,:ModifyDateTime);');
	$sth -> execute(array(
		'Name' => $name,
		'Password' => md5($password),
		'NickName' => $name,
		'Sex' => empty($sex)?0:1,
		'CreateDateTime' => $timespan,
		'ModifyDateTime' => $timespan
	));
	
	$errors = $sth -> errorInfo();
	
	$error = $errors[1];
	if($error > 0){
		return -1;
	}
	return CurrentUserId($pdomysql -> lastInsertId());
}

$result = array();

if(empty($name) || empty($password)){
	$result['status'] = false;
	$result['message'] = '缺少登录账号/密码';
	echo json_encode($result);
	exit(1);
}
switch(IsLoginSuccess($pdomysql,$name,$password)){
	case 0:
		$result['status'] = false;
		$result['message'] = '密码错误';
		echo json_encode($result);
		exit(1);
		break;
	case -1:
		break;
	default:
		$result['status'] = true;
		$result['session_name'] = session_name();
		$result['session_id'] = session_id();
		echo json_encode($result);
		exit(1);
		break;
}
switch(IsRegisterSuccess($pdomysql,$name,$password ,$sex)){
	case -1:
		$result['status'] = false;
		$result['message'] = '该用户名已被注册';
		echo(json_encode($result));
		exit(1);
		break;
	default:
		$result['status'] = true;
		$result['session_name'] = session_name();
		$result['session_id'] = session_id();
		echo json_encode($result);
		exit(1);
		break;
}

$result['status'] = false; 
$result['message'] = '未知错误';

echo(json_encode($result));