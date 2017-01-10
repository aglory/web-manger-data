<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;	
	$PageItems = array();
	
	$PageColumns = array(
		'tbCategoryImageInfo' => array('Id','Title','Tag','Src','Level','Status')
	);
	
	$tbFrom = 'tbCategoryImageInfo';
		
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
	
	
	$whereSql = array('tbCategoryImageInfo.Status = 1');
	$whereParams = array();
	
	if(array_key_exists('Tag',$_POST) && !empty($_POST['Tag'])){
		$whereSql[] = 'find_in_set(:Tag,tbCategoryImageInfo.Tag)';
		$whereParams['Tag'] = $_POST['Tag'];
	}
	
	if(array_key_exists('Tags',$_POST) && !empty($_POST['Tags'])){
		$items = array();
		foreach(explode(',',$_POST['Tags']) as $key => $val){
			$items[] = 'find_in_set(:Tag'.$key.',tbCategoryImageInfo.Tag)';
			$whereParams['Tag'.$key] = $val;
		}
		$whereSql[] = '('.implode(' or ',$items).')';
	}
	
	if(array_key_exists('Title',$_POST) && !empty($_POST['Title'])){
		$whereSql[] = 'tbCategoryImageInfo.Title like :Title';
		$whereParams['Title'] = '%'.$_POST['Title'].'%';
	}
	
	if(array_key_exists('Level',$_POST) && is_numeric($_POST['Level'])){
		$whereSql[] = 'tbCategoryImageInfo.Level <= '.intval($_POST['Level']);
	}
	
	$sthList = null;
	$sthCount = null;
	
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
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();