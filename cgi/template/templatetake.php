<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	header('Content-Type: application/json;');
	
	$templateGroupId = 0;
	if(array_key_exists('TemplateGroupId',$_POST) && is_numeric($_POST['TemplateGroupId'])){
		$templateGroupId = intval($_POST['TemplateGroupId']);
	}
	
	$sthGet = $pdomysql -> prepare('select * from tbTemplateInfo where TemplateGroupId = :TemplateGroupId order by Target * rand() limit 0,1');
	$sthGet -> execute(array('TemplateGroupId' => $templateGroupId));
	
	$errors = array();
	$error = $sthGet -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	$model = $sthGet -> fetch(PDO::FETCH_ASSOC);
	
	$sthUpdate = $pdomysql -> prepare('update tbTemplateInfo set Target = Target + 1 where Id = :Id');
	if(!empty($model)){
		$sthUpdate -> execute(array('Id' => $model['Id']));
	}
	$error = $sthUpdate -> errorInfo();
	if($error[1] > 0){
		echo json_encode(array('status' => false,'message' => $error[2]));
		exit();
	}
	
	echo json_encode(array('status' => true,'model' => $model));
	exit();