<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
	$Ids = array();
	if(array_key_exists('Id',$_POST) && is_array($_POST['Id'])){
		foreach($_POST['Id'] as $id){
			$Ids[] = intval($id);
		}
	}
	$Status = 0;
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$Status = intval($_POST['Status']);
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('update tbImageInfo set Status = :StatusNew,DateTimeModify = :timespan where Id in('.implode(',',$Ids).') and Status != :StatusOld;');
	$sth -> execute(array(
		'StatusNew' => $Status,
		'timespan' => $timespan,
		'StatusOld' => $Status
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