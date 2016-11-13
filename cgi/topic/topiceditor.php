<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	$id = 0;
	if(array_key_exists('id',$_POST) && is_numeric($_POST['id'])){
		$id = intval($_POST['id']);
	}
	
	$sth = $pdomysql -> prepare('select * from tbTopicInfo where id = '.$id);
	$sth -> execute();
	
	$errors = array();
	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	$result = array();
	
	if(empty($errors)){
		$model = $sth -> fetch(PDO::FETCH_ASSOC);
		$result['status'] = true;
		$result['model'] = empty($model)?null:$model;
	}else{
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}	
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();