<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
	require_once Lib('pdo');

	$PageSort = 'tbUserImageInfo.OrderNumber desc,tbUserImageInfo.Id desc';
	$PageIndex = 1;
	$PageSize = 20;
	$PageItems = array('Id','User_Id','OrderNumber','Src','IsDefault','Status','Description','DateTimeCreate','DateTimeModify');
	$PageItems_User = array('User_Name','User_NickName');
	
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
				$items[] = 'tbUserImageInfo.'.$item;
			else if(in_array($item,$PageItems_User)){
				$items[] = preg_replace('/^User_(\w+)$/i','tbUserInfo.$1 as User_$1',$item);
			}
		}
		if(!empty($items))
			$PageItems = $items;
		else
			$PageItems = array('1');
	}else{
		$items = array();
		foreach($PageItems as $item){
			$items[] = 'tbUserImageInfo.'.$item;
		}
		foreach($PageItems_User as $item){
			$items[] = preg_replace('/^User_(\w+)$/i','tbUserInfo.$1 as User_$1',$item);
		}
		$PageItems = $items;
	}

	$PageStart = ($PageIndex - 1)*$PageSize;
	$PageEnd = $PageSize;
	$PageOrderBy = empty($PageSort)?'':" order by $PageSort ";
	
	$whereSql = array('1=1');
	$whereParams = array();

	
	if(array_key_exists('User_Name',$_POST) && !empty($_POST['User_Name'])){
		$whereSql[] = 'tbUserInfo.Name like :User_Name';
		$whereParams['User_Name'] = '%'.$_POST['User_Name'].'%';
	}
	if(array_key_exists('User_NickName',$_POST) && !empty($_POST['User_NickName'])){
		$whereSql[] = 'tbUserInfo.NickName like :User_NickName';
		$whereParams['User_NickName'] = '%'.$_POST['User_NickName'].'%';
	}
	if(array_key_exists('IsDefault',$_POST) && is_numeric($_POST['IsDefault'])){
		$whereSql[] = 'tbUserImageInfo.IsDefault = '.$_POST['IsDefault'];
	}
	if(array_key_exists('DateTimeModifyStart',$_POST) && !empty($_POST['DateTimeModifyStart'])){
		$whereSql[] = 'tbUserImageInfo.DateTimeModify >= :DateTimeModifyStart';
		$whereParams['DateTimeModifyStart'] = $_POST['DateTimeModifyStart'];
	}
	if(array_key_exists('DateTimeModifyEnd',$_POST) && !empty($_POST['DateTimeModifyEnd'])){
		$whereSql[] = 'tbUserImageInfo.DateTimeModify <= date_add(:DateTimeModifyEnd,INTERVAL 1 DAY)';
		$whereParams['DateTimeModifyEnd'] = $_POST['DateTimeModifyEnd'];
	}
	if(array_key_exists('Status',$_POST) && is_numeric($_POST['Status'])){
		$whereSql[] = 'tbUserImageInfo.Status = '.$_POST['Status'];
	}
	if(array_key_exists('User_Id',$_POST) && is_numeric($_POST['User_Id'])){
		$whereSql[] = 'tbUserImageInfo.User_Id = '.$_POST['User_Id'];
	}
	if(array_key_exists('$User_Id',$_POST) && is_numeric($_POST['$User_Id'])){
		if($_POST['$User_Id'] == 1){
			$whereSql[] = 'tbUserImageInfo.User_Id != 0';
		}else if($_POST['$User_Id'] == -1){
			$whereSql[] = 'tbUserImageInfo.User_Id = 0';
		}
	}
	
	
	$sthList = null;
	$sthCount = null;

	$sthList = $pdomysql -> prepare('select '.implode(',',$PageItems).' from tbUserImageInfo left join tbUserInfo on tbUserImageInfo.User_Id = tbUserInfo.Id where '.implode(' and ',$whereSql)."$PageOrderBy limit $PageStart,$PageEnd;");
	$sthCount = $pdomysql -> prepare('select Count(1) from tbUserImageInfo left join tbUserInfo on tbUserImageInfo.User_Id = tbUserInfo.Id where '.implode(' and ',$whereSql));
	if(empty($whereParams)){
		$sthList -> execute();
		$sthCount -> execute();
	}else{
		$sthList -> execute($whereParams);
		$sthCount -> execute($whereParams);
	}

	$errors = array();
	
	$error = $sthList -> errorInfo();
	if($error[0] > 0){
		$errors[] = $error[2];
	}
	$error = $sthCount -> errorInfo();
	if($error[0] > 0){
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