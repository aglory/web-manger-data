<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	$CategoryIds = array();
	$ImageIds = array();
	if(array_key_exists('CategoryIds',$_POST) && is_array($_POST['CategoryIds'])){
		foreach($_POST['CategoryIds'] as $item){
			$CategoryId = intval($item);
			if(in_array($CategoryId,$CategoryIds)){
				continue;
			}
			$CategoryIds[] = $CategoryId;
		}
	}
	if(empty($CategoryIds)){
		echo json_encode(array('status' => false,'message' => '缺少相册信息'));
		exit();
	}
	if(array_key_exists('ImageIds',$_POST) && is_array($_POST['ImageIds'])){
		foreach($_POST['ImageIds'] as $item){
			$ImageId = intval($item);
			if(in_array($ImageId,$ImageIds)){
				continue;
			}
			$ImageIds[] = $ImageId;
		}
	}
	if(empty($ImageIds)){
		echo json_encode(array('status' => false,'message' => '缺少图片信息'));
		exit();
	}
	
	$sth = $pdomysql -> prepare('insert into tbCategoryImageRelationInfo(CategoryId,ImageId)values(:CategoryId,:ImageId)');
	
	$errors = array();
	
	foreach($CategoryIds as $CategoryId){
		foreach($ImageIds 	as $ImageId){
			$sth -> execute(array('CategoryId' => $CategoryId,'ImageId' => $ImageId));
			$error = $sth -> errorInfo();
			if($error[1] > 0 && $error[1] !=1062){
				$errors[] = "[CategoryId:$CategoryId,ImageId:$ImageId]".$error[2];
				
			}
		}
	}
	
	header('Content-Type: application/json;');
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
		exit();
	}	
	echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	exit();