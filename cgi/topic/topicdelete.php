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
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sthTopic = $pdomysql -> prepare('delete from tbTopicInfo where Id = :Id;');
	$sthTopic -> execute(array('Id' => $Id));	
		
	$errors = array();

	$error = $sthTopic -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	$sthTopicItem = $pdomysql -> prepare('delete from tbTopicItemInfo where Topic_Id = :Topic_Id');
	$sthTopicItem -> execute(array('Topic_Id' => $Id));

	$error = $sthTopic -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();