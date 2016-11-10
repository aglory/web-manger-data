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
	$Ids = array();
	if(array_key_exists('Ids',$_POST) && is_array($_POST['Ids'])){
		foreach($_POST['Ids'] as $item){
			$Ids[] = intval($item);
		}
	}
	if(!empty($Id)){
		$Ids[] = $Id;
	}
	
	if(empty($Ids)){
		echo json_encode(array('status' => false,'message' => '未选择消息'));
		die();
	}
	
	$sqlSet = array('DateTimeModify = :DateTimeModify');
	
	if(array_key_exists('Status_User',$_POST) && is_numeric($_POST['Status_User'])){
		$sqlSet[] = 'Status_User = '.$_POST['Status_User'];
	}
	if(array_key_exists('Status_Sender',$_POST) && is_numeric($_POST['Status_Sender'])){
		$sqlSet[] = 'Status_Sender = '.$_POST['Status_Sender'];
	}
	
	
	$timespan = date('Y-m-d H:i:s',time());

	$sthUserMessage = $pdomysql -> prepare('update tbUserMessageInfo set '.implode(',',$sqlSet).' where Id = :Id');
	$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountMessage = CountMessage - 1 where Id = :Id');
	
	$errors = array();
	
	foreach($Ids as $item){
		$sthUserMessage -> execute(array(
			'Id' => $item,
			'DateTimeModify' => $timespan
		));	
		$error = $sthUserMessage -> errorInfo();
		if($error[1] > 0){
			$errors[] = $error[2];
		}
		$sthUserStatistics -> execute(array('Id' => $Id));
		$error = $sthUserStatistics -> errorInfo();
		if($error[1] > 0){
			$errors[] = $error[2];
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();