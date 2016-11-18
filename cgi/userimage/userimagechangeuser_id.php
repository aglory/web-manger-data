<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
	$Ids = [];
	if(array_key_exists('Id',$_POST) && !empty($_POST['Id'])){
		foreach($_POST['Id'] as $item){
			$Ids[] = intval($item);
		}
	}
	$User_Id = null;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	
	header('Content-Type: application/json');
	
	if(empty($Ids) || empty($User_Id)){
		echo json_encode(array('status' => false,'message' => '缺少数据'));
		exit();
	}
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('update tbUserImageInfo set User_Id = :User_Id,DateTimeModify = :timespan where Id in('.implode(',',$Ids).');');
	$sth -> execute(array(
		'User_Id' => $User_Id,
		'timespan' => $timespan
	));	
		
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