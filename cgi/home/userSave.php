<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$NickName = '';
	if(array_key_exists('NickName',$_POST) && !empty($_POST['NickName'])){
		$NickName = $_POST['NickName'];
	}
	$Sex = 0;
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$Sex = intval($_POST['Sex']) > 0 ? 1 : 0;
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	if(empty($Id)){
		$Name = '';
		if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
			$Name = $_POST['Name'];
		}else{
			echo json_encode(array('status' => false,'message' => '账号不能为空'));
			exit();
		}
		$Password = '';
		if(array_key_exists('Password',$_POST) && !empty($_POST['Password'])){
			$Password = $_POST['Password'];
		}else{
			echo json_encode(array('status' => false,'message' => '密码不能为空'));
			exit();
		}
		$sth = $pdomysql -> prepare('insert into tbUserInfo(Name,Password,NickName,Sex,DateTimeCreate,DateTimeModify,Status)values(:Name,:Password,:NickName,:Sex,:DateTimeCreate,:DateTimeModify,:Status);');
		$sth -> execute(array(
			'Name' => $Name,
			'Password' => md5($Password),
			'NickName' => $NickName,
			'Sex' => $Sex,
			'DateTimeCreate' => $timespan,
			'DateTimeModify' => $timespan,
			'Status' => 1
		));
		
		$errors = array();

		$error = $sth -> errorInfo();
		if($error[0] > 0){
			$errors[] = $error[2];
		}
		
		if(empty($errors)){
			echo json_encode(array('status' => true,'sql' => var_export($error,true)));
		}else{
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		}
		exit();
	}
	
	$sth = $pdomysql -> prepare('update tbUserInfo set NickName = :NickName,Sex = :Sex,DateTimeModify = :timespan where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id,
		'NickName' => $NickName,
		'Sex' => $Sex,
		'timespan' => $timespan
	));	
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[0] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();