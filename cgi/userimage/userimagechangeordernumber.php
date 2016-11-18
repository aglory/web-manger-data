<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');
	
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
	switch($OrderType){
		case 1: $sql = $sql.' order by OrderNumber asc,Id asc;'; break;
		case -1:$sql = $sql.' order by OrderNumber desc,Id desc;'; break;
	}
	
	$sth = $pdomysql -> prepare($sql);
	$sth -> execute();
	
	$timespan = date('Y-m-d H:i:s',time());
		
	$errors = array();

	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	$itemChange = null;
	$total = $sth -> rowCount();
	$indexCurrent = 0;
	$indexLast = 0;
	
	
	if(empty($errors)){
		switch($OrderType){
			case 1:$indexCurrent = 1;break;
			case -1:$indexCurrent = $total;break;
		}
		$sthChangeOrderNumber = $pdomysql -> prepare('Update tbUserImageInfo set OrderNumber = :OrderNumber,DateTimeModify = :DateTimeModify where Id = :Id');
		foreach($sth -> fetchAll(PDO::FETCH_ASSOC) as $val){			
			if($val['Id'] == $Id){			
				$indexCurrent +=  $OrderType;
				$itemChange = $val;
				continue;
			}
			if(empty($itemChange)){
				$sthChangeOrderNumber -> execute(array('Id' => $val['Id'],'OrderNumber' => $indexCurrent, 'DateTimeModify' => $timespan));
			}else{
				$sthChangeOrderNumber -> execute(array('Id' => $itemChange['Id'],'OrderNumber' => $indexCurrent, 'DateTimeModify' => $timespan));
				$sthChangeOrderNumber -> execute(array('Id' => $val['Id'],'OrderNumber' => $indexCurrent - $OrderType, 'DateTimeModify' => $timespan));
				$itemChange = null;
			}
			$indexCurrent +=  $OrderType;
		}
		if(!empty($itemChange)){
			$sthChangeOrderNumber -> execute(array('Id' => $itemChange['Id'],'OrderNumber' => $indexCurrent, 'DateTimeModify' => $timespan));
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();