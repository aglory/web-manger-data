<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;	
	$PageItems = array();
	
	$tbFrom = 'tbCategoryImageInfo';
		
	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	
	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = array();	
	
	$havingSql = array();
	$havingParams = array();
	
	if(array_key_exists('Tag',$_POST) && !empty($_POST['Tag'])){
		$whereSql[] = 'find_in_set(:Tag,tbCategoryImageInfo.Tag)';
		$whereParams['Tag'] = $_POST['Tag'];
	}
	
	$sthList = null;
	$sthCount = null;
	
	$sthList = $pdomysql -> prepare('select Tag from '.$tbFrom.' where Status = 1 group by Tag'.(!empty($havingSql)?' having '.implode(',',$havingSql):''));
	
	if(empty($whereParams)){
		$sthList -> execute();
	}else{
		$sthList -> execute($whereParams);
	}

	$errors = array();
	$error = $sthList -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}

	$result = array();
	
	$ls = array();

	foreach($sthList -> fetchAll(PDO::FETCH_ASSOC) as $items){
		foreach(explode(',',$items['Tag']) as $item){
			if(in_array($item,$ls)){
				continue;
			}
			$ls[] = $item;
		}
	}
	
	if(empty($errors)){
		$result['code'] = 200;
		$result['status'] = true;
		$result['recordList'] = array_slice($ls,$PageStart,$PageSize);
		$result['recordCount'] = count($ls);
	}else{
		$result['code'] = 550;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();