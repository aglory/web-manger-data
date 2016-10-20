<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = 'Id desc';
	$PageIndex = 1;
	$PageSize = 20;

	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	if(array_key_exists('PageSort',$_POST) && !empty($_POST['PageSort'])){
		$PageSort = $_POST['PageSort'];
	}

	$PageStart = $PageIndex - 1;
	$PageEnd = $PageSize;
	$PageOrderBy = empty($PageSort)?'':" order by $PageSort ";

	$whereSql = array('1=1');
	$whereParams = array();

	if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
		$whereSql[] = 'Name like :Name';
		$whereParams['Name'] = '%'.$_POST['Name'].'%';
	}
	if(array_key_exists('NickName',$_POST) && !empty($_POST['NickName'])){
		$whereSql[] = 'NickName like :NickName';
		$whereParams['NickName'] = '%'.$_POST['NickName'].'%';
	}
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$whereSql[] = 'Sex = '.$_POST['Sex'];
	}
	if(array_key_exists('DateTimeModifyStart',$_POST) && !empty($_POST['DateTimeModifyStart'])){
		$whereSql[] = 'DateTimeModify >= :DateTimeModifyStart';
		$whereParams['DateTimeModifyStart'] = $_POST['DateTimeModifyStart'];
	}
	if(array_key_exists('DateTimeModifyEnd',$_POST) && !empty($_POST['DateTimeModifyEnd'])){
		$whereSql[] = 'DateTimeModify <= date_add(:DateTimeModifyEnd,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyEnd'] = $_POST['DateTimeModifyEnd'];
	}
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'Status = '.$_POST['Status'];
	}
	
	
	$sthList = null;
	$sthCount = null;

	$sthList = $pdomysql -> prepare('select * from tbUserInfo where '.implode(' and ',$whereSql)."$PageOrderBy limit $PageStart,$PageEnd;");
	$sthCount = $pdomysql -> prepare('select count(1) from tbUserInfo where '.implode(' and ',$whereSql));
	if(empty($whereParams)){
		$sthList -> execute();
		$sthCount -> execute();
	}else{
		$sthList -> execute($whereParams);
		$sthCount -> execute($whereParams);
	}

	$errors = array();
	
	$error = $sthList -> errorInfo();
	if($error[0] > 0){
		$errors[] = $error[2];
	}
	$error = $sthCount -> errorInfo();
	if($error[0] > 0){
		$errors[] = $error[2];
	}

	$result = array();
	
	$result['sql'] ='select * from tbUserInfo where '.implode(' and ',$whereSql)."$PageOrderBy limit $PageStart,$PageEnd;";
	
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