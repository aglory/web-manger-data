<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbUserScoreLog' => array('Id','User_Id','Type','Number','Mark','DateTimeCreate')
	);
	
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
	
	header('Content-Type: application/json;');

	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$whereSql[] = 'tbUserScoreLog.User_Id ='.$_POST['User_Id'];
	}else{
		echo json_encode(array('code' => 400,'status' => true,'recordList' => null,'recordCount' => 0));
		exit(1);
	}
	
	if(array_key_exists('Type',$_POST) && is_numeric($_POST['Type'])){
		$whereSql[] = 'tbUserScoreLog.Type = '.$_POST['Type'];
	}
	
	if(array_key_exists('NumberMin',$_POST) && is_numeric($_POST['NumberMin'])){
		$whereSql[] = 'tbUserScoreLog.Number >= '.$_POST['NumberMin'];
	}
	if(array_key_exists('NumberMax',$_POST) && is_numeric($_POST['NumberMax'])){
		$whereSql[] = 'tbUserScoreLog.Number <= '.$_POST['NumberMax'];
	}
	if(array_key_exists('DateTimeCreateMin',$_POST) && !empty($_POST['DateTimeCreateMin'])){
		$whereSql[] = 'tbUserScoreLog.DateTimeCreate >= :DateTimeCreateMin';
		$whereParams['DateTimeCreateMin'] = $_POST['DateTimeCreateMin'];
	}
	if(array_key_exists('DateTimeCreateMax',$_POST) && !empty($_POST['DateTimeCreateMax'])){
		$whereSql[] = 'tbUserScoreLog.DateTimeCreate < date_add(:DateTimeCreateMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeCreateMax'] = $_POST['DateTimeCreateMax'];
	}
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserScoreLog';
	
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(',',$PageOrderBy):'')." limit $PageStart,$PageEnd;");
	$sthCount = $pdomysql -> prepare('select count(1) from '.$tbFrom.' where '.implode(' and ',$whereSql));

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
		$result['code'] = 200;
		$result['status'] = true;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['code'] = 550;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	echo json_encode($result);
	exit();