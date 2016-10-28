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
			$("#recordList").html(template($("#PageTemplate").val(),rest));
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


function userImageEditor(sender,id){
	if(id == 0){
		userImageEditorRender(sender,{status:true,model:{Id:id,User_Id:$("#User_Id").val(),Description:''}});
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:"?model=userimage&action=userimageeditor",
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
			userImageEditorRender(sender,rest);
		},error:function(e,x,r){
			console.info(e);
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}


function userImageEditorRender(sender,model){
	var html = $(template('userimageeditor',model)).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		userImageSave(this,modal);
	});
	return modal;
}

function userImageSave(sender,modal){
	var form = modal.find(".editorForm");
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:form.attr("action"),
		data: new FormData(form[0]),
		type:'post',
		cache: false,
		contentType: false,
		processData: false,
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


function userImageChangeStatus(sender,id,status){
	if(sender){
		$(sender).prop('disabed',true);
	}
	if(status){
		var sender = this;
		$.ajax({
			url:'?model=userimage&action=userimagechangestatus',
			type:"post",
			data:{Id:id,Status:status},
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
			url:'?model=userimage&action=userimagechangestatus',
			type:"post",
			data:{Id:id,Status:status},
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

function userImageChangeDefault(sender,id,isdefault){
	if(isdefault){
		var sender = this;
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=userimage&action=userimagechangedefault',
			type:"post",
			data:{Id:id,IsDefault:isdefault},
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
				UI_Tips('success',"设置主图成功");
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
		return;
	}
	
	var modal = $(template('confirm',{message:'确定取消主图'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		$.ajax({
			url:'?model=userimage&action=userimagechangedefault',
			type:"post",
			data:{Id:id,IsDefault:isdefault},
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
				UI_Tips('success',"取消主图成功");
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

function userImageChangeUser_Id(sender,id){
	if(!id && $(sender.form).find(":checked[name='Id']").length==0){
		UI_Tips('warning',"未选择图片");
		return;
	}
	
	userDialogOpen({
	PageItems:[
		{Key:'Id',Val:'',Type:'radio',Sort:false,HeadCss:'t_c wd40',BodyCss:"t_c"},
		{Key:'Name',Val:'名字',BodyCss:"t_l"},
		{Key:'NickName',Val:'昵称',BodyCss:"t_l"},
		{Key:'Sex',Val:'性别',HeadCss:"wd60",BodyCss:"t_c"},
		{Key:'Status',Val:'状态',HeadCss:"wd60",BodyCss:"t_c"}],
		Status:1,
		CallBack:function(items){
			if(sender){
				$(sender).prop('disabed',true);
			}
			if(items && items.length>0){
				var data = "User_Id=" + items[0].Id;
				if(id){
					data += '&Id[]='+id;
				}else{
					$(sender.form).find(":checked[name='Id']").each(function(i,o){
						data += '&Id[]='+o.value;
					});
				}
				$.ajax({
					url:'?model=userimage&action=userimagechangeuser_id',
					type:'post',
					dataType:'json',
					data:data,
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
					},error:function(){
						if(sender){
							$(sender).prop('disabed',false);
						}
					}
				});
			}
		}
	});
}

function userImageChangeOrderNumber(sender,id,user_Id,type){
	var sender = this;
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:'?model=userimage&action=userimagechangeordernumber',
		type:"post",
		data:{Id:id,OrderType:type,User_Id:user_Id},
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
			UI_Tips('success',"移动成功");
			doQuery();
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function userImageDelete(sender,id){
	if(!id && $(sender.form).find(":checked[name='Id']").length==0){
		UI_Tips('success',"未选择图片");
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
	var modal = $(template('confirm',{message:'确定删除图片?'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=userimage&action=userimagedelete',
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

function changePageTemplate(sender){
	if($("#PageTemplate").val() == 'userimagemangerblock'){
		$(sender).html('<span class="glyphicon glyphicon-th-large"></span>图块');
		$("#PageTemplate").val("userimagemangerlist");
		$("#PageItems").val("Id,User_Id,User_Name,Status,IsDefault,OrderNumber,DateTimeCreate,DateTimeModify");
	}else{
		$(sender).html('<span class="glyphicon glyphicon-th-list"></span>列表');
		$("#PageTemplate").val("userimagemangerblock");
		$("#PageItems").val("Id,User_Id,Src,Status,IsDefault,Description");
	}
	doQuery();
}