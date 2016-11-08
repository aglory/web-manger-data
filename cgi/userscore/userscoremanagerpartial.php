<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = 'Id desc';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbUserInfo' => array('Name','NickName'),
		'tbUserScoreLog' => array('Id','User_Id','Type','Number','Mark','DateTimeCreate')
	);

	
	if(array_key_exists('PageIndex',$_POST) && is_numeric($_POST['PageIndex'])){
		$PageIndex = intval($_POST['PageIndex']);
	}
	if(array_key_exists('PageSize',$_POST) && is_numeric($_POST['PageSize'])){
		$PageSize = intval($_POST['PageSize']);
	}
	if(array_key_exists('PageSort',$_POST) && !empty($_POST['PageSort'])){
		$PageSort = $_POST['PageSort'];
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

	if(array_key_exists('User_Name',$_POST) && !empty($_POST['User_Name'])){
		$whereSql[] = 'tbUserInfo.Name like :User_Name';
		$whereParams['User_Name'] = '%'.$_POST['User_Name'].'%';
	}
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id']) && $_POST['User_Id'] > 0){
		$whereSql[] = 'tbUserScoreLog.User_Id = '.intval($_POST['User_Id']);
	}
	if(array_key_exists('Type',$_POST) && is_numeric($_POST['Type'])){
		$whereSql[] = 'tbUserScoreLog.Type = '.intval($_POST['Type']);
	}
	if(array_key_exists('NumberMin',$_POST) && is_numeric($_POST['NumberMin'])){
		$whereSql[] = 'tbUserScoreLog.Number >= '.intval($_POST['NumberMin']);
	}
	if(array_key_exists('NumberMax',$_POST) && is_numeric($_POST['NumberMax'])){
		$whereSql[] = 'tbUserScoreLog.Number <= '.intval($_POST['NumberMax']);
	}
	if(array_key_exists('DateTimeCreateMin',$_POST) && !empty($_POST['DateTimeCreateMin'])){
		$whereSql[] = 'tbUserScoreLog.DateTimeCreate >= :DateTimeCreateMin';
		$whereParams['DateTimeCreateMin'] = $_POST['DateTimeCreateMin'];
	}
	if(array_key_exists('DateTimeCreateMax',$_POST) && !empty($_POST['DateTimeCreateMax'])){
		$whereSql[] = 'tbUserScoreLog.DateTimeCreate <= date_add(:DateTimeCreateMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeCreateMax'] = $_POST['DateTimeCreateMax'];
	}
	
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserScoreLog left join tbUserInfo on tbUserScoreLog.User_Id = tbUserInfo.Id';
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(' ',$PageOrderBy):'')." limit $PageStart,$PageEnd;");
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
		$result['status'] = true;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['status'] = false;
		$result['recordCount'] = 0; 
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();