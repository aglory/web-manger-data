<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	header('Content-Type: application/json;');
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$Code = '';
	if(array_key_exists('Code',$_POST) && !empty($_POST['Code'])){
		$Code = $_POST['Code'];
	}else{
		echo json_encode(array('status' => false,'message' => '编码必须输入'));
		exit();
	}
	$Title = '';
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$Title = $_POST['Title'];
	}
	
	if(empty($Id)){
		$sth = $pdomysql -> prepare('insert into tbTopicInfo(Code,Title)values(:Code,:Title)');
		$sth -> execute(array(
			'Code' => $Code,
			'Title' => $Title
		));
		
		$errors = array();

		$error = $sth -> errorInfo();
		if($error[1] == 1062){
			$errors[] = '该编码已使用';
		}else if($error[1] > 0){
			$errors[] = $error[2];
		}
		
		if(!empty($errors)){
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
			exit();
		}
		
		echo json_encode(array('status' => true));
		exit();
	}
		
	$sth = $pdomysql -> prepare('update tbTopicInfo set Code=:Code,Title=:Title where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id,
		'Code' => $Code,
		'Title' => $Title
	));

	$error = $sth -> errorInfo();
	
	$errors = array();
	
	if($error[1] == 1062){
		$errors[] = '该编码已使用';
	}else if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();