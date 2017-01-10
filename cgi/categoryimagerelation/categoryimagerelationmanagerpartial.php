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
		'tbCategoryImageRelationInfo' => array('CategoryId','ImageId'),
		'tbCategoryImageInfo' => array('Title','Tag','Img','Src','Level','Status'),
		'tbImageInfo' => array('Title','Img','Src','Level','Status')
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
				if($table == 'tbCategoryImageInfo')
					$alias = ' as Category_'.$column;
				else if($table == 'tbImageInfo')
					$alias = ' as Image_'.$column;
				else
					$alias = '';
				if(in_array($item,$columns)){
					$PageItems[] = $table.'.'.$item;
					break;
				}
			}
		}
	}else{
		foreach($PageColumns as $table => $columns){
			foreach($columns as $column){
				if($table == 'tbCategoryImageInfo')
					$alias = ' as Category_'.$column;
				else if($table == 'tbImageInfo')
					$alias = ' as Image_'.$column;
				else
					$alias = '';
				$PageItems[] = $table.'.'.$column.$alias;
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
				if($table == 'tbCategoryImageInfo')
					$column = str_replace('Category_','',$column_orderby);
				else if($table == 'tbImageInfo')
					$column = str_replace('Image_','',$column_orderby);
				else
					$column = $column_orderby;
				if(in_array($column,$columns)){
					$PageOrderBy[] = $table.'.'.$column.' '.$column_orderbytype;
				}
			}
		}
	}
	
	$whereSql = array('1=1');
	$whereParams = array();

	if(array_key_exists('CategoryId',$_POST) && is_numeric($_POST['CategoryId'])){
		$whereSql[] = 'tbCategoryImageRelationInfo.CategoryId = '.intval($_POST['CategoryId']);
	}
	if(array_key_exists('ImageId',$_POST) && is_numeric($_POST['ImageId'])){
		$whereSql[] = 'tbCategoryImageRelationInfo.ImageId = '.intval($_POST['ImageId']);
	}
	
	
	if(array_key_exists('Category_Title',$_POST) && !empty($_POST['Category_Title'])){
		$whereSql[] = 'tbCategoryImageInfo.Title like :Category_Title';
		$whereParams['Category_Title'] = '%'.$_POST['Category_Title'].'%';
	}
	if(array_key_exists('Category_Level',$_POST) && is_numeric($_POST['Category_Level'])){
		$whereSql[] = 'tbCategoryImageInfo.Level = '.intval($_POST['Category_Level']);
	}
	if(array_key_exists('Category_Status',$_POST) && is_numeric($_POST['Category_Status'])){
		$whereSql[] = 'tbCategoryImageInfo.Status = '.intval($_POST['Category_Status']);
	}

	if(array_key_exists('Image_Title',$_POST) && !empty($_POST['Image_Title'])){
		$whereSql[] = 'tbImageInfo.Title like :Image_Title';
		$whereParams['Image_Title'] = '%'.$_POST['Image_Title'].'%';
	}
	if(array_key_exists('Image_Level',$_POST) && is_numeric($_POST['Image_Level'])){
		$whereSql[] = 'tbImageInfo.Level = '.intval($_POST['Image_Level']);
	}
	if(array_key_exists('Image_Status',$_POST) && is_numeric($_POST['Image_Status'])){
		$whereSql[] = 'tbImageInfo.Status = '.intval($_POST['Image_Status']);
	}
	
	
	$sthList = null;
	$sthCount = null;
	

	$tbFrom = 'tbCategoryImageRelationInfo inner join tbCategoryImageInfo on tbCategoryImageRelationInfo.CategoryId = tbCategoryImageInfo.Id inner join tbImageInfo on tbCategoryImageRelationInfo.ImageId = tbImageInfo.id';

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

	$result = array('sql'=>'select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(',',$PageOrderBy):'')." limit $PageStart,$PageEnd;");

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