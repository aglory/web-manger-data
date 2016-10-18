<?php
require_once implode(DIRECTORY_SEPARATOR,array('.','lib','pdo')).'.php';
$whereSql = array();
$whereParams = array();
if(array_key_exists('Name',$_POST)){
	$whereSql[] = 'Name = :Name';
	$whereParams['Name'] = '%'.$_POST['Name'].'%';
}
if(array_key_exists('NickName',$_POST)){
	$whereSql[] = 'NickName = :NickName';
	$whereParams['NickName'] = '%'.$_POST['NickName'].'%';
}

header('Content-Type: application/json;');
$result = array();

$sthList = null;
$sthCount = null;

if(empty($whereSql)){
	$sthList = $pdomysql -> prepare('select * from tbUserInfo;');
	$sthList -> execute();
	$sthCount = $pdomysql -> prepare('select count(1) from tbUserInfo;');
	$sthCount -> execute();
}else{
	$sthList = $pdomysql -> prepare('select * from tbUserInfo where'.implode(' and ',$whereSql).';');
	$sthList -> execute($whereParams);
	$sthCount = $pdomysql -> prepare('select count(1) from tbUserInfo where'.implode(' and ',$whereSql).';');
	$sthCount -> execute();
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
	$result['recordList'] = $sthList -> fetchAll();
	$result['recordCount'] = $sthCount -> fetch()[0]; 
}else{
	$result['status'] = false;
	$result['recordCount'] = 0; 
	$result['message'] = implode('\r\n',$errors);
}
echo json_encode($result);
exit();
?>