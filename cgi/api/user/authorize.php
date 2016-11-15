<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	header('Content-Type: application/json;');
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$account = '';$password = '';$sex = 0;
	if(array_key_exists('Account',$_POST)){
		$account = $_POST['Account'];
	}
	if(array_key_exists('Password',$_POST)){
		$password = $_POST['Password'];
	}
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$sex = $_POST['Sex'];
	}
	
	if(empty($account) || empty($password)){
		echo json_encode(array('code' => 400,'status' => false,'message' => '缺少登录账号/密码'));
		exit(1);
	}
	
	$sth = $pdomysql -> prepare('select Id,Password,Salt from tbAccountInfo where Account = :Account and Status = :Status');
	$sth -> execute(array('Account' => $account,'Status' => 1));
	foreach($sth -> fetchAll() as $item){
		if($item['Password'] == md5(md5($password).$item['Salt'])){
			CurrentUserId($item['Id']);
			echo json_encode(array('code' => 200,'status' => true,'session_id' => session_id(),'session_name' => session_name()));
			exit(1);
		}
		echo json_encode(array('code' => 200,'status' => false,'message' => '用户名或密码错误'));
		exit(1);
	}
	
	
	$timespan = date('Y-m-d H:i:s',time());
	$sth = $pdomysql -> prepare('insert into tbAccountInfo(`Account`,`Name`,`Password`,`Salt`,`RoleId`,`SourceId`,`Status`,`DateTimeCreate`,`DateTimeModify`)values(:Account,:Name,:Password,:Salt,:RoleId,:SourceId,:Status,:DateTimeCreate,:DateTimeModify);');
		
	$salt = rand(1,0x7FFFFFFF);
	
	$sth -> execute(array(
		'Account' => $account,
		'Name' => $account,
		'Password' => md5(md5($password).$salt),
		'Salt' => $salt,
		'RoleId' => 0,
		'SourceId' => 0,
		'Status' => 1,
		'DateTimeCreate' => $timespan,
		'DateTimeModify' => $timespan
	));
	
	$errors = $sth -> errorInfo();
	
	$error = $errors[1];
	if($error > 0){
		echo json_encode(array('code' => 550,'status' => false,'message' => '用户名或密码错误'));
		die(1);
	}
	
	$id = $pdomysql -> lastInsertId();
	
	CurrentUserId($id);
	
	$sthUser = $pdomysql -> prepare('insert into tbUserInfo(Id,Name,NickName,Sex,Img,BodyHeight,BodyWeight,EducationalHistory,Constellation,CivilState,Career,Description,ContactWay,ContactQQ,ContactEmail,ContactMobile,InterestAndFavorites,DateTimeModify,Birthday)values(:Id,:Name,:NickName,:Sex,:Img,:BodyHeight,:BodyWeight,:EducationalHistory,:Constellation,:CivilState,:Career,:Description,:ContactWay,:ContactQQ,:ContactEmail,:ContactMobile,:InterestAndFavorites,:DateTimeModify,:Birthday)');
	$sthUser -> execute(array(
		'Id' => $id,
		'Name' => $account,
		'NickName' => $account,
		'Sex' => empty($Sex)?0:1,
		'Img' => null,
		'BodyHeight' => 0,
		'BodyWeight' => 0,
		'EducationalHistory' => 0,
		'Constellation' => 0,
		'CivilState' => 1,
		'Career' => null,
		'Description' => null,
		'ContactWay' => null,
		'ContactQQ' => null,
		'ContactEmail' => null,
		'ContactMobile' => null,
		'InterestAndFavorites' => null,
		'DateTimeModify' => $timespan,
		'Birthday' => null
	));
	
	$sthUserStatisticsInfo = $pdomysql -> prepare('insert into tbUserStatisticsInfo(Id,CountFollow,CountFollowed,CountView,CountScore,CountPoint,CountMessage)values(:Id,:CountFollow,:CountFollowed,:CountView,:CountScore,:CountPoint,:CountMessage)');
	
	$sthUserStatisticsInfo -> execute(array(
		'Id' => $id,
		'CountFollow' => 0,
		'CountFollowed' => 0,
		'CountView' => 0,
		'CountScore' => 0,
		'CountPoint' => 0,
		'CountMessage' => 0
	));
	
	$sthUserConfiguration = $pdomysql -> prepare('insert into tbUserConfiguration(Id,ConfigurationProtected,ConfigurationVewCost)values(:Id,:ConfigurationProtected,:ConfigurationVewCost)');
	$sthUserConfiguration -> execute(array(
		'Id' => $id,
		'ConfigurationProtected' => 0,
		'ConfigurationVewCost' => 0
	));
	
	echo json_encode(array('code' => 200,'status' => true,'session_id' => session_id(),'session_name' => session_name()));
	