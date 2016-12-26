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
	$Status = 0;
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$Status = intval($_POST['Status']);
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());

	$sth = $pdomysql -> prepare('update tbAccountInfo set Status = :StatusNew,DateTimeModify = :DateTimeModify where Id = :Id and Status != :StatusOld;');
	$sth -> execute(array(
		'Id' => $Id,
		'StatusNew' => $Status,
		'DateTimeModify' => $timespan,
		'StatusOld' => $Status,
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