<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$AccountId = 0;
	if(array_key_exists('AccountId',$_POST) && is_numeric($_POST['AccountId'])){
		$AccountId = intval($_POST['AccountId']);
	}
	$Name = '';
	if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
		$Name = $_POST['Name'];
	}
	$NickName = '';
	if(array_key_exists('NickName',$_POST) && !empty($_POST['NickName'])){
		$NickName = $_POST['NickName'];
	}
	$Sex = 0;
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$Sex = intval($_POST['Sex']) > 0 ? 1 : 0;
	}
	$BodyHeight = 0;
	if(array_key_exists('BodyHeight',$_POST) && is_numeric($_POST['BodyHeight'])){
		$BodyHeight = intval($_POST['BodyHeight']);
	}
	$BodyWeight = 0;
	if(array_key_exists('BodyWeight',$_POST) && is_numeric($_POST['BodyWeight'])){
		$BodyWeight = intval($_POST['BodyWeight']);
	}
	$EducationalHistory = 0;
	if(array_key_exists('EducationalHistory',$_POST) && is_numeric($_POST['EducationalHistory'])){
		$EducationalHistory = intval($_POST['EducationalHistory']);
	}
	$Constellation = 0;
	if(array_key_exists('Constellation',$_POST) && is_numeric($_POST['Constellation'])){
		$Constellation = intval($_POST['Constellation']);
	}
	$CivilState = 0;
	if(array_key_exists('CivilState',$_POST) && is_numeric($_POST['CivilState'])){
		$CivilState = intval($_POST['CivilState']);
	}
	$ContactWay = '';
	if(array_key_exists('ContactWay',$_POST) && !empty($_POST['ContactWay'])){
		$ContactWay = $_POST['ContactWay'];
	}
	$ContactQQ = '';
	if(array_key_exists('ContactQQ',$_POST) && !empty($_POST['ContactQQ'])){
		$ContactQQ = $_POST['ContactQQ'];
	}
	$ContactEmail = '';
	if(array_key_exists('ContactEmail',$_POST) && !empty($_POST['ContactEmail'])){
		$ContactEmail = $_POST['ContactEmail'];
	}
	$ContactMobile = '';
	if(array_key_exists('ContactMobile',$_POST) && !empty($_POST['ContactMobile'])){
		$ContactMobile = $_POST['ContactMobile'];
	}
	$Career = '';
	if(array_key_exists('Career',$_POST) && !empty($_POST['Career'])){
		$Career = $_POST['Career'];
	}
	$InterestAndFavorites = '';
	if(array_key_exists('InterestAndFavorites',$_POST) && !empty($_POST['InterestAndFavorites'])){
		$InterestAndFavorites = $_POST['InterestAndFavorites'];
	}
	$Description = '';
	if(array_key_exists('Description',$_POST) && !empty($_POST['Description'])){
		$Description = $_POST['Description'];
	}
	$Birthday = null;
	if(array_key_exists('Birthday',$_POST) && !empty($_POST['Birthday'])){
		$Birthday = $_POST['Birthday'];
	}
	
	header('Content-Type: application/json');	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	if(empty($Id)){
		$sthUserInfo = $pdomysql -> prepare('insert into tbUserInfo(AccountId,Name,NickName,Sex,BodyHeight,BodyWeight,EducationalHistory,Constellation,CivilState,Career,ContactWay,ContactQQ,ContactEmail,ContactMobile,InterestAndFavorites,Description,DateTimeCreate,DateTimeModify,Birthday)values(:AccountId,:Name,:NickName,:Sex,:BodyHeight,:BodyWeight,:EducationalHistory,:Constellation,:CivilState,:Career,:ContactWay,:ContactQQ,:ContactEmail,:ContactMobile,:InterestAndFavorites,:Description,:DateTimeCreate,:DateTimeModify,:Birthday);');
		$sthUserInfo -> execute(array(
			'AccountId' => $AccountId,
			'Name' => $Name,
			'NickName' => $NickName,
			'Sex' => $Sex,
			'BodyHeight' => $BodyHeight,
			'BodyWeight' => $BodyWeight,
			'EducationalHistory' => $EducationalHistory,
			'Constellation' => $Constellation,
			'CivilState' => $CivilState,
			'Career' => $Career,
			'ContactWay' => $ContactWay,
			'ContactQQ' => $ContactQQ,
			'ContactEmail' => $ContactEmail,
			'ContactMobile' => $ContactMobile,
			'InterestAndFavorites' => $InterestAndFavorites,
			'Description' => $Description,
			'DateTimeCreate' => $timespan,
			'DateTimeModify' => $timespan,
			'Birthday' => $Birthday
		));
		
		$Id = $pdomysql -> lastInsertId();

		$errorUserInfo = $sthUserInfo -> errorInfo();
		if($errorUserInfo[1] > 0){
			$errors[] = $errorUserInfo[2];
		}
		
		if(!empty($errors)){
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
			exit();
		}
		
		$sthUserStatistics = $pdomysql -> prepare('insert into tbUserStatisticsInfo(Id,CountFollow,CountFollowed,CountView,CountScore,CountPoint,CountMessage)values(:Id,:CountFollow,:CountFollowed,:CountView,:CountScore,:CountPoint,:CountMessage)');
		$sthUserStatistics -> execute(array(
			'Id' => $Id,
			'CountFollow' => 0,
			'CountFollowed' => 0,
			'CountView' => 0,
			'CountScore' => 0,
			'CountPoint' => 0,
			'CountMessage' => 0
		));

		$errorUserStatistics = $sthUserStatistics -> errorInfo();
		if($errorUserStatistics[1] > 0){
			$errors[] = $errorUserStatistics[2];
		}
		
		if(!empty($errors)){
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
			exit();
		}
		
		$ConfigurationProtected = 0;
		if(array_key_exists('ConfigurationProtected',$_POST) && is_numeric($_POST['ConfigurationProtected'])){
			$ConfigurationProtected = intval($_POST['ConfigurationProtected']);
		}
		
		$ConfigurationVewCost = 0;
		if(array_key_exists('ConfigurationVewCost',$_POST) && is_numeric($_POST['ConfigurationVewCost'])){
			$ConfigurationVewCost = intval($_POST['ConfigurationVewCost']);
		}
		
		$sthUserConfiguration = $pdomysql -> prepare('insert into tbUserConfiguration(Id,ConfigurationProtected,ConfigurationVewCost)value(:Id,:ConfigurationProtected,:ConfigurationVewCost);');
		$sthUserConfiguration -> execute(array(
			'Id' => $Id,
			'ConfigurationProtected' => $ConfigurationProtected,
			'ConfigurationVewCost' => $ConfigurationVewCost,	
		));
		
		$errorUserConfiguration = $sthUserConfiguration -> errorInfo(); 
		if($errorUserConfiguration[1] > 0){
			$errors[] = $errorUserConfiguration[2];
		}
		
		if(!empty($errors)){
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
			exit();
		}
		
		echo json_encode(array('status' => true));
		exit();
	}
		
	$sthUserInfo = $pdomysql -> prepare('update tbUserInfo set AccountId=:AccountId,Name=:Name,NickName=:NickName,Sex=:Sex,BodyHeight=:BodyHeight,BodyWeight=:BodyWeight,EducationalHistory=:EducationalHistory,Constellation=:Constellation,CivilState=:CivilState,Career=:Career,ContactWay=:ContactWay,ContactQQ=:ContactQQ,ContactEmail=:ContactEmail,ContactMobile=:ContactMobile,InterestAndFavorites=:InterestAndFavorites,Description=:Description,DateTimeModify=:DateTimeModify,Birthday=:Birthday where Id = :Id;');
	$sthUserInfo -> execute(array(
		'Id' => $Id,
		'AccountId' => $AccountId,
		'Name' => $Name,
		'NickName' => $NickName,
		'Sex' => $Sex,
		'BodyHeight' => $BodyHeight,
		'BodyWeight' => $BodyWeight,
		'EducationalHistory' => $EducationalHistory,
		'Constellation' => $Constellation,
		'CivilState' => $CivilState,
		'Career' => $Career,
		'ContactWay' => $ContactWay,
		'ContactQQ' => $ContactQQ,
		'ContactEmail' => $ContactEmail,
		'ContactMobile' => $ContactMobile,
		'InterestAndFavorites' => $InterestAndFavorites,
		'Description' => $Description,
		'DateTimeModify' => $timespan,
		'Birthday' => $Birthday
	));

	$errorUserInfo = $sthUserInfo -> errorInfo();
	
	$errors = array();
	
	if($errorUserInfo[1] > 0){
		$errors[] = $errorUserInfo[2];
	}
	
	if(!empty($errors)){
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		exit();
	}
	
	echo json_encode(array('status' => true));
	exit();