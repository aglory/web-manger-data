<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
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
	
	$sth = $pdomysql -> prepare('delete from tbUserMessageInfo where Id in('.implode(',',$Ids).');');
	$sth -> execute();	
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();