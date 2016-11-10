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
	$IsDefault = 0;
	if(array_key_exists('IsDefault',$_POST) && is_numeric($_POST['IsDefault'])){
		$IsDefault = intval($_POST['IsDefault']);
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('update tbUserImageInfo set IsDefault = :IsDefaultNew,DateTimeModify = :timespan where Id = :Id and IsDefault != :IsDefaultOld;');
	$sth -> execute(array(
		'Id' => $Id,
		'IsDefaultNew' => $IsDefault,
		'timespan' => $timespan,
		'IsDefaultOld' => $IsDefault,
	));	
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors) && !empty($IsDefault)){
		$sth = $pdomysql -> prepare('update tbUserInfo inner join tbUserImageInfo on tbUserInfo.Id= tbUserImageInfo.User_Id set tbUserInfo.Img = tbUserImageInfo.Src,tbUserInfo.DateTimeModify =:DateTimeModify where tbUserImageInfo.Id = '.$Id.';');
		$sth -> execute(array(
			'DateTimeModify' => $timespan
		));
		
		$error = $sth -> errorInfo();
		if($error[1] > 0){
			$errors[] = $error[2];
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();