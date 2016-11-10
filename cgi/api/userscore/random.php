<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	header('Content-Type: application/json;');
	
	$User_Id = CurrentUserId();
	
	$Number = 0;
	if(array_key_exists('Number',$_POST) && is_numeric($_POST['Number'])){
		$Number = intval($_POST['Number']);
	}
	if($Number <= 0){
		echo json_encode(array('code' => 540,'status' => false,'message' => '积分参数错误'));
		die(1);
	}
	
	$Type = 1;
	$timespan = date('Y-m-d H:i:s',time());
	$errors = array();
	
	$Change = $Number - rand(0,2 * $Number);
	
	$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountScore = CountScore + :Change where Id = :User_Id and CountScore >= :Number');
	$sthUserStatistics -> execute(array(
		'User_Id' => $User_Id,
		'Change' => $Change,
		'Number' => $Number
	));
	

	if(!$sthUserStatistics -> rowCount()){
		echo json_encode(array('code' => 200,'status' => 'false','message' => '积分不够'));
		die(1);
	}
	
	$error = $sthUserStatistics -> errorInfo();	
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	$sthUserScoreLog = $pdomysql -> prepare('insert into tbUserScoreLogInfo(User_Id,Type,Number,TotalNumber,Mark,DateTimeCreate)select Id,:Type,:Number,CountScore + :Change,:Mark,:DateTimeCreate from tbUserStatisticsInfo where Id = :User_Id;');
	$sthUserScoreLog -> execute(array(
		'User_Id' => $User_Id,
		'Type' => $Type,
		'Number' => 0 - $Number,
		'Change' => 0 - $Number - $Change,
		'Mark' => '抽奖消耗积分',
		'DateTimeCreate' => $timespan
	));
	$error = $sthUserScoreLog -> errorInfo();
	if($error[1]>0){
		$errors[] = $error[2];
	}
	$sthUserScoreLog -> execute(array(
		'User_Id' => $User_Id,
		'Type' => $Type,
		'Number' => $Number + $Change,
		'Change' => 0,
		'Mark' => '抽奖活动积分',
		'DateTimeCreate' => $timespan
	));
	
	if(empty($errors)){
		$result['code'] = 200;
		$result['status'] = true;
	}else{
		$result['code'] = 540;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	$result['Change'] = $Change;
	
	echo json_encode($result);