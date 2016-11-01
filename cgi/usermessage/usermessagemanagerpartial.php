<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = 'Id desc';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array('Id','User_Id','Sender_Id','Flag','Message','DateTimeCreate','DateTimeModify','Status');

	
	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	if(array_key_exists('PageSort',$_POST) && !empty($_POST['PageSort'])){
		$PageSort = $_POST['PageSort'];
	}
	if(array_key_exists('PageItems',$_POST) && !empty($_POST['PageItems'])){
		$items = array();
		foreach(explode(',',$_POST['PageItems']) as $item){
			if(in_array($item,$PageItems))
				$items[] = 'tbUserMessageInfo.'.$item;
			else if($item == 'User_Name'){
				$items[] = 'UserInfo.Name as User_Name';
			}else if($item == 'Sender_Name'){
				$items[] = 'SenderInfo.Name as Sender_Name';
			}
		}
		if(!empty($items))
			$PageItems = $items;
		else
			$PageItems = array('1');
	}else{
		$items = array();
		foreach($PageItems as $item){
			$items[] = 'tbUserMessageInfo.'.$item;
		}
		$items[] = 'UserInfo.Name as User_Name';
		$items[] = 'SenderInfo.Name as Sender_Name';
		
		$PageItems = $items;
	}

	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = empty($PageSort)?'':" order by $PageSort ";

	$whereSql = array('1=1');
	$whereParams = array();

	if(array_key_exists('User_Name',$_POST) && !empty($_POST['User_Name'])){
		$whereSql[] = 'UserInfo.Name like :User_Name';
		$whereParams['User_Name'] = '%'.$_POST['User_Name'].'%';
	}
	if(array_key_exists('Sender_Name',$_POST) && !empty($_POST['Sender_Name'])){
		$whereSql[] = 'SenderInfo.Name like :Sender_Name';
		$whereParams['Sender_Name'] = '%'.$_POST['Sender_Name'].'%';
	}
	if(array_key_exists('DateTimeCreateStart',$_POST) && !empty($_POST['DateTimeCreateStart'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeCreate >= :DateTimeCreateStart';
		$whereParams['DateTimeCreateStart'] = $_POST['DateTimeCreateStart'];
	}
	if(array_key_exists('DateTimeCreateEnd',$_POST) && !empty($_POST['DateTimeCreateEnd'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeCreate <= date_add(:DateTimeCreateEnd,INTERVAL 1 DAY)';
		$whereParams['DateTimeCreateEnd'] = $_POST['DateTimeCreateEnd'];
	}
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbUserMessageInfo.Status = '.$_POST['Status'];
	}
	
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserMessageInfo left join tbUserInfo as UserInfo on tbUserMessageInfo.User_Id = UserInfo.Id left join tbUserInfo as SenderInfo on tbUserMessageInfo.Sender_Id = SenderInfo.Id';
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql)."$PageOrderBy limit $PageStart,$PageEnd;");
	$sthCount = $pdomysql -> prepare('select count(1) from '.$tbFrom.' where '.implode(' and ',$whereSql));

	if(empty($whereParams)){
		$sthList -> execute();
		$sthCount -> execute();
	}else{
		$sthList -> execute($whereParams);
		$sthCount -> execute($whereParams);
	}

	$errors = array();
	
	$error = $sthList -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	$error = $sthCount -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}

	$result = array();
	

	if(empty($errors)){
		$result['status'] = true;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['status'] = false;
		$result['recordCount'] = 0; 
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();