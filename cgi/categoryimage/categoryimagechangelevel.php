<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$Level = 0;
	if(array_key_exists('Level',$_POST) && is_numeric($_POST['Level'])){
		$Level = intval($_POST['Level']);
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	$sth = $pdomysql -> prepare('update tbCategoryImageInfo set Level = :Level,DateTimeModify = :DateTimeModify where Id = :Id');
	$sth -> execute(array(
		'Id' => $Id,
		'DateTimeModify' => $timespan,
		'Level' => $Level
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