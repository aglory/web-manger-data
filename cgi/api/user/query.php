<?php
	if(!defined('Execute') && !defined('Api')){ exit();}
	require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';

	$PageSort = '';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	
	$PageColumns = array(
		'tbUserInfo' => array('Id','Name','NickName','Sex','Img','BodyHeight','BodyWeight','EducationalHistory','Constellation','CivilState','Career','Description','ContactWay','ContactQQ','ContactEmail','ContactMobile','InterestAndFavorites','DateTimeModify','Birthday'),
		'tbAccountInfo' => array('DateTimeCreate'),
		'tbUserStatisticsInfo' => array('CountFollow,CountFollowed,CountView,CountScore,CountPoint,CountMessage'),
		'tbUserConfiguration' => array('ConfigurationProtected','ConfigurationVewCost')
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
			if(count($item)>1){
				$column_orderby = $itemgroup[0];
				$column_orderbytype = $itemgroup[1];
			}else if(count($item)>0){
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

	if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
		$whereSql[] = 'tbUserInfo.Name like :Name';
		$whereParams['Name'] = '%'.$_POST['Name'].'%';
	}
	if(array_key_exists('NickName',$_POST) && !empty($_POST['NickName'])){
		$whereSql[] = 'tbUserInfo.NickName like :NickName';
		$whereParams['NickName'] = '%'.$_POST['NickName'].'%';
	}
	if(array_key_exists('Sex',$_POST) && is_numeric($_POST['Sex'])){
		$whereSql[] = 'Sex = '.$_POST['Sex'];
	}
	if(array_key_exists('BirthdayMin',$_POST) && !empty($_POST['BirthdayMin'])){
		$whereSql[] = 'tbUserInfo.Birthday >= :BirthdayMin';
		$whereParams['BirthdayMin'] = $_POST['BirthdayMin'];
	}
	if(array_key_exists('BirthdayMax',$_POST) && !empty($_POST['BirthdayMax'])){
		$whereSql[] = 'tbUserInfo.Birthday <= date_add(:BirthdayMax,INTERVAL 1 DAY)';
		$whereParams['BirthdayMax'] = $_POST['BirthdayMax'];
	}	
	if(array_key_exists('BodyHeightMin',$_POST) && is_numeric($_POST['BodyHeightMin'])){
		$whereSql[] = 'BodyHeight >= '.$_POST['BodyHeightMin'];
	}
	if(array_key_exists('BodyHeightMax',$_POST) && is_numeric($_POST['BodyHeightMax'])){
		$whereSql[] = 'BodyHeight <= '.$_POST['BodyHeightMax'];
	}
	if(array_key_exists('BodyWeightMin',$_POST) && is_numeric($_POST['BodyWeightMin'])){
		$whereSql[] = 'BodyWeight >= '.$_POST['BodyWeightMin'];
	}
	if(array_key_exists('BodyWeightMax',$_POST) && is_numeric($_POST['BodyWeightMax'])){
		$whereSql[] = 'BodyWeight <= '.$_POST['BodyWeightMax'];
	}
	if(array_key_exists('EducationalHistory',$_POST) && is_numeric($_POST['EducationalHistory'])){
		$whereSql[] = 'BodyWeight = '.$_POST['EducationalHistory'];
	}
	
	//'BodyHeight','BodyWeight','EducationalHistory','Constellation','CivilState','Career'
	
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserInfo inner join tbAccountInfo on tbUserInfo.Id = tbAccountInfo.Id inner join tbUserStatisticsInfo on tbUserInfo.Id = tbUserStatisticsInfo.Id inner join tbUserConfiguration on tbUserInfo.Id = tbUserConfiguration.Id';
	
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