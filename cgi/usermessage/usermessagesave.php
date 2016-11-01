<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json');	
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$User_Id = 0;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	if(empty($User_Id)){
		echo json_encode(array('status' => false,'message' => '收件人不能为空'));
		exit();
	}
	
	$Sender_Id = 0;
	if(array_key_exists('Sender_Id',$_POST) && !empty($_POST['Sender_Id'])){
		$Sender_Id = $_POST['Sender_Id'];
	}
	if(empty($Sender_Id)){
		$Sender_Id = CurrentUserId();
	}
	
	
	$Flag = 0;
	if(array_key_exists('Flag',$_POST) && is_numeric($_POST['Flag'])){
		$Flag = intval($_POST['Flag']);
	}
	$Message = 0;
	if(array_key_exists('Message',$_POST) && !empty($_POST['Message'])){
		$Message = $_POST['Message'];
	}
	
	if(empty($Message)){
		echo json_encode(array('status' => false,'message' => '消息不能为空'));
		exit();
	}
	
	$timespan = date('Y-m-d H:i:s',time());
		
	$sthUserInfo = $pdomysql -> prepare('insert into tbUserMessageInfo(User_Id,Sender_Id,Flag,Message,DateTimeCreate,DateTimeModify,Status)values(:User_Id,:Sender_Id,:Flag,:Message,:DateTimeCreate,:DateTimeModify,:Status);');
	$sthUserInfo -> execute(array(
		'User_Id' => $User_Id,
		'Sender_Id' => $Sender_Id,
		'Flag' => $Flag,
		'Message' => $Message,
		'DateTimeCreate' => $timespan,
		'DateTimeModify' => $timespan,
		'Status' => 1
	));

	$errorUserInfo = $sthUserInfo -> errorInfo();
	
	$errors = array();
	
	if($errorUserInfo[1] > 0){
		$errors[] = $errorUserInfo[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();