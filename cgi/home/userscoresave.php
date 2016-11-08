<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json');	
	
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
	
	$User_Id = array();
	if(array_key_exists('User_Id',$_POST) && is_array($_POST['User_Id'])){
		foreach($_POST['User_Id'] as  $item){
			$User_Id[] = intval($item);
		}
		if(!empty($User_Id)){
			$sthLog = $pdomysql -> prepare('insert into tbUserScoreLog(User_Id,Type,Number,Mark,DateTimeCreate)values(:User_Id,:Type,:Number,:Mark,:DateTimeCreate);');
			$sthStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountScore = CountScore + :Number where Id = :Id;');
			$parLog = array('Type' => $Type,'Number' => $Number,'Mark' => $Mark,'DateTimeCreate' => date('Y-m-d H:i:s',time()));
			$parStatistics = array('Number' => $Number);
			$errors = array();
			foreach($User_Id as  $item){
				$parLog['User_Id'] = $item;
				$sthLog -> execute($parLog);
				$error = $sthLog -> errorInfo();
				if($error[1]>0){
					$errors[] = $error[2];
				}
				$parStatistics['Id'] = $item;
				$sthStatistics -> execute($parStatistics);
				$error = $sthStatistics -> errorInfo();
				if($error[1]>0){
					$errors[] = $error[2];
				}
			}
			if(empty($errors)){
				echo json_encode(array('status' => true));
				die();
			}
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
			die();
		}
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