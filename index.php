<?php
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
	if(!($model =='account' && $action == 'login') && empty(CurrentUserId())){
		$model = 'account';
		$action = 'login';
	}
	
	define('Model',$model);
	define('Action',$action);
	define('Execute',true);
	
	date_default_timezone_set('PRC');
	
	function ActionLink($model='',$action='',$opts=null,$echo=true){
		$result = array();
		$result[] = 'model='.urlencode($model);
		$result[] = 'action='.urlencode($action);
		if(!empty($opts)){
			foreach($opts as $k => $v){
				$result[] = $k.'='.urlencode($v);
			}
		}
		if($echo)
			echo '?'.implode('&',$result);
		return '?'.implode('&',$result);
	}
	
	function Render(){
		$params = func_get_args();
		if(empty($params))return;
		array_unshift($params,'.','cgi');
		
		$file = implode(DIRECTORY_SEPARATOR,$params).'.php';
		if(file_exists($file)){
			include $file;
		}
	}
	
	function Lib(){
		$params = func_get_args();
		if(empty($params))return;
		array_unshift($params,'.','lib');
		
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
	
	
	
	Render($model,$action);