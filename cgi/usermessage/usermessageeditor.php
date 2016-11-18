<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	$id = 0;
	if(array_key_exists('id',$_POST) && is_numeric($_POST['id'])){
		$id = intval($_POST['id']);
	}
	
	$sth = $pdomysql -> prepare('select tbUserMessageInfo.*'.
	',UserInfo.Name as User_Name,UserInfo.NickName as User_NickName'.
	',SenderInfo.Name as Sender_Name,SenderInfo.NickName as Sender_NickName'.
    ' from tbUserMessageInfo left join tbUserInfo as UserInfo on tbUserMessageInfo.User_Id = UserInfo.Id left join tbUserInfo as SenderInfo on tbUserMessageInfo.Sender_Id = SenderInfo.Id where tbUserMessageInfo.Id = '.$id);
	
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