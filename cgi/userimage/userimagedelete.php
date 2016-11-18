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
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('select Src from tbUserImageInfo where Id in('.implode(',',$Ids).');');
	$sth -> execute();
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	foreach($sth -> fetchAll(PDO::FETCH_ASSOC) as $item){
		unlink($item['Src']);
	}
	
	$sth = $pdomysql -> prepare('delete from tbUserImageInfo where Id in('.implode(',',$Ids).');');
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