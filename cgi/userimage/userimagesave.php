<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json;');
	
	$SrcList = GetServerPath();
	
	if(empty($SrcList)){
		echo json_encode(array('status' => false,'message' => '缺少图片信息'));
		exit();
	}
	
	$User_Id = 0;
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$User_Id = intval($_POST['User_Id']);
	}
	$Description = '';
	if(array_key_exists('Description',$_POST) && !empty($_POST['Description'])){
		$Description = $_POST['Description'];
	}
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sth = $pdomysql -> prepare('insert into tbUserImageInfo(User_Id,OrderNumber,Src,Description,IsDefault,Status,DateTimeCreate,DateTimeModify)values(:User_Id,:OrderNumber,:Src,:Description,:IsDefault,:Status,:DateTimeCreate,:DateTimeModify);');
		
	$errors = array();
	
	foreach($SrcList as $val => $key){
		$sth -> execute(array(
			'User_Id' => $User_Id,
			'OrderNumber' => $key,
			'Src' => $val,
			'Description' => $Description,
			'IsDefault' => !$key,
			'Status' => 1,
			'DateTimeCreate' => $timespan,
			'DateTimeModify' => $timespan
		));
		$error = $sth -> errorInfo();
		if($error[0] > 0){
			$errors[] = $error[2];
		}
	}

	
	if(empty($errors)){
		echo json_encode(array('status' => true,'src' => $SrcList));
	}else{
		echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
	}
	
	exit();
	
	function GetServerPath(){
		$ls = array();
		$imgCount = 0;
		$directory = GetLoadPath();
		
		$Src = '';
		if(array_key_exists('Src',$_POST) && !empty($_POST['Src'])){
			$Src = $_POST['Src'];
		}
		if(!empty($Src)){
			$serverpath = implode(DIRECTORY_SEPARATOR,array($directory,md5(time()).'.gif'));
			$file = fopen($serverpath,'wb');
			if($file !== false){
				fwrite($file,file_get_contents($Src));
				fclose($file);
			}
			$imgCount ++;
			$ls[] = $serverpath;
		}

		if(!empty($_FILES)){
			foreach($_FILES as $file){
				$serverpath = implode(DIRECTORY_SEPARATOR,array($directory,md5(time() + $imgCount*60*60*24).'.gif'));
				copy($file['tmp_name'],$serverpath);
				$imgCount ++;
				$ls[] = $serverpath;
			}
		}
		return $ls;
	}
	
	function GetLoadPath(){
		$p = implode(DIRECTORY_SEPARATOR,array('store',date('Y-m-d',time())));
		if(!file_exists($p)){
			mkdir($p);
		}
		return $p;
	}