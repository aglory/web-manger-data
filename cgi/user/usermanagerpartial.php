<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array();
	

	$PageColumns = array(
		'tbUserInfo' => array('Id','Name','NickName','Sex','Img','BodyHeight','BodyWeight','EducationalHistory','Constellation','CivilState','Career','Description','ContactWay','ContactQQ','ContactEmail','ContactMobile','InterestAndFavorites','DateTimeModify','Birthday'),
		'tbAccountInfo' => array('DateTimeCreate','RoleId','Status')
	);
	
	$PageTables = 'tbUserInfo inner join tbAccountInfo on tbUserInfo.Id = tbAccountInfo.Id inner join tbUserStatisticsInfo on tbUserInfo.Id = tbUserStatisticsInfo.Id inner join tbUserConfiguration on tbUserInfo.Id = tbUserConfiguration.Id';
	
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

	if(array_key_exists('Ids',$_POST) && is_array($_POST['Ids'])){
		$ids = array();
		foreach($_POST['Ids'] as  $item){
			$ids[] = intval($item);
		}
		if(!empty($ids)){
			$whereSql[] = 'tbUserInfo.Id in('.implode(',',$ids).')';
		}
	}
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
	if(array_key_exists('DateTimeModifyStart',$_POST) && !empty($_POST['DateTimeModifyStart'])){
		$whereSql[] = 'tbUserInfo.DateTimeModify >= :DateTimeModifyStart';
		$whereParams['DateTimeModifyStart'] = $_POST['DateTimeModifyStart'];
	}
	if(array_key_exists('DateTimeModifyEnd',$_POST) && !empty($_POST['DateTimeModifyEnd'])){
		$whereSql[] = 'tbUserInfo.DateTimeModify <= date_add(:DateTimeModifyEnd,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyEnd'] = $_POST['DateTimeModifyEnd'];
	}
	if(array_key_exists('BirthdayStart',$_POST) && !empty($_POST['BirthdayStart'])){
		$whereSql[] = 'tbUserInfo.Birthday >= :BirthdayStart';
		$whereParams['BirthdayStart'] = $_POST['BirthdayStart'];
	}
	if(array_key_exists('BirthdayEnd',$_POST) && !empty($_POST['BirthdayEnd'])){
		$whereSql[] = 'tbUserInfo.Birthday <= date_add(:BirthdayEnd,INTERVAL 1 DAY)';
		$whereParams['BirthdayEnd'] = $_POST['BirthdayEnd'];
	}
	
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbAccountInfo.Status = '.$_POST['Status'];
	}
	
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