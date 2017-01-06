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
			$("#recordList").html(template($("#PageTemplate").val(),EnumConfig(rest)));
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
});

function categoryImageChangeLevel(sender,id,level){
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


function categoryImageEditor(sender,id){
	if(id == 0){
		categoryImageEditorRender(sender,{status:true,model:{Id:id}});
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
			categoryImageEditorRender(sender,rest);
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}


function categoryImageEditorRender(sender,model){
	var t = EnumConfig(model);
	var html = $(template('categoryimageeditor',EnumConfig(model))).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		categoryImageSave(this,modal);
	});
	return modal;
}

function categoryImageSave(sender,modal){
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


function categoryImageChangeStatus(sender,id,status){
	if(!id && $(sender.form).find(":checked[name='Id']").length==0){
		UI_Tips('success',"未选择相册");
		return;
	}
	var data = 'Status='+status;
	if(id)
		data += "&Id[]="+id;
	else{
		$(sender.form).find(":checked[name='Id']").each(function(i,o){
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

function categoryImageDelete(sender,id){
	if(!id && $(sender.form).find(":checked[name='Id']").length==0){
		UI_Tips('success',"未选择相册");
		return;
	}
	var data = '';
	if(id)
		data = "Id[]="+id;
	else{
		$(sender.form).find(":checked[name='Id']").each(function(i,o){
			if(data.length == 0){
				data = "Id[]="+o.value;
			}else{
				data += "&Id[]="+o.value;
			}
		});
	}
	var modal = $(template('confirm',{message:'确定删除相册?'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=categoryimage&action=categoryimagedelete',
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
				UI_Tips('success',"删除成功");
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

function categoryImageScrawled(sender,id){
	if(sender){
		$(sender).prop('disabled',true);
	}
	$.ajax({
		url:'?model=categoryimage&action=categoryimagescrawled',
		type:'post',
		data:{Id:id},
		dataType:'json',
		success:function(rest){
			if(sender){
				$(sender).prop('disabled',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			UI_Tips('success',"采集成功");
			doQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabled',false);
			}
		}
	});
}

function changeAllCheckBoxStatus(sender){
	$("#recordList :checkbox[name='Id']").prop("checked",sender.checked);
}

function changePageTemplate(sender){
	if($("#PageTemplate").val() == 'categoryimageblock'){
		$(sender).html('<span class="glyphicon glyphicon-th-large"></span>图块');
		$("#PageTemplate").val("categoryimagelist");
		//$("#PageItems").val("Id,User_Id,User_Name,Status,IsDefault,OrderNumber,DateTimeCreate,DateTimeModify");
	}else{
		$(sender).html('<span class="glyphicon glyphicon-th-list"></span>列表');
		$("#PageTemplate").val("categoryimageblock");
		//$("#PageItems").val("Id,User_Id,Src,Status,IsDefault,Description");
	}
	doQuery();
}