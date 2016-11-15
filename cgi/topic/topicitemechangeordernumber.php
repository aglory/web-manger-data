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
		echo json_encode(array('status' => false,'message' => '未选择专题项'));
		exit();		
	}
	
	$Topic_Id = 0;
	if(array_key_exists('Topic_Id',$_POST) && is_numeric($_POST['Topic_Id'])){
		$Topic_Id = intval($_POST['Topic_Id']);
	}
	if(empty($Topic_Id)){
		echo json_encode(array('status' => false,'message' => '未选择专题项'));
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
	
	$sql = 'select Id,Topic_Id from tbTopicItemInfo where Topic_Id='.$Topic_Id;
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
		$sthChangeOrderNumber = $pdomysql -> prepare('Update tbTopicItemInfo set OrderNumber = :OrderNumber where Id = :Id');
		foreach($sth -> fetchAll(PDO::FETCH_ASSOC) as $val){			
			if($val['Id'] == $Id){			
				$indexCurrent +=  $OrderType;
				$itemChange = $val;
				continue;
			}
			if(empty($itemChange)){
				$sthChangeOrderNumber -> execute(array('Id' => $val['Id'],'OrderNumber' => $indexCurrent));
			}else{
				$sthChangeOrderNumber -> execute(array('Id' => $itemChange['Id'],'OrderNumber' => $indexCurrent));
				$sthChangeOrderNumber -> execute(array('Id' => $val['Id'],'OrderNumber' => $indexCurrent - $OrderType));
				$itemChange = null;
			}
			$indexCurrent +=  $OrderType;
		}
		if(!empty($itemChange)){
			$sthChangeOrderNumber -> execute(array('Id' => $itemChange['Id'],'OrderNumber' => $indexCurrent));
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	exit();