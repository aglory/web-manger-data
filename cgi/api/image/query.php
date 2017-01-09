<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;	
	$PageItems = array();
	
	$PageColumns = array(
		'tbImageInfo' => array('Id','Title','Src','Level')
	);
	
	$tbFrom = 'tbImageInfo inner join tbCategoryImageRelationInfo on tbImageInfo.Id = tbCategoryImageRelationInfo.ImageId';
		
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
	
	if(array_key_exists('CategoryId',$_POST) && is_numeric($_POST['CategoryId'])){
		$whereSql[] = 'tbCategoryImageRelationInfo.CategoryId = '.intval($_POST['CategoryId']);
	}else{
		echo json_encode(array('code' => 400,'status' => false,'message' => '缺少编号'));
		exit();
	}
	
	if(array_key_exists('Level',$_POST) && is_numeric($_POST['Level'])){
		$whereSql[] = 'tbImageInfo.Level <='.intval($_POST['Level']);
	}
	
	$sthList = null;
	$sthCount = null;
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql).(!empty($PageOrderBy)?' order by '.implode(',',$PageOrderBy):'')." limit $PageStart,$PageEnd;");
	
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
	

	if(empty($errors)){
		$result['code'] = 200;
		$result['status'] = true;
		$result['recordList'] = $sthList -> fetchAll(PDO::FETCH_ASSOC);
	}else{
		$result['code'] = 550;
		$result['status'] = false;
		$result['message'] = implode('\r\n',$errors);
	}
	
	header('Content-Type: application/json;');
	echo json_encode($result);
	exit();