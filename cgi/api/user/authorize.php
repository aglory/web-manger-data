<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	header('Content-Type: application/json;');
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$account = '';$password = '';$sex = 0;
	if(array_key_exists('Account',$_POST)){
		$account = $_POST['Account'];
	}
	if(array_key_exists('Password',$_POST)){
		$password = $_POST['Password'];
	}
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$sex = $_POST['Sex'];
	}
	
	if(empty($account) || empty($password)){
		echo json_encode(array('code' => 200,'status' => false,'message' => '缺少登录账号/密码'));
		exit(1);
	}
	
	$sth = $pdomysql -> prepare('select Id,Password,Salt from tbAccountInfo where Account = :Account and Status = :Status');
	$sth -> execute(array('Account' => $account,'Status' => 1));
	foreach($sth -> fetchAll() as $item){
		if($item['Password'] == md5(md5($password).$item['Salt'])){
			CurrentUserId($item['Id']);
			echo json_encode(array('code' => 200,'status' => true,'session_id' => session_id(),'session_name' => session_name()));
			exit(1);
		}
	}	
	
die('xx');
function IsRegisterSuccess($pdomysql,$name,$password,$sex){
	$timespan = date('Y-m-d H:i:s',time());
	$sth = $pdomysql -> prepare('insert into tbAccountInfo(`Name`,`Password`,`NickName`,`Sex`,`CreateDateTime`,`ModifyDateTime`)values(:Name,:Password,:NickName,:Sex,:CreateDateTime,:ModifyDateTime);');
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