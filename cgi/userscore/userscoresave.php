<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json');	
	
	
	$User_Id = 0;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	
	if(empty($User_Id)){
		echo json_encode(array('status' => false,'message' => '用户错误'));
		exit();
	}
	
	$Number = 0;
	if(array_key_exists('Number',$_POST) && is_numeric($_POST['Number'])){
		$Number = intval($_POST['Number']);
	}
	if(empty($Number)){
		echo json_encode(array('status' => false,'message' => '积分不能为0'));
		die();
	}
	
	$Type = 0;
	if(array_key_exists('Type',$_POST) && is_numeric($_POST['Type'])){
		$Type = intval($_POST['Type']);
	}
	
	$Mark = '';
	if(array_key_exists('Mark',$_POST) && !empty($_POST['Mark'])){
		$Mark = $_POST['Mark'];
	}
	
	$sthStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountScore = CountScore + :Number where Id = :Id;');
	$sthLog = $pdomysql -> prepare('insert into tbUserScoreLogInfo(User_Id,Type,Number,TotalNumber,Mark,DateTimeCreate)select Id,:Type,:Number,CountScore,:Mark,:DateTimeCreate from tbUserStatisticsInfo where Id = :User_Id;');
	$sthStatistics -> execute(array(
		'Id' => $User_Id,
		'Number' => $Number
	));
	$sthLog -> execute(array(
		'User_Id' => $User_Id,
		'Type' => $Type,
		'Number' => $Number,
		'Mark' => $Mark,
		'DateTimeCreate' => date('Y-m-d H:i:s',time())
	));
	$errors = array();
	
	$error = $sthLog -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	$error = $sthStatistics -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();