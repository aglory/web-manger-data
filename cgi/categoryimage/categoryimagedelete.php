<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
	$Ids = array();
	if(array_key_exists('Id',$_POST) && is_array($_POST['Id'])){
		foreach($_POST['Id'] as $item){
			$Ids[] = intval($item);
		}
	}
	
	header('Content-Type: application/json');
	
	if(empty($Ids)){
		echo json_encode(array('status' => false,'message' => '缺少数据'));
		exit();
	}

	$sth = $pdomysql -> prepare('delete from tbCategoryImageInfo where Id in('.implode(',',$Ids).');');
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