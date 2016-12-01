<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	header('Content-Type: application/json;');
	
	$User_Id = CurrentUserId();
	
	$Number = 0;
	if(array_key_exists('Number',$_POST) && is_numeric($_POST['Number'])){
		$Number = intval($_POST['Number']);
	}else{
		echo json_encode(array('code' => 400,'status' => false,'message' => '缺少积分'));
	}
	
	$Type = 0;
	if(array_key_exists('Type',$_POST) && is_numeric($_POST['Type'])){
		$Type = intval($_POST['Type']);
	}
	$Mark = '';
	if(array_key_exists('Mark',$_POST) && !empty($_POST['Mark'])){
		$Mark = $_POST['Mark'];
	}
	$timespan = date('Y-m-d H:i:s',time());
	$errors = array();
	
	$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountScore = CountScore + :NumberNew where Id = :User_Id and CountScore + :NumberOld >=0;');
	$sthUserStatistics -> execute(array(
		'User_Id' => $User_Id,
		'NumberNew' => $Number,
		'NumberOld' => $Number
	));
	if(empty($sthUserStatistics -> rowCount())){
		echo json_encode(array(
			'code' => 200,
			'status' => false,
			'message' => '积分不够'
		));
		die(1);
	}
	
	$error = $sthUserStatistics -> errorInfo();	
	if($error[1] > 0){
		echo json_encode(array(
			'code' =>550,
			'status' => false,
			'message' => $error[2]
		));
		exit();
	}
	$sthUserScoreLog = $pdomysql -> prepare('insert into tbUserScoreLogInfo(User_Id,Type,Number,TotalNumber,Mark,DateTimeCreate)select Id,:Type,:Number,CountScore,:Mark,:DateTimeCreate from tbUserStatisticsInfo where Id = :User_Id;');
	$sthUserScoreLog -> execute(array(
		'User_Id' => $User_Id,
		'Type' => $Type,
		'Number' => $Number,
		'Mark' => $Mark,
		'DateTimeCreate' => $timespan
	));
	
	$error = $sthUserScoreLog -> errorInfo();
	if($error[1]>0){
		echo json_encode(array(
			'code' => 550,
			'status' => false,
			'message' => $error[2]
		));
		exit();
	}
	
	echo json_encode(array(
		'code' => 200,
		'status' => true
	));