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
	
	$sthAccount = $pdomysql -> prepare('delete from tbAccountInfo where Id = :Id and RoleId != :RoleId;');
	$sthAccount -> execute(array('Id' => $Id,'RoleId' => 0x7FFFFFFF));
	
	$error = $sthAccount -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error));
		exit();
	}
	
	if(empty($sthAccount -> rowCount())){
		echo json_encode(array('status' => true));
		exit();
	}
	
	$sthUserStatistics = $pdomysql -> prepare('delete from tbUserStatisticsInfo where Id = (select Id from tbUserInfo where AccountId = :Id)');
	$sthUserStatistics -> execute(array('Id' => $Id));
	$error = $sthUserStatistics -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	$sthUserConfiguration = $pdomysql -> prepare('delete from tbUserConfiguration where Id in(select Id from tbUserInfo where AccountId = :Id)');
	$sthUserConfiguration -> execute(array('Id' => $Id));
	$error = $sthUserConfiguration -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	$sthUser = $pdomysql -> prepare('delete from tbUserInfo where AccountId = :Id');
	$sthUser -> execute(array('Id' => $Id));
	$error = $sthUser -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();