<?php
	if(!defined('Execute')) exit();
	if(empty(CurrentUserId())){
		Render('home','login');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>图片相册关联管理</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="jquery/jquery.min.js"></script>
		
		<script src="jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="jquery/jquery-ui.theme.min.css" />
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
		<script src="bootstrap/js/bootstrap.js"></script>
		
		<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" />
		
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome.min.css" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="Font-Awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		
		<script src="My97DatePicker/WdatePicker.js"></script>
		<script src="My97DatePicker/lang/zh-cn.js"></script>
		
		<script src="common/pager.js"></script>
		
		<link rel="stylesheet" href="common/common.css" />
		<script src="common/common.js"></script>
		
		<script src="common/template.js"></script>

		<script src="common/config.js"></script>
		
		<link href="resource/categoryimagerelation/categoryimagerelationmanager.css"  rel="stylesheet"/>
		<script src="resource/categoryimagerelation/categoryimagerelationmanager.js"></script>
		
		<script>
			$(function(){
				doQuery();
			});
		</script>
	</head>
	<body>
		<?php
			Render('header');
		?>
		<div class="container">
		
			<?php if(array_key_exists('ImageId',$_GET) && is_numeric($_GET['ImageId'])){?>
			<div id="divCategoryId">
				<div class="col-md-12">
					<form id="mainCategoryForm" class="form-inline" action="<?php ActionLink('categoryimage','categoryimagemanagerpartial')?>">
						<input id="CategoryPageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="CategoryPageSize" name="PageSize" type="hidden" value="5" />
						<input id="CategoryPageSort" name="PageSort" type="hidden" value="" />
						<input id="CategoryPageItems" name="PageItems" type="hidden" value="Id,Title,Tag,Img,Src,Level,Status,DateTimeCreate,DateTimeModify" />
						<input name="ExceptRelationImageId" type="hidden" value="<?php echo $_GET['ImageId'];?>" />
						
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-12 clearfix">
										<div class="btn-group f_l">
											<button class="btn btn-sm btn-info" type="button" onclick="categoryImageRelationAdd(this,0,<?php echo $_GET['ImageId']; ?>)">添加</button>
										</div>
										<div class="f_r">
											<div class="form-group">
												<input id="Id" name="Id" type="text" class="form-control input-sm wd120" placeholder="编号" />
											</div>
											<div class="form-group">
												<input id="Title" name="Title" type="text" class="form-control input-sm wd120" placeholder="标题" />
											</div>
											<div class="form-group">
												<input id="Tag" name="Tag" type="text" class="form-control input-sm wd120" placeholder="标签" />
											</div>
											<div class="form-group">
												<select id="Level" name="Level" class="form-control input-sm" placeholder="等级">
													<option value="">全部</option>
													<script type="text/javascript">
													var categoryImageLevel = EnumConfig().CategoryImageLevel;
													for(var i in categoryImageLevel){
														var o = categoryImageLevel[i];
														document.write('<option value="'+o.Key+'">'+o.Value+'</option>');
													}
													</script>
												</select>
											</div>
											<div class="form-group">
												<input id="CategoryDateTimeModifyMin" name="DateTimeModifyMin" class="form-control input-sm date Wdate wd100" placeholder="开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'CategoryDateTimeModifyMax\')}'});" />
												<input id="CategoryDateTimeModifyMax" name="DateTimeModifyMax" class="form-control input-sm date Wdate wd100" placeholder="结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'CategoryDateTimeModifyMin\')}'});" />
											</div>
											<div class="form-group">
												<select id="Status" name="Status" class="form-control input-sm" placeholder="状态">
													<option value="">全部</option>
													<option value="1">启用</option>
													<option value="0">禁用</option>
												</select>
											</div>
											<div class="form-group">
												<button type="submit" class="btn btn-info btn-sm btn-query">查询</button>
												<button type="button" class="btn btn-default btn-sm btn-refresh">刷新</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<thead id="recordCategoryHead">
										<tr>
											<th class="t_c wd40"><input type="checkbox" onchange="changeAllCheckBoxStatus(this);" /></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Title"> 标题</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Tag"> 标签</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Level"> 等级</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Src"> 图片</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Status"> 状态</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="DateTimeModify"> 时间（创建/修改）</a></th>
											<th class="t_c wd200">操作</th>
										</tr>
									</thead>
									<tbody id="recordCategoryList">
									</tbody>
									<tfoot id="recordCategoryStatic">
										<tr>
											<td colspan="8" class="t_r"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
			<?php }?>
			<?php if(array_key_exists('CategoryId',$_GET)){?>
			
			<div id="divImageId">
				<div class="col-md-12">
					<form id="mainImageForm" class="form-inline" action="<?php ActionLink('image','imagemanagerpartial')?>">
						<input id="ImagePageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="ImagePageSize" name="PageSize" type="hidden" value="5" />
						<input id="ImagePageSort" name="PageSort" type="hidden" value="" />
						<input id="ImagePageTemplate" type="hidden" value="imageblock" />
						<input id="ImagePageItems" name="PageItems" type="hidden" value="Id,Title,Img,Src,Level,Status,DateTimeCreate,DateTimeModify" />
						<input name="ExceptRelationCategoryId" type="hidden" value="<?php echo $_GET['CategoryId'];?>" />
						
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-12 clearfix">
										<div class="btn-group f_l">
											<button class="btn btn-sm btn-info" type="button" onclick="categoryCategoryRelationAdd(this,<?php echo $_GET['CategoryId']; ?>,0)">添加</button>
										</div>
										<div class="f_r">
											<div class="form-group">
												<input id="Id" name="Id" type="text" class="form-control input-sm wd120" placeholder="编号" />
											</div>
											<div class="form-group">
												<input id="Title" name="Title" type="text" class="form-control input-sm wd120" placeholder="标题" />
											</div>
											<div class="form-group">
												<select id="Level" name="Level" class="form-control input-sm" placeholder="等级">
													<option value="">全部</option>
													<script type="text/javascript">
													var imageLevel = EnumConfig().ImageLevel;
													for(var i in imageLevel){
														var o = imageLevel[i];
														document.write('<option value="'+o.Key+'">'+o.Value+'</option>');
													}
													</script>
												</select>
											</div>
											<div class="form-group">
												<input id="ImageDateTimeModifyMin" name="DateTimeModifyMin" class="form-control input-sm date Wdate wd100" placeholder="开始日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'ImageDateTimeModifyMax\')}'});" />
												<input id="ImageDateTimeModifyMax" name="DateTimeModifyMax" class="form-control input-sm date Wdate wd100" placeholder="结束日期" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'ImageDateTimeModifyMin\')}'});" />
											</div>
											<div class="form-group">
												<select id="Status" name="Status" class="form-control input-sm" placeholder="状态">
													<option value="">全部</option>
													<option value="1">启用</option>
													<option value="0">禁用</option>
												</select>
											</div>
											<div class="form-group">
												<button type="submit" class="btn btn-info btn-sm btn-query">查询</button>
												<button type="button" class="btn btn-default btn-sm btn-refresh">刷新</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<thead id="recordHead">
										<tr>
											<th class="t_c wd40"><input type="checkbox" onchange="changeAllCheckBoxStatus(this);" /></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Title"> 标题</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Level"> 等级</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Src"> 本地图片</a></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Status"> 状态</a></th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="DateTimeModify"> 时间（创建/修改）</a></th>
											<th class="t_c wd200">操作</th>
										</tr>
									</thead>
									<tbody id="recordImageList">
									</tbody>
									<tfoot id="recordImageStatic">
										<tr>
											<td colspan="8" class="t_r"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<?php }?>
			
			<div id="main">
				<div class="col-md-12">
					<form id="mainForm" class="form-inline" action="<?php ActionLink('categoryimagerelation','categoryimagerelationmanagerpartial')?>">
						<input id="PageIndex" name="PageIndex" type="hidden" value="1" />
						<input id="PageSize" name="PageSize" type="hidden" value="5" />
						<input id="PageSort" name="PageSort" type="hidden" value="" />
						<input id="PageItems" name="PageItems" type="hidden" value="" />
						
						<input id="ImageId" name="ImageId" type="hidden" value="<?php if(array_key_exists('ImageId',$_GET)){echo $_GET['ImageId'];}?>" />


						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="panel-title clearfix">
									<div class="col-sm-12 clearfix">
										<div class="f_l">
											<div class="btn-group">
												<button class="btn btn-sm btn-success" type="button" onclick="changeCategoryStatus(this,0,1)">启用</button>
												<button class="btn btn-sm btn-warning" type="button" onclick="changeCategoryStatus(this,0,0)">禁用</button>
											</div>
											<div class="btn-group">
												<button class="btn btn-sm btn-success" type="button" onclick="changeImageStatus(this,0,1)">启用</button>
												<button class="btn btn-sm btn-warning" type="button" onclick="changeImageStatus(this,0,0)">禁用</button>
											</div>
										</div>
										<div class="f_r">
											<div class="inline-block">
												<div class="form-group">
													<input name="CategoryId" type="text" class="form-control input-sm wd60" placeholder="分类编号" value="<?php if(array_key_exists('CategoryId',$_GET)){echo $_GET['CategoryId'];}?>" />
												</div>
												<div class="form-group">
													<input name="Category_Title" type="text" class="form-control input-sm wd120" placeholder="分类标题" />
												</div>
												<div class="form-group">
													<select name="Category_Level" class="form-control input-sm" placeholder="分类等级">
														<option value="">全部</option>
														<script type="text/javascript">
														var categoryImageLevel = EnumConfig().CategoryImageLevel;
														for(var i in categoryImageLevel){
															var o = categoryImageLevel[i];
															document.write('<option value="'+o.Key+'">'+o.Value+'</option>');
														}
														</script>
													</select>
												</div>
											</div>
											<div class="inline-block">
												<div class="form-group">
													<input name="ImageId" type="text" class="form-control input-sm wd60" placeholder="相片编号" value="<?php if(array_key_exists('ImageId',$_GET)){echo $_GET['ImageId'];}?>" />
												</div>
												<div class="form-group">
													<input name="Image_Title" type="text" class="form-control input-sm wd120" placeholder="相片标题" />
												</div>
												<div class="form-group">
													<select name="Image_Level" class="form-control input-sm" placeholder="相片等级">
														<option value="">全部</option>
														<script type="text/javascript">
														var imageLevel = EnumConfig().ImageLevel;
														for(var i in imageLevel){
															var o = imageLevel[i];
															document.write('<option value="'+o.Key+'">'+o.Value+'</option>');
														}
														</script>
													</select>
												</div>
											</div>
											<div class="form-group">
												<button type="submit" class="btn btn-info btn-sm btn-query">查询</button>
												<button type="button" class="btn btn-default btn-sm btn-refresh">刷新</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<thead id="recordHead">
										<tr>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="CategoryId"> 相册</a><input type="checkbox" onchange="changeCategoryIdSelected(this)" /></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Category_Img"> 图片</a></th>
											<th class="t_c wd200"><a class="btn btn-sort icon-sort " sort-expression="Category_Level"> 等级</a></th>
											<th class="t_c wd120"> 相册操作 </th>
											<th class="t_c"><a class="btn btn-sort icon-sort " sort-expression="Image_Id"> 图片</a><input type="checkbox" onchange="changeImageIdSelected(this)" /></th>
											<th class="t_c wd80"><a class="btn btn-sort icon-sort " sort-expression="Image_Img"> 图片</a></th>
											<th class="t_c wd200"><a class="btn btn-sort icon-sort " sort-expression="Image_Level"> 等级</a></th>
											<th class="t_c wd120"> 图片操作 </th>
											<th class="t_c wd80">操作</th>
										</tr>
									</thead>
									<tbody id="recordList">
									</tbody>
									<tfoot id="recordStatic">
										<tr>
											<td colspan="9" class="t_r"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
		
		</div>
		
		
		<?php
			Render('footer');
		?>
	</body>
</html>