<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');

	$PageSort = 'Id desc';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbCategoryImageInfo' => array('Id','Title','Tag','Img','ExtenseId','Scrawled','Src','Level','DateTimeCreate','DateTimeModify','Status')
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

	if(array_key_exists('Id',$_POST) && is_numeric($_POST['Id'])){
		$whereSql[] = 'tbCategoryImageInfo.Id = '.intval($_POST['Id']);
	}
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$whereSql[] = 'tbCategoryImageInfo.Title like :Title';
		$whereParams['Title'] = '%'.$_POST['Title'].'%';
	}
	if(array_key_exists('Tag',$_POST) && !empty($_POST['Tag'])){
		$whereSql[] = 'find_in_set(:Tag,tbCategoryImageInfo.Tag)';
		$whereParams['Tag'] = $_POST['Tag'];
	}
	if(array_key_exists('Level',$_POST) && is_numeric($_POST['Level'])){
		$whereSql[] = 'tbCategoryImageInfo.Level = '.intval($_POST['Level']);
	}
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbCategoryImageInfo.Status = '.intval($_POST['Status']);
	}
	if(array_key_exists('Scrawled',$_POST) && is_numeric($_POST['Scrawled'])){
		$whereSql[] = 'tbCategoryImageInfo.Scrawled = '.intval($_POST['Scrawled']);
	}
	if(array_key_exists('DateTimeCreateMin',$_POST) && !empty($_POST['DateTimeCreateMin'])){
		$whereSql[] = 'tbCategoryImageInfo.DateTimeCreate >= :DateTimeCreateMin';
		$whereParams['DateTimeCreateMin'] = $_POST['DateTimeCreateMin'];
	}
	if(array_key_exists('DateTimeCreateMax',$_POST) && !empty($_POST['DateTimeCreateMax'])){
		$whereSql[] = 'tbCategoryImageInfo.DateTimeCreate <= date_add(:DateTimeCreateMax,INTERVAL 1 DAY)';
		$whereParams['DateTimeCreateMax'] = $_POST['DateTimeCreateMax'];
	}
	if(array_key_exists('ExceptRelationImageId',$_POST) && is_numeric($_POST['ExceptRelationImageId'])){
		$whereSql[] = 'tbCategoryImageInfo.Id not in(select CategoryId from tbCategoryImageRelationInfo where ImageId = '.intval($_POST['ExceptRelationImageId']).')';
	}
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbCategoryImageInfo';
	
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