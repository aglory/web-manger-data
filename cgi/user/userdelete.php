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
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sthAccountList = $pdomysql -> prepare('select Id,RoleId from tbAccountInfo where Id in(select AccountId from tbUserInfo where Id = :Id)');
	$sthAccountList -> execute(array('Id' => $Id));
	$accountId = 0;
	foreach($sthAccountList -> fetchAll(PDO::FETCH_ASSOC) as $item){
		if($item['RoleId'] == 0x7FFFFFFF){
			echo json_encode(array('status' => false,'message' => '管理员账号不能删除'));
			exit();
		}
		$accountId = $item['id'];
	}
	
	$sthAccount = $pdomysql -> prepare('delete from tbAccountInfo where Id = :Id and RoleId != :RoleId;');
	$sthAccount -> execute(array('Id' => $accountId,'RoleId' => 0x7FFFFFFF));
	
	$error = $sthAccount -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error));
		exit();
	}
	
	$sthUser = $pdomysql -> prepare('delete from tbUserInfo where Id = :Id');
	$sthUser -> execute(array('Id' => $Id));
	$error = $sthUser -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	$sthUserStatistics = $pdomysql -> prepare('delete from tbUserStatisticsInfo where Id = :Id');
	$sthUserStatistics -> execute(array('Id' => $Id));
	$error = $sthUserStatistics -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	$sthUserConfiguration = $pdomysql -> prepare('delete from tbUserConfiguration where Id = :Id');
	$sthUserConfiguration -> execute(array('Id' => $Id));
	$error = $sthUserConfiguration -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();