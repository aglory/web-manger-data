<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	header('Content-Type: application/json;');
	
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	$Title = '';
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$Title = $_POST['Title'];
	}
	$Message = '';
	if(array_key_exists('Message',$_POST) && !empty($_POST['Message'])){
		$Message = $_POST['Message'];
	}
	
	
	if(empty($Id)){
		$Topic_Id = 0;
		if(array_key_exists('Topic_Id',$_POST) && is_numeric($_POST['Topic_Id'])){
			$Topic_Id = $_POST['Topic_Id'];
		}
		
		$img = GetServerPath();
		
		if(empty($img)){
			$sth = $pdomysql -> prepare('insert into tbTopicItemInfo(Topic_Id,OrderNumber,Title,Message)values(:Topic_Id,:OrderNumber,:Title,:Message)');
			$sth -> execute(array(
				'Topic_Id' => $Topic_Id,
				'OrderNumber' => 0,
				'Title' => $Title,
				'Message' => $Message
			));
			
			$error = $sth -> errorInfo();
			if($error[1] > 0){
				echo json_encode(array('status' => false,'message' => $error[2]));
				exit();
			}
		}
		
		$errors = array();
		$sth = $pdomysql -> prepare('insert into tbTopicItemInfo(Topic_Id,OrderNumber,Img,Title,Message)values(:Topic_Id,:OrderNumber,:Img,:Title,:Message)');
		foreach($img as $key => $val){
			$sth -> execute(array(
				'Topic_Id,' => $Topic_Id,
				'OrderNumber' => $key,
				'Img' => $val,
				'Title' => $Title,
				'Message' => $Message
			));
			$error = $sth -> errorInfo();
			if($error[1] > 0){
				$errors[] = $error[2];
			}
		}
		if(empty($errors)){
			echo json_encode(array('status' => true));
		}else{
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		}
		exit();
	}
	
	$img = GetServerPath();
	$sth = $pdomysql -> prepare('update tbTopicItemInfo set Title=:Title,Message=:Message'.(empty($img)?'':',Img=:Img').' where Id = :Id;');
	
	if(empty($img)){
		$sth -> execute(array(
			'Id' => $Id,
			'Title' => $Title,
			'Message' => $Message,
		));
		$error = $sth -> errorInfo();
		if($error[1] > 0){
			echo json_encode(array('status' => false,'message' => $error[2]));
			exit();
		}
		echo json_encode(array('status' => true));
		exit();
	}
	
	$errors = array();
	foreach($img as $key => $val){
		$sth -> execute(array(
			'Id' => $Id,
			'Title' => $Title,
			'Message' => $Message,
			'Img' => $val,
		));
		$error = $sth -> errorInfo();
		
		if($error[1] > 0){
			$errors[] = $error[2];
		}
	}
	
	if(empty($errors)){
		echo json_encode(array('status' => true));
		exit();
	}
	
	echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
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
			$extral = '';
			if(preg_match('/[^\/]\/\w+(\.\w+)/',$Src,$extral)){
				$extral = $extral[1];
			}
			$serverpath = implode(DIRECTORY_SEPARATOR,array($directory,md5(time()).$extral));
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