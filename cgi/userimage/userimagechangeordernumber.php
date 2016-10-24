<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json');
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	if(empty($Id)){
		echo json_encode(array('status' => false,'message' => '未选择图片'));
		exit();		
	}
	
	$User_Id = 0;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	if(empty($User_Id)){
		echo json_encode(array('status' => false,'message' => '未选择用户'));
		exit();
	}
	
	$OrderType = 0;
	if(array_key_exists('OrderType',$_POST) && is_numeric($_POST['OrderType'])){
		$OrderType = intval($_POST['OrderType']);
	}
	if(empty($OrderType)){
		echo json_encode(array('status' => false,'message' => '未选择移动类型'));
		exit();
	}
	
	$sql = 'select Id,OrderNumber from tbUserImageInfo where User_Id='.$User_Id;
	if($OrderType>0){
		$sql = $sql.' order by OrderNumber asc,Id asc;';
	}
	if($OrderType<0){
		$sql = $sql.' order by OrderNumber desc,Id desc;';
	}
	
	$sth = $pdomysql -> prepare($sql);
	$sth -> execute();
	
	$timespan = date('Y-m-d H:i:s',time());
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	$tag = null;
	$group = array();
	$total = $th -> rowCount();
	if(!empty($errors)){
		foreach($th -> fetchAll(PDO::FETCH_ASSOC) as $key = >$val){
			if($val['Id'] == $Id){
				$tag = $val;
			}
			if(empty($tag)){
				$group[] = 'Update from tbUserImageInfo set OrderNumber ='.$key.'where Id = '.$val['Id'];
			}else{
				$group[] = 'Update from tbUserImageInfo set OrderNumber ='.$key.'where Id = '.$val['Id'];
			}
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();