<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbUserMessageInfo' => array('Id','User_Id','Sender_Id','Flag','Message','DateTimeCreate','DateTimeModify','Status_User','Status_Sender','ReplayId')
	);
	
	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	
	if(array_key_exists('PageItems',$_POST) && !empty($_POST['PageItems'])){
		foreach(explode(',',$_POST['PageItems']) as $item){
			if($item == 'User_Name'){
				$PageItems[] = 'UserInfo.Name as User_Name';
				continue;
			}
			if($item == 'Sender_Name'){
				$PageItems[] = 'SenderInfo.Name as Sender_Name';
				continue;
			}
			foreach($PageColumns as $table => $columns){
				if(in_array($item,$columns)){
					$PageItems[] = $table.'.'.$item;
					break;
				}
			}
		}
	}else{
		$PageItems[] = 'UserInfo.Name as User_Name';
		$PageItems[] = 'SenderInfo.Name as Sender_Name';
		foreach($PageColumns as $table => $columns){
			foreach($columns as $column){
				$PageItems[] = $table.'.'.$column;
			}
		}
	}
	if(empty($PageItems))
		$PageItems = array('1');

	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = array();
	
	
	if(array_key_exists('PageSort',$_POST) && !empty($_POST['PageSort'])){
		foreach(explode(',',$_POST['PageSort']) as $item){
			$itemgroup = explode(' ',$item);
			if(count($itemgroup)>1){
				$column_orderby = $itemgroup[0];
				$column_orderbytype = $itemgroup[1];
			}else if(count($itemgroup)>0){
				$column_orderby = $itemgroup[0];
				$column_orderbytype = 'asc';
			}else{
				continue;
			}
			if($column_orderby == 'User_Name'){
				$PageOrderBy[] = 'UserInfo.Name'.' '.$column_orderbytype;
				continue;
			}
			if($column_orderby == 'Sender_Name'){
				$PageOrderBy[] = 'SenderInfo.Name'.' '.$column_orderbytype;
				continue;
			}
			
			foreach($PageColumns as $table => $columns){
				if(in_array($column_orderby,$columns)){
					$PageOrderBy[] = $table.'.'.$column_orderby.' '.$column_orderbytype;
				}
			}
		}
	}
	
	
	$whereSql = array('tbUserMessageInfo.User_Id ='.CurrentUserId());
	$whereParams = array();	
	
	header('Content-Type: application/json;');

	if(array_key_exists('Sender_Id',$_POST) && is_numeric($_POST['Sender_Id'])){
		$whereSql[] = 'tbUserMessageInfo.Sender_Id ='.$_POST['Sender_Id'];
	}
	
	if(array_key_exists('Flag',$_POST) && is_numeric($_POST['Flag'])){
		$whereSql[] = 'tbUserMessageInfo.Flag = '.$_POST['Flag'];
	}
	
	if(array_key_exists('DateTimeCreateMin',$_POST) && !empty($_POST['DateTimeCreateMin'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeCreate >= :DateTimeCreateMin';
		$whereParams['DateTimeCreateMin'] = $_POST['DateTimeCreateMin'];
	}
	if(array_key_exists('DateTimeCreateMax',$_POST) && !empty($_POST['DateTimeCreateMax'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeCreate < date_add(:DateTimeCreateMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeCreateMax'] = $_POST['DateTimeCreateMax'];
	}
	
	if(array_key_exists('DateTimeModifyMin',$_POST) && !empty($_POST['DateTimeModifyMin'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeModify >= :DateTimeModifyMin';
		$whereParams['DateTimeModifyMin'] = $_POST['DateTimeModifyMin'];
	}
	if(array_key_exists('DateTimeModifyMax',$_POST) && !empty($_POST['DateTimeModifyMax'])){
		$whereSql[] = 'tbUserMessageInfo.DateTimeModify < date_add(:DateTimeModifyMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyMax'] = $_POST['DateTimeModifyMax'];
	}
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserMessageInfo left join tbUserInfo as UserInfo on tbUserMessageInfo.User_Id = UserInfo.Id left join tbUserInfo as SenderInfo on tbUserMessageInfo.Sender_Id = SenderInfo.Id';
	
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(',',$PageOrderBy):'')." limit $PageStart,$PageEnd;");
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
		$result['code'] = 200;
		$result['status'] = true;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['code'] = 550;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	echo json_encode($result);
	exit();