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
	
	header('Content-Type: application/json');	
	
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
	
	
	$timespan = date('Y-m-d H:i:s',time());

	$Salt = rand(1,0x7FFFFFFF);
	
	$sth = $pdomysql -> prepare('update tbAccountInfo set Password = :Password,Salt = :Salt,DateTimeModify = :DateTimeModify where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id,
		'Password' => md5(md5($Password).$Salt),
		'Salt' => $Salt,
		'DateTimeModify' => $timespan
	));	
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();