<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	header('Content-Type: application/json;');
	
	$User_Id = 0;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	$Sender_Id = CurrentUserId();
	$Message = '';
	if(array_key_exists('Message',$_POST) && !empty($_POST['Message'])){
		$Message = $_POST['Message'];
	}
	$ReplayId = 0;
	if(array_key_exists('ReplayId',$_POST) && is_numeric($_POST['ReplayId'])){
		$ReplayId = intval($_POST['ReplayId']);
	}
	
	$timespan = date('Y-m-d H:i:s',time());

	
	$sthUserMessage = $pdomysql -> prepare('insert into tbUserMessageInfo(User_Id,Sender_Id,Flag,Message,DateTimeCreate,DateTimeModify,Status_User,Status_Sender,ReplayId)values(:User_Id,:Sender_Id,:Flag,:Message,:DateTimeCreate,:DateTimeModify,:Status_User,:Status_Sender,:ReplayId)');
	$sthUserMessage -> execute(array(
		'User_Id' => $User_Id,
		'Sender_Id' => $Sender_Id,
		'Flag' => 1,
		'Message' => $Message,
		'DateTimeCreate' => $timespan,
		'DateTimeModify' => $timespan,
		'Status_User' => 0,
		'Status_Sender' => 0,
		'ReplayId' => $ReplayId
	));

	$errors = array();
	$error = $sthUserMessage -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'code' => 550,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountMessage = CountMessage + 1 where Id = :Id');
	$sthUserStatistics -> execute(array('Id' => $User_Id));
	
	$error = $sthUserStatistics -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'code' => 550,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true,'code' => 200));