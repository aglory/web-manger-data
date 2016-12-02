<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	header('Content-Type: application/json;');
	
	$User_Id = CurrentUserId();
	
	$timespan = date('Y-m-d H:i:s',time());

	$sthUserMessageSelect = $pdomysql -> prepare('select * from tbUserMessageInfo where User_Id = :User_Id and Status_User = :Status_User;');
	$sthUserMessageSelect -> execute(array(
		'User_Id' => $User_Id,
		'Status_User' => 0
	));

	$error = $sthUserMessageSelect -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'code' => 550,'message' => $error[2]));
		exit();
	}
	
	$Ids = array();
	$MessageList = $sthUserMessageSelect -> fetchAll(PDO::FETCH_ASSOC);
	foreach($MessageList as $item){
		$Ids[] = $item['Id'];
	}
	
	if(!empty($Ids)){
		$sthUserMessageUpdate = $pdomysql -> prepare('update tbUserMessageInfo set Status_User = 1 where Id in('.implode(',',$Ids).')');
		$sthUserMessageUpdate -> execute();
		
		$error = $sthUserMessageUpdate -> errorInfo();
		if($error[1] > 0){
			echo json_encode(array('status' => false,'code' => 550,'message' => $error[2]));
			exit();
		}
		
		$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountMessage = CountMessage - :CountMessage where Id = :Id');
		$sthUserStatistics -> execute(array(
			'Id' => $User_Id,
			'CountMessage' => count($Ids)
		));
		$error = $sthUserStatistics -> errorInfo();
		if($error[1] > 0){
			echo json_encode(array('status' => false,'code' => 550,'message' => $error[2]));
			exit();
		}
	}
	
	echo json_encode(array('status' => true,'code' => 200,'recordList' => $MessageList));