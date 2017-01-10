<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
	$CategoryIds = array();
	if(array_key_exists('CategoryIds',$_POST) && is_array($_POST['CategoryIds'])){
		foreach($_POST['CategoryIds'] as $item){
			$CategoryIds[] = intval($item);
		}
	}
	$ImageIds = array();
	if(array_key_exists('ImageIds',$_POST) && is_array($_POST['ImageIds'])){
		foreach($_POST['ImageIds'] as $item){
			$ImageIds[] = intval($item);
		}
	}
	
	header('Content-Type: application/json');
	
	if(empty($CategoryIds) || empty($ImageIds)){
		echo json_encode(array('status' => false,'message' => '缺少数据'));
		exit();
	}

	$sth = $pdomysql -> prepare('delete from tbCategoryImageRelationInfo where CategoryId in('.implode(',',$CategoryIds).') and ImageId in('.implode(',',$ImageIds).');');
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