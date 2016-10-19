<?php
	if(!defined('Execute')) exit(0);
	if(empty(CurrentUserId())){
		Render('home','login');
		exit(1);
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;

	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	if(array_key_exists('PageSort',$_POST)){
		$PageSort = $_POST['PageSort'];
	}

	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageStart + $PageSize;
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
	if(array_key_exists('ModifyDateTimeStart',$_POST) && !empty($_POST['ModifyDateTimeStart'])){
		$whereSql[] = 'ModifyDateTime >= :ModifyDateTimeStart';
		$whereParams['ModifyDateTimeStart'] = $_POST['ModifyDateTimeStart'];
	}
	if(array_key_exists('ModifyDateTimeEnd',$_POST) && !empty($_POST['ModifyDateTimeEnd'])){
		$whereSql[] = 'ModifyDateTime <= date_add(:ModifyDateTimeEnd,INTERVAL 1 DAY)';
		$whereParams['ModifyDateTimeEnd'] = $_POST['ModifyDateTimeEnd'];
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