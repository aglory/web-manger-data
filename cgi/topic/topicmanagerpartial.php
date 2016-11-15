<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
	
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	

	$PageColumns = array(
		'tbTopicInfo' => array('Id','Code','Title')
	);
	
	$PageTables = 'tbTopicInfo';
	
	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	
	if(array_key_exists('PageItems',$_POST) && !empty($_POST['PageItems'])){
		foreach(explode(',',$_POST['PageItems']) as $item){
			foreach($PageColumns as $table => $columns){
				if(in_array($item,$columns)){
					$PageItems[] = $table.'.'.$item;
					break;
				}
			}
		}
	}else{
		foreach($PageColumns as $table => $columns){
			foreach($columns as $column){
				$PageItems[] = $table.'.'.$column;
			}
		}
	}
	if(empty($PageItems))
		$PageItems = array('1');

	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = array();
	
	if(array_key_exists('PageSort',$_POST) && !empty($_POST['PageSort'])){
		foreach(explode(',',$_POST['PageSort']) as $item){
			$itemgroup = explode(' ',$item);
			if(count($itemgroup)>1){
				$column_orderby = $itemgroup[0];
				$column_orderbytype = $itemgroup[1];
			}else if(count($itemgroup)>0){
				$column_orderby = $itemgroup[0];
				$column_orderbytype = 'asc';
			}else{
				continue;
			}
			foreach($PageColumns as $table => $columns){
				if(in_array($column_orderby,$columns)){
					$PageOrderBy[] = $table.'.'.$column_orderby.' '.$column_orderbytype;
				}
			}
		}
	}

	$whereSql = array('1=1');
	$whereParams = array();
	
	if(array_key_exists('Code',$_POST) && !empty($_POST['Code'])){
		$whereSql[] = 'tbTopicInfo.Code like :Code';
		$whereParams['Code'] = '%'.$_POST['Code'].'%';
	}
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$whereSql[] = 'tbTopicInfo.Title like :Title';
		$whereParams['Title'] = '%'.$_POST['Title'].'%';
	}
	
	$sthList = null;
	$sthCount = null;
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$PageTables.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(',',$PageOrderBy):'')." limit $PageStart,$PageEnd;");
	$sthCount = $pdomysql -> prepare('select count(1) from '.$PageTables.' where '.implode(' and ',$whereSql));

	if(empty($whereParams)){
		$sthList -> execute();
		$sthCount -> execute();
	}else{
		$sthList -> execute($whereParams);
		$sthCount -> execute($whereParams);
	}

	$errors = array();
	$error = $sthList -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}
	$error = $sthCount -> errorInfo();
	if($error[1] > 0){
		$errors[] = $error[2];
	}

	$result = array();

	if(empty($errors)){
		
		$ls = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		if(!empty($ls)){
			$Ids = array();
			foreach($ls as $item){
				$Ids[] = $item['Id'];
			}
			$sthItemList = $pdomysql -> prepare('select * from tbTopicItemInfo where Topic_Id in('.implode(',',$Ids).') order by Topic_Id,OrderNumber desc,Id desc;');
			$sthItemList -> execute();
			$items = $sthItemList -> fetchAll(PDO::FETCH_ASSOC);
			foreach($ls as &$item){
				$item['Items'] = array();
				foreach($items as $child){
					if($child['Topic_Id'] == $item['Id']){
						$item['Items'][] = $child;
					}
				}
			}
		}
		
		$result['status'] = true;
		$result['recordList'] = $ls;
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['status'] = false;
		$result['recordCount'] = 0; 
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();