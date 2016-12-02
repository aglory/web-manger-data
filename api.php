<?php
	define('Execute',true);
	define('Api',true);
	
	ob_start();
	switch(session_status()){
		case PHP_SESSION_NONE:
			session_start();
			break;
		case PHP_SESSION_DISABLED:
			break;
		case PHP_SESSION_ACTIVE:
			break;
	}
	$model="account";
	$action="login";
	
	if(array_key_exists('model',$_GET)){
		$model=$_GET['model'];
	}
	if(array_key_exists('action',$_GET)){
		$action=$_GET['action'];
	}
	
	function ValidateModelAction($model,$action){
		if($model == 'topic')
			return false;
		if($model == 'user' && $action == 'authorize')
			return false;
		return true;
	}
	
	if(ValidateModelAction($model,$action) && empty(CurrentUserId())){
		header('Content-Type: application/json;');
		echo json_encode(array('status' => false,'code' => 401));
		exit();
	}
	
	date_default_timezone_set('PRC');
	
	function GetLibFile(){
		$params = func_get_args();
		if(empty($params))return;
		array_unshift($params,'.','cgi','api');
		
		return implode(DIRECTORY_SEPARATOR,$params).'.php';
	}
	
	function CurrentUserId(){
		$params = func_get_args();
		if(!empty($params)){
			foreach($params as $item){
				$_SESSION['UserId'] = $item;
				return $item;
			}
		}
		if(empty($_SESSION)){
			return 0;
		}
		if(!array_key_exists("UserId",$_SESSION)){
			return 0;
		}
		return $_SESSION['UserId'];
	}
	
	$libFile = GetLibFile($model,$action);
	if(file_exists($libFile))
		include $libFile;