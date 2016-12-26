<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');



	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$Account = '';
	if(array_key_exists('Account',$_POST) && !empty($_POST['Account'])){
		$Account = $_POST['Account'];
	}
	$Name = '';
	if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
		$Name = $_POST['Name'];
	}
	$RoleId = 0;
	if(array_key_exists('RoleId',$_POST) && is_numeric($_POST['RoleId'])){
		$RoleId = intval($_POST['RoleId']);
	}
	$SourceId = 0;
	if(array_key_exists('SourceId',$_POST) && is_numeric($_POST['SourceId'])){
		$SourceId = intval($_POST['SourceId']);
	}
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	if(empty($Id)){
		$Password = '';
		if(array_key_exists('Password',$_POST) && !empty($_POST['Password'])){
			$Password = $_POST['Password'];
		}
		
		if(empty($Password)){
			echo json_encode(array('status' => false,'message' => '请输入密码'));
			exit();
		}
		
		$PasswordConfirm = '';
		if(array_key_exists('PasswordConfirm',$_POST) && !empty($_POST['PasswordConfirm'])){
			$PasswordConfirm = $_POST['PasswordConfirm'];
		}
		
		if($Password != $PasswordConfirm){
			echo json_encode(array('status' => false,'message' => '输入密码不一致'));
			exit();
		}
		
		$Status = 1;
		if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
			$Status = intval($_POST['Status']);
		}
		
		$Salt = rand();
		$sthAccountInfo = $pdomysql -> prepare('insert into tbAccountInfo(Account,Name,Password,Salt,RoleId,SourceId,Status,DateTimeCreate,DateTimeModify)values(:Account,:Name,:Password,:Salt,:RoleId,:SourceId,:Status,:DateTimeCreate,:DateTimeModify);');
		$sthAccountInfo -> execute(array(
			'Account' => $Account,
			'Name' => $Name,
			'Password' => md5(md5($Password).$Salt),
			'Salt' => $Salt,
			'RoleId' => $RoleId,
			'SourceId' => $SourceId,
			'Status' => $Status,
			'DateTimeCreate' => $timespan,
			'DateTimeModify' => $timespan
		));
		
		$Id = $pdomysql -> lastInsertId();

		$errorAccount = $sthAccountInfo -> errorInfo();
		if($errorAccount[1] > 0){
			if($errorAccount[1] == 1062){
				echo json_encode(array('status' => false,'message' => '该账号已存在'));
				exit();
			}
			echo json_encode(array('status' => false,'message' => $errorAccounInfo[2]));
			exit();
		}
		
		echo json_encode(array('status' => true));
		exit();
	}
		
	$sthAccountInfo = $pdomysql -> prepare('update tbAccountInfo set Name=:Name,RoleId=:RoleId,SourceId=:SourceId,DateTimeModify=:DateTimeModify where Id = :Id;');
	$sthAccountInfo -> execute(array(
		'Id' => $Id,
		'Name' => $Name,
		'RoleId' => $RoleId,
		'SourceId' => $SourceId,
		'DateTimeModify' => $timespan
	));
	
	$errorAccount = $sthAccountInfo -> errorInfo();
	
	$errors = array();
	
	if($errorAccount[1] > 0){
		$errors[] = $errorAccount[2];
	}
	if(!empty($errors)){
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();