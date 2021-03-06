<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbUserImageInfo' => array('Id','User_Id','OrderNumber','Src','IsDefault','Status','Description','DateTimeCreate','DateTimeModify')
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

	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$whereSql[] = 'tbUserImageInfo.User_Id ='.$_POST['User_Id'];
	}
	if(array_key_exists('IsDefault',$_POST) && is_numeric($_POST['IsDefault'])){
		$whereSql[] = 'tbUserImageInfo.IsDefault = '.$_POST['IsDefault'];
	}
	
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbUserImageInfo.Status = '.$_POST['Status'];
	}
	if(array_key_exists('DateTimeModifyMin',$_POST) && !empty($_POST['DateTimeModifyMin'])){
		$whereSql[] = 'tbUserImageInfo.DateTimeModify >= :DateTimeModifyMin';
		$whereParams['DateTimeModifyMin'] = $_POST['DateTimeModifyMin'];
	}
	if(array_key_exists('DateTimeModifyMax',$_POST) && !empty($_POST['DateTimeModifyMax'])){
		$whereSql[] = 'tbUserImageInfo.DateTimeModify < date_add(:DateTimeModifyMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyMax'] = $_POST['DateTimeModifyMax'];
	}
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserImageInfo';

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
		$result['status'] = true;
		$result['code'] = 200;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
		$result['recordCount'] = $sthCount -> fetch(PDO::FETCH_NUM)[0]; 
	}else{
		$result['code'] = 550;
		$result['status'] = false;
		$result['recordCount'] = 0; 
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();