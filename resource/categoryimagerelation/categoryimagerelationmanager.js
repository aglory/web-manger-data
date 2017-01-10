function doCategoryQuery(opts){
	var sender = null;
	var pageIndex = null;
	var pageSize = null;
	if(opts){
		sender = opts.sender;
		pageIndex = opts.pageIndex;
		pageSize = opts.pageSize;
	}
	
	var frm = $("#mainCategoryForm");
	if(pageIndex != undefined && pageIndex != null){
		$("#CategoryPageIndex").val(pageIndex);
	}
	if(pageSize != undefined && pageSize != null){
		$("#CategoryPageSize").val(pageSize);
	}
	var pageIndex = parseInt($("#CategoryPageIndex").val());
	var pageSize = parseInt($("#CategoryPageSize").val());
	$("#recordCategoryList").html('<tr><td class="loading" colspan="100">&nbsp;</td></tr>');
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:frm.attr("action"),
		type:"post",
		data:frm.serialize(),
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			var model = EnumConfig(rest);
			model.CategoryId = frm.find("input[name='ExceptRelationImageId']").val();
			$("#recordCategoryList").html(template('categoryimagerelationcategoryselectimage',model));
			$("#recordCategoryStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doCategoryQuery});
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
			$("#recordCategoryList").html();
			$("#recordCategoryStatic>tr:first>td:last").html();
		}
	});
}
function doImageQuery(opts){
	var sender = null;
	var pageIndex = null;
	var pageSize = null;
	if(opts){
		sender = opts.sender;
		pageIndex = opts.pageIndex;
		pageSize = opts.pageSize;
	}
	
	var frm = $("#mainImageForm");
	if(pageIndex != undefined && pageIndex != null){
		$("#ImagePageIndex").val(pageIndex);
	}
	if(pageSize != undefined && pageSize != null){
		$("#ImagePageSize").val(pageSize);
	}
	var pageIndex = parseInt($("#ImagePageIndex").val());
	var pageSize = parseInt($("#ImagePageSize").val());
	$("#recordImageList").html('<tr><td class="loading" colspan="100">&nbsp;</td></tr>');
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:frm.attr("action"),
		type:"post",
		data:frm.serialize(),
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			var model = EnumConfig(rest);
			model.ImageId = frm.find("input[name='ExceptRelationCategoryId']").val();
			$("#recordImageList").html(template('categoryimagerelationimageselectcategory',model));
			$("#recordImageStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doCategoryQuery});
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
			$("#recordImageList").html();
			$("#recordImageStatic>tr:first>td:last").html();
		}
	});
}

function doQuery(opts){
	var sender = null;
	var pageIndex = null;
	var pageSize = null;
	if(opts){
		sender = opts.sender;
		pageIndex = opts.pageIndex;
		pageSize = opts.pageSize;
	}
	
	var frm = $("#mainForm");
	if(pageIndex != undefined && pageIndex != null){
		$("#PageIndex").val(pageIndex);
	}
	if(pageSize != undefined && pageSize != null){
		$("#PageSize").val(pageSize);
	}
	var pageIndex = parseInt($("#PageIndex").val());
	var pageSize = parseInt($("#PageSize").val());
	$("#recordList").html('<tr><td class="loading" colspan="100">&nbsp;</td></tr>');
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:frm.attr("action"),
		type:"post",
		data:frm.serialize(),
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			if($("#mainForm input[name='CategoryId']").val().length > 0){
				rest.Merging = 1;
			}
			if($("#mainForm input[name='ImageId']").val().length > 0){
				rest.Merging = 2;
			}
			$("#recordList").html(template('categoryimagerelationmanager',EnumConfig(rest)));
			$("#recordStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doQuery});
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
			$("#recordList").html();
			$("#recordStatic>tr:first>td:last").html();
		}
	});
}

