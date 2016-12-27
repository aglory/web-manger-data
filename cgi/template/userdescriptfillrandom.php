<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('account','login');
		exit();
	}
	require_once Lib('pdo');
	
	$sthUserList = $pdomysql -> prepare("select Id,Birthday from tbUserInfo where Description is null or Description = '';");
	$sthUserList -> execute();
	
	$sthTemplate = $pdomysql -> prepare('select Id from tbTemplateInfo where TemplateGroupId = :TemplateGroupId order by Target * rand() limit 0,1;');
	$sthUser = $pdomysql -> prepare('update tbUserInfo inner join tbTemplateInfo set Description = Data,Target = Target+1  where tbUserInfo.Id = :UserId and tbTemplateInfo.Id = :TemplateId');
	foreach($sthUserList -> fetchAll(PDO::FETCH_ASSOC) as $row){
		echo json_encode($row);
		if($row['Birthday']>'1988-01-01'){
			$sthTemplate -> execute(array('TemplateGroupId' => 2));
			$template = $sthTemplate -> fetch(PDO::FETCH_ASSOC);
			$sthUser -> execute(array('UserId' => $row['Id'],'TemplateId' => $template['Id']));
		}else{
			$sthTemplate -> execute(array('TemplateGroupId' => 2));
			$template = $sthTemplate -> fetch(PDO::FETCH_ASSOC);
			$sthUser -> execute(array('UserId' => $row['Id'],'TemplateId' => $template['Id']));
		}
	}
	exit();