<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
		
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
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
	
	if(empty($Id)){
	
		$SrcList = GetServerPath();
		
		if(empty($SrcList) && empty($Id)){
			echo json_encode(array('status' => false,'message' => '缺少图片信息'));
			exit();
		}
		
		$sth = $pdomysql -> prepare('insert into tbUserImageInfo(User_Id,OrderNumber,Src,Description,IsDefault,Status,DateTimeCreate,DateTimeModify)values(:User_Id,:OrderNumber,:Src,:Description,:IsDefault,:Status,:DateTimeCreate,:DateTimeModify);');
			
		$errors = array();
		
		foreach($SrcList as $key => $val){
			$sth -> execute(array(
				'User_Id' => $User_Id,
				'OrderNumber' => $key,
				'Src' => $val,
				'Description' => $Description,
				'IsDefault' => 0,
				'Status' => 1,
				'DateTimeCreate' => $timespan,
				'DateTimeModify' => $timespan
			));
			
			$error = $sth -> errorInfo();
			if($error[1] > 0){
				$errors[] = $error[2];
			}
		}

		
		if(empty($errors)){
			echo json_encode(array('status' => true,'ls' => $SrcList,'file' => $_FILES));
		}else{
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		}
		
		exit();
	}
	
	$sth = $pdomysql -> prepare('update tbUserImageInfo set Description = :Description,DateTimeModify = :DateTimeModify where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id,
		'Description' => $Description,
		'DateTimeModify' => $timespan
	));
	
	$errors = array();
	
	$error = $sth -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
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

		if(!empty($_FILES) && array_key_exists('imgs',$_FILES)){
			for($i = count($_FILES['imgs']['name']) -1 ;$i>=0;$i--){
				if($_FILES['imgs']['size'][$i] == 0)
					continue;
				$extral = '';
				if(preg_match('/\.\w+$/',$_FILES['imgs']['name'][$i],$extral)){
					$extral = $extral[0];
				}
				$serverpath = implode(DIRECTORY_SEPARATOR,array($directory,md5(time() + $imgCount*60*60*24).$extral));
				copy($_FILES['imgs']['tmp_name'][$i],$serverpath);
				$imgCount ++;
				$ls[] = $serverpath;
			}
		}
		return $ls;
	}
	
	function GetLoadPath(){
		if(!file_exists('store')){
			mkdir('store');
		}
		$p = implode(DIRECTORY_SEPARATOR,array('store',date('Y-m-d',time())));
		if(!file_exists($p)){
			mkdir($p);
		}
		return $p;
	}