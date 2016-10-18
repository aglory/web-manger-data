<?php
require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
$whereSql = array();
$whereParams = array();
if(array_key_exists('Name',$_POST) && !empty($_POST['Name'])){
	$whereSql[] = 'Name like :Name';
	$whereParams['Name'] = '%'.$_POST['Name'].'%';
}
if(array_key_exists('NickName',$_POST) && !empty($_POST['NickName'])){
	$whereSql[] = 'NickName like :NickName';
	$whereParams['NickName'] = '%'.$_POST['NickName'].'%';
}

header('Content-Type: application/json;');

$sthList = null;
$sthCount = null;

if(empty($whereSql)){
	$sthList = $pdomysql -> prepare('select * from tbUserInfo;');
	$sthList -> execute();
	$sthCount = $pdomysql -> prepare('select count(1) from tbUserInfo;');
	$sthCount -> execute();
}else{
	$sthList = $pdomysql -> prepare('select * from tbUserInfo where '.implode(' and ',$whereSql).';');
	$sthList -> execute($whereParams);
	$sthCount = $pdomysql -> prepare('select count(1) from tbUserInfo where '.implode(' and ',$whereSql).';');
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
echo json_encode($result);
exit();
?>