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
	$Ids = array();
	if(array_key_exists('Ids',$_POST) && is_array($_POST['Ids'])){
		foreach($_POST['Ids'] as $item){
			$Ids[] = intval($item);
		}
	}
	if(!empty($Id)){
		$Ids[] = $Id;
	}
	
	header('Content-Type: application/json');	
	
	if(empty($Ids)){
		echo json_encode(array('status' => false,'message' => '编号为空'));
		exit();
	}
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sthUserMessageSelect = $pdomysql -> prepare('select Id,Sender_Id,Status_Sender from tbUserMessageInfo where Id in('.implode(',',$Ids).')');
	$sthUserMessageDelete = $pdomysql -> prepare('delete from tbUserMessageInfo where Id = :Id;');
	$sthUserStatistics = $pdomysql -> prepare('update tbUserStatisticsInfo set CountMessage = CountMessage - 1 where Id = :Id');
	
	$sthUserMessageSelect -> execute();
	
	$errors = array();

	$error = $sthUserMessageSelect -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,message => $error[2]));
		exit();
	}
	
	$countnumber = 0;
	
	foreach($sthUserMessageSelect -> fetchAll(PDO::FETCH_ASSOC) as $item){
		$param = array('Id' => $item['Id']);
		if($item['Status_Sender'] == 0){
			$sthUserStatistics -> execute($param);
			$error = $sthUserStatistics -> errorInfo();
			if($error[1] > 0){
				$errors[] = $error[2];
			}
			$sthUserMessageDelete -> execute($param);
			$error = $sthUserMessageDelete -> errorInfo();
			if($error[1] > 0){
				$errors[] = $error[2];
			}
		}
	}	
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();