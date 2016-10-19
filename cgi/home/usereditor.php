<?php
	if(!defined('Execute')) exit(0);
	if(empty(CurrentUserId())){
		Render('home','login');
		exit(1);
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	$id = 0;
	if(array_key_exists('id',$_POST) && is_numeric($_POST['id'])){
		$id = intval($_POST['id']);
	}
	
	$sth = $pdomysql -> prepare('select * from tbUserInfo where id = '.$id);
	$sth -> execute();
	
	$errors = array();
	$error = $sth -> errorInfo();
	if($error[0] > 0){
		$errors[] = $error[2];
	}
	
	$result = array();
	
	if(empty($errors)){
		$result['status'] = true;
		$result['model'] = $sth -> fetchAll(PDO::FETCH_ASSOC);
	}else{
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}	
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit(1);