$(function(){
	$("#mainForm .btn-query").click(function(e){
		e.preventDefault();
		doQuery({sender:this,pageIndex:1});
	});
	$("#mainForm .btn-refresh").click(function(e){
		e.preventDefault();
		doQuery({sender:this});
	});
	$("#mainForm").on("click",".btn-sort",function(e){
		$(this).removeClass("icon-sort icon-sort-up icon-sort-down");
		var lis = ($("#PageSort").val().length==0)? (new Array()) : $("#PageSort").val().split(",");
		var liv = new Array();
		var key = $(this).attr('sort-expression');
		var f = false;
		for(var i = lis.length;i>0;i--){
			var s = lis[i-1];
			var p = s.split(" ");
			var k = p[0];
			var v = p[1];
			if(k!==key){
				liv.unshift(s);
				continue;
			}
			f = true;
			switch(v){
				case 'asc':
					$(this).addClass("icon-sort");
					break;
				case 'desc':
					$(this).addClass("icon-sort-up");
					liv.unshift(key + " asc");
					break;
				default:
					$(this).addClass("icon-sort-down");
					break;
			}
		}
		if(!f){
			$(this).addClass("icon-sort-down")
			liv.push(key+" desc");
		}
		$("#PageSort").val(liv.join(','));
		doQuery();
	});
	
	
	$("#mainCategoryForm .btn-query").click(function(e){
		e.preventDefault();
		doCategoryQuery({sender:this,pageIndex:1});
	});
	$("#mainCategoryForm .btn-refresh").click(function(e){
		e.preventDefault();
		doCategoryQuery({sender:this});
	});
	$("#mainCategoryForm").on("click",".btn-sort",function(e){
		$(this).removeClass("icon-sort icon-sort-up icon-sort-down");
		var lis = ($("#CategoryPageSort").val().length==0)? (new Array()) : $("#CategoryPageSort").val().split(",");
		var liv = new Array();
		var key = $(this).attr('sort-expression');
		var f = false;
		for(var i = lis.length;i>0;i--){
			var s = lis[i-1];
			var p = s.split(" ");
			var k = p[0];
			var v = p[1];
			if(k!==key){
				liv.unshift(s);
				continue;
			}
			f = true;
			switch(v){
				case 'asc':
					$(this).addClass("icon-sort");
					break;
				case 'desc':
					$(this).addClass("icon-sort-up");
					liv.unshift(key + " asc");
					break;
				default:
					$(this).addClass("icon-sort-down");
					break;
			}
		}
		if(!f){
			$(this).addClass("icon-sort-down")
			liv.push(key+" desc");
		}
		$("#CategoryPageSort").val(liv.join(','));
		doCategoryQuery();
	});
	
	
	$("#mainImageForm .btn-query").click(function(e){
		e.preventDefault();
		doImageQuery({sender:this,pageIndex:1});
	});
	$("#mainImageForm .btn-refresh").click(function(e){
		e.preventDefault();
		doImageQuery({sender:this});
	});
	$("#mainImageForm").on("click",".btn-sort",function(e){
		$(this).removeClass("icon-sort icon-sort-up icon-sort-down");
		var lis = ($("#ImagePageSort").val().length==0)? (new Array()) : $("#ImagePageSort").val().split(",");
		var liv = new Array();
		var key = $(this).attr('sort-expression');
		var f = false;
		for(var i = lis.length;i>0;i--){
			var s = lis[i-1];
			var p = s.split(" ");
			var k = p[0];
			var v = p[1];
			if(k!==key){
				liv.unshift(s);
				continue;
			}
			f = true;
			switch(v){
				case 'asc':
					$(this).addClass("icon-sort");
					break;
				case 'desc':
					$(this).addClass("icon-sort-up");
					liv.unshift(key + " asc");
					break;
				default:
					$(this).addClass("icon-sort-down");
					break;
			}
		}
		if(!f){
			$(this).addClass("icon-sort-down")
			liv.push(key+" desc");
		}
		$("#ImagePageSort").val(liv.join(','));
		doImageQuery();
	});
});
		 
