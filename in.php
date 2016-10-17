<?php
	ob_start();
	
	function ActionLink($action='',$model='',$opts=null,$echo=true){
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
	
	function Loader(){
		$params = func_get_args();
		if(empty($params))return;
		array_unshift($params,'.','lib');
		
		$file = implode(DIRECTORY_SEPARATOR,$params).'.php';
		if(file_exists($file)){
			require $file;
		}
	}
	
	function GetUserId(){
		if(empty($_SESSION)){
			return 0;
		}
		if(!array_key_exists("UserId",$_SESSION)){
			return 0;
		}
		return $_SESSION['UserId'];
	}
	
	$model="index";
	$action="index";
	if(array_key_exists('model',$_GET)){
		$model=$_GET['model'];
	}
	if(array_key_exists('action',$_GET)){
		$action=$_GET['action'];
	}
	
	define('Model',$model);
	define('Action',$action);
	define('Execute',true);
	
	Render($model,$action);
?>