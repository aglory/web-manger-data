<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	header('Content-Type: application/json;');
	
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}else{
		echo json_encode(array('code' => 540,'status' => false,'message' => '缺少用户'));
		exit();
	}
	$Number = 0;
	if(array_key_exists('Number',$_POST) && is_numeric($_POST['Number'])){
		$Number = intval($_POST['Number']);
	}else{
		echo json_encode(array('code' => 540,'status' => false,'message' => '缺少积分'));
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
	
	$sthUserScoreLog = $pdomysql -> prepare('insert into tbUserScoreLog(User_Id,Type,Number,Mark)values(:User_Id,:Type,:Number,:Mark);');
	$sthUserScoreLog -> execute(array(
		'User_Id' => $User_Id,
		'Type' => $Type,
		'Number' => $Number,
		'Mark' => $Mark
	));
	
	$errors = array();
	
	$error = $sthUserScoreLog -> errorInfo();
	if($error[1]>0){
		$errors[] = $error[2];
	}

	if(empty($error)){
		$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountScore = CountScore + :Number where Id = :User_Id;');
		$sthUserStatistics -> execute(array(
			'User_Id' => $User_Id,
			'Number' => $Number
		));
		$error = $sthUserStatistics -> errorInfo();	
		if($error[1] > 0){
			$errors[] = $error[2];
		}
	}
	
	if(empty($errors)){
		$result['code'] = 200;
		$result['status'] = true;
	}else{
		$result['code'] = 540;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	
	echo json_encode($result);