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
		$(sender).prop('disabled',true);
	}
	$.ajax({
		url:frm.attr("action"),
		type:"post",
		data:frm.serialize(),
		dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabled',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('danger',rest.message);
				return;
			}
			$("#recordList").html(template('accountmanger',EnumConfig(rest)));
			$("#recordStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doQuery});
		},error:function(){
			if(sender){
				$(sender).prop('disabled',false);
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
	doQuery();
});

function accountEditor(sender,id){
	if(id == 0){
		accountEditorRender(sender,EnumConfig({status:true,model:null}));
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:"?model=account&action=accounteditor",
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
			accountEditorRender(sender,EnumConfig(rest));
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function accountEditorRender(sender,model){
	var html = $(template('accounteditor',EnumConfig(model))).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		accountSave(this,modal);
	});
	return modal;
}

function accountSave(sender,modal){
	var form = modal.find(".editorForm");
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:form.attr("action"),
		type:"post",
		data:form.serialize(),
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

function accountChangeStatus(sender,id,status){
	if(sender){
		$(sender).prop('disabed',true);
	}
	if(status){
		var sender = this;
		$.ajax({
			url:'?model=account&action=accountchangestatus',
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
	var modal = $(template('confirm',{message:'确定禁用该账号?'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		$.ajax({
			url:'?model=account&action=accountchangestatus',
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

function accountDelete(sender,id){
	var modal = $(template('confirm',{message:'确定删除该账号?'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=account&action=accountdelete',
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

function accountChangePassword (sender,id){
	var html = $(template('accountchangepassword',{status:true,model:{Id:id}})).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		accountChangePasswordSave(this,modal);
	});
	return modal;
}

function accountChangePasswordSave(sender,modal){
	var form = modal.find(".editorForm");
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:form.attr("action"),
		type:"post",
		data:form.serialize(),
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