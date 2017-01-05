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
	
	header('Content-Type: application/json;');
	
	$timespan = date('Y-m-d H:i:s',time());
	
	$sthSelect = $pdomysql -> prepare('select * from tbImageInfo where Id = :Id');
	$sthSelect -> execute(array('Id' => $Id));
	$sthUpdate = $pdomysql -> prepare('update tbImageInfo set Src = :Src,Scrawled =1,DateTimeModify = :DateTimeModify where Id = :Id');
	foreach($sthSelect -> fetchAll(PDO::FETCH_ASSOC) as $item){
		$id = $item['Id'];
		$src = $item['Img'];
		$path = $item['Src'];
		if(empty($path)){
			$extral = '';		
			if(preg_match('/[^\/]\/\w+(\.\w+)/',$src,$extral)){
				$extral = $extral[1];
			}
			$serverpath = implode(DIRECTORY_SEPARATOR,array(GetLoadPath(),md5(time()).$extral));
		}else{
			$serverpath = $path;
			if(!file_exists($serverpath)){
				echo json_encode(array('status' => false,'message' => '路径'.$serverpath.'不存在'));
				exit();
			}
		}
		try{
			$file = fopen($serverpath,'wb');
		}catch(Exception $e){
			echo json_encode(array('status' => false,'message' =>  $e->getMessage()));
			exit();
		}
		if($file !== false){
			fwrite($file,file_get_contents($src));
		}
		fclose($file);
		$sthUpdate -> execute(array('Id' => $id,'Src' => $serverpath,'DateTimeModify' => $timespan));
		$error = $sthUpdate -> errorInfo();
		if($error[1] > 0){
			echo json_encode(array('status' => false,'message' => $error[2]));
			exit();
		}
		echo json_encode(array('status' => true));
		exit();
	}

	exit();
	
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