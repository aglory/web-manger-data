<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('select Src from tbUserImageInfo where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id
	));
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	foreach($sth -> fetchAll(PDO::FETCH_ASSOC) as $item){
		unlink($item['Src']);
	}
	
	$sth = $pdomysql -> prepare('delete from tbUserImageInfo where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id
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