function changeCategoryLevel(sender,id,level){
	$.ajax({
		url:"?model=categoryimage&action=categoryimagechangelevel",
		type:"post",
		dataType:'json',
		data:{"Id":id,"Level":level},
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			UI_Tips('success',"修改成功");
			doQuery();
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function changeCategoryStatus (sender,id,status){
	if(!id && $(sender.form).find(":checked[name='CategoryId']").length==0){
		UI_Tips('success',"未选择相册");
		return;
	}
	var data = 'Status='+status;
	if(id)
		data += "&Id[]="+id;
	else{
		$(sender.form).find(":checked[name='CategoryId']").each(function(i,o){
			data += "&Id[]="+o.value;
		});
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	if(status){
		var sender = this;
		$.ajax({
			url:'?model=categoryimage&action=categoryimagechangestatus',
			type:"post",
			data:data,
			dataType:"json",
			success:function(rest){
				if(sender){
					$(sender).prop('disabed',false);
				}			
				if(!rest)return;
				if(!rest.status){
					UI_Tips('danger',rest.message);
					return;
				}
				UI_Tips('success',"启用成功");
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
		return;
	}
	var modal = $(template('confirm',{message:'确定禁用相册'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		$.ajax({
			url:'?model=categoryimage&action=categoryimagechangestatus',
			type:"post",
			data:data,
			dataType:"json",
			success:function(rest){
				if(sender){
					$(sender).prop('disabed',false);
				}			
				modal.modal('hide');
				if(!rest)return;
				if(!rest.status){
					UI_Tips('danger',rest.message);
					return;
				}
				UI_Tips('success',"禁用成功");
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
	});
	modal.find(".btn-no").click(function(){
		modal.modal("hide");
	});
	return modal;
}

function categoryEditor(sender,id){
	if(id == 0){
		categoryEditorRender(sender,{status:true,model:{Id:id}});
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:"?model=categoryimage&action=categoryimageeditor",
		type:'post',
		dataType:'json',
		data:{"id":id},
		success:function(rest){
			
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			categoryEditorRender(sender,rest);
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}


function categoryEditorRender(sender,model){
	var t = EnumConfig(model);
	var html = $(template('categoryimageeditor',EnumConfig(model))).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		categorySave(this,modal);
	});
	return modal;
}

function categorySave(sender,modal){
	var form = modal.find(".editorForm");
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:form.attr("action"),
		data: form.serialize(),
		type:'post',
		cache: false,
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}			
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			modal.modal('hide');
			doQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function imageEditor(sender,id){
	if(id == 0){
		imageEditorRender(sender,{status:true,model:{Id:id}});
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:"?model=image&action=imageeditor",
		type:'post',
		dataType:'json',
		data:{"id":id},
		success:function(rest){
			
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			imageEditorRender(sender,rest);
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}


function imageEditorRender(sender,model){
	var t = EnumConfig(model);
	var html = $(template('imageeditor',EnumConfig(model))).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		imageSave(this,modal);
	});
	return modal;
}

function imageSave(sender,modal){
	var form = modal.find(".editorForm");
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:form.attr("action"),
		data: form.serialize(),
		type:'post',
		cache: false,
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}			
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			modal.modal('hide');
			doQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}





 

function changeImageLevel(sender,id,level){
	$.ajax({
		url:"?model=image&action=imagechangelevel",
		type:"post",
		dataType:'json',
		data:{"Id":id,"Level":level},
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			UI_Tips('success',"修改成功");
			doQuery();
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function changeImageStatus (sender,id,status){
	if(!id && $(sender.form).find(":checked[name='ImageId']").length==0){
		UI_Tips('success',"未选择图片");
		return;
	}
	var data = 'Status='+status;
	if(id)
		data += "&Id[]="+id;
	else{
		$(sender.form).find(":checked[name='ImageId']").each(function(i,o){
			data += "&Id[]="+o.value;
		});
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	if(status){
		var sender = this;
		$.ajax({
			url:'?model=image&action=imagechangestatus',
			type:"post",
			data:data,
			dataType:"json",
			success:function(rest){
				if(sender){
					$(sender).prop('disabed',false);
				}			
				if(!rest)return;
				if(!rest.status){
					UI_Tips('danger',rest.message);
					return;
				}
				UI_Tips('success',"启用成功");
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
		return;
	}
	var modal = $(template('confirm',{message:'确定禁用图片'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		$.ajax({
			url:'?model=image&action=imagechangestatus',
			type:"post",
			data:data,
			dataType:"json",
			success:function(rest){
				if(sender){
					$(sender).prop('disabed',false);
				}			
				modal.modal('hide');
				if(!rest)return;
				if(!rest.status){
					UI_Tips('danger',rest.message);
					return;
				}
				UI_Tips('success',"禁用成功");
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
	});
	modal.find(".btn-no").click(function(){
		modal.modal("hide");
	});
	return modal;
}

function changeCategoryIdSelected(sender){
	$("#recordList :checkbox[name='CategoryId']").prop("checked",sender.checked);
}
function changeImageIdSelected(sender){
	$("#recordList :checkbox[name='ImageId']").prop("checked",sender.checked);
}

function changeAllCheckBoxStatus(sender){
	$(sender.form).find("input[name='Id']").each(function(i,o){
		o.checked = sender.checked;
	});
}

function categoryImageRelationAdd(sender,categoryId,imageId){
	if(!categoryId && $(sender.form).find(":checked[name='Id']").length==0){
		UI_Tips('success',"未选择相册");
		return;
	}
	
	
	var dat = new Array();
	
	if(categoryId){
		dat.push('CategoryIds[]='+categoryId);
	}else{
		$(sender.form).find(":checked[name='Id']").each(function(i,o){
			dat.push('CategoryIds[]='+o.value);
		});
	}
	if(imageId){
		dat.push('ImageIds[]='+imageId);
	}else{
		$(sender.form).find(":checked[name='Id']").each(function(i,o){
			dat.push('ImageIds[]='+o.value);
		});
	}
	
	
	$.ajax({
		url:'?model=categoryimagerelation&action=categoryimagerelationsave',
		type:'post',
		data:dat.join('&'),
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			UI_Tips('success',"添加成功");
			doQuery();
			
			if($("#mainCategoryForm").length != 0)
				doCategoryQuery();
			if($("#mainImageForm").length != 0)
				doImageQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function categoryImageRelationDelete(sender,categoryId,imageId){
	if(!categoryId || !imageId){
		UI_Tips('success',"未选择对应关系");
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:'?model=categoryimagerelation&action=categoryimagerelationdelete',
		type:'post',
		data:{'CategoryIds[]':categoryId,'ImageIds[]':imageId},
		dataType:'json',
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			UI_Tips('success',"删除成功");
			doQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}
