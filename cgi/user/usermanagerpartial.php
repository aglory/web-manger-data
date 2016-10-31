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
	$PageItems = array('Id','Name','NickName','Sex','Img','BodyHeight','BodyWeight','EducationalHistory','Constellation','CivilState','Career','Description','ContactWay','ContactQQ','ContactEmail','ContactMobile','InterestAndFavorites','DateTimeModify');
	$PageItems_Account = array('RoleId','Status');
	$PageItems_Statistics = array('CountFollow,CountFollowed,CountView,CountScore,CountPoint,CountMessage');
	$PageItems_Configuration = array('ConfigurationProtected','ConfigurationVewCost');
	
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
		$items = array();
		foreach(explode(',',$_POST['PageItems']) as $item){
			if(in_array($item,$PageItems))
				$items[] = 'tbUserInfo.'.$item;
			else if(in_array($item,$PageItems_Account)){
				$items[] = 'tbAccountInfo.'.$item;
			}else if(in_array($item,$PageItems_Statistics)){
				$items[] = 'tbUserStatisticsInfo.'.$item;
			}else if(in_array($item,$PageItems_Configuration)){
				$items[] = 'tbUserConfiguration.'.$item;
			}
		}
		if(!empty($items))
			$PageItems = $items;
		else
			$PageItems = array('1');
	}else{
		$items = array();
		foreach($PageItems as $item){
			$items[] = 'tbUserInfo.'.$item;
		}
		foreach($PageItems_Account as $item){
			$items[] = 'tbAccountInfo.'.$item;
		}
		foreach($PageItems_Statistics as $item){
			$items[] = 'tbUserStatisticsInfo.'.$item;
		}
		foreach($PageItems_Configuration as $item){
			$items[] = 'tbUserConfiguration.'.$item;
		}
		$PageItems = $items;
	}

	$PageStart = ($PageIndex - 1) * $PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = empty($PageSort)?'':" order by $PageSort ";

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
	if(array_key_exists('DateTimeModifyStart',$_POST) && !empty($_POST['DateTimeModifyStart'])){
		$whereSql[] = 'tbUserInfo.DateTimeModify >= :DateTimeModifyStart';
		$whereParams['DateTimeModifyStart'] = $_POST['DateTimeModifyStart'];
	}
	if(array_key_exists('DateTimeModifyEnd',$_POST) && !empty($_POST['DateTimeModifyEnd'])){
		$whereSql[] = 'tbUserInfo.DateTimeModify <= date_add(:DateTimeModifyEnd,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyEnd'] = $_POST['DateTimeModifyEnd'];
	}
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbAccountInfo.Status = '.$_POST['Status'];
	}
	
	
	$sthList = null;
	$sthCount = null;
	
	$tbFrom = 'tbUserInfo inner join tbAccountInfo on tbUserInfo.Id = tbAccountInfo.Id inner join tbUserStatisticsInfo on tbUserInfo.Id = tbUserStatisticsInfo.Id inner join tbUserConfiguration on tbUserInfo.Id = tbUserConfiguration.Id';
	
	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from '.$tbFrom.' where '.implode(' and ',$whereSql)."$PageOrderBy limit $PageStart,$PageEnd;");
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