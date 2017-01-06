<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	
	require_once Lib('pdo');
		
	$Id = 0;
	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$Id = intval($_POST['Id']);
	}
	
	$Title = '';
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$Title = $_POST['Title'];
	}
	$Img = '';
	if(array_key_exists('Img',$_POST) && !empty($_POST['Img'])){
		$Img = $_POST['Img'];
	}
	$Src = '';
	if(array_key_exists('Src',$_POST) && !empty($_POST['Src'])){
		$Src = $_POST['Src'];
	}
	$Level = 0;
	if(array_key_exists('Level',$_POST) && is_numeric($_POST['Level'])){
		$Level = intval($_POST['Level']);
	}
	$Scrawled = 0;
	if(array_key_exists('Scrawled',$_POST) && is_numeric($_POST['Scrawled'])){
		$Scrawled = intval($_POST['Scrawled']);
	}
	
	
	$timespan = date('Y-m-d H:i:s',time());
	
	if(empty($Id)){
		$sth = $pdomysql -> prepare('insert into tbImageInfo(Title,Tag,Img,Scrawled,Src,Level,DateTimeCreate,DateTimeModify,Status)values(:Title,:Tag,:Img,:Scrawled,:Src,:Level,:DateTimeCreate,:DateTimeModify,:Status);');
			
		$errors = array();
		
		$sth -> execute(array(
			'Title' => $Title,
			'Tag' => $Tag,
			'Img' => $Img,
			'Scrawled' => $Scrawled,
			'Src' => $Src,
			'Level' => $Level,
			'DateTimeCreate' => $timespan,
			'DateTimeModify' => $timespan,
			'Status' => 1
		));
		
		if(empty($errors)){
			echo json_encode(array('status' => true));
		}else{
			echo json_encode(array('status' => false,'message' => implode('\r\n',$errors)));
		}
		
		exit();
	}
	

	$sth = $pdomysql -> prepare('update tbImageInfo set Title=:Title,Img=:Img,Src=:Src,Scrawled=:Scrawled,Level=:Level,DateTimeModify=:DateTimeModify where Id = :Id;');
	$sth -> execute(array(
		'Id' => $Id,
		'Title' => $Title,
		'Img' => $Img,
		'Src' => $Src,
		'Scrawled' => $Scrawled,
		'Level' => $Level,
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