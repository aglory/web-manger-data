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
			$("#recordList").html(template('usermessagemanger',rest));
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

function userMessageEditor(sender,id){
	if(id==0){
		userMessageRender(sender,{status:true,model:{}});
		return;
	}
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:'?model=usermessage&action=usermessageeditor',
		data:{id : id},
		type:'post',
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
			userMessageRender(sender,rest);
		},
		error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
		}
	});
}

function userMessageRender(sender,model){
	var html = $(template('usermessageeditor',model)).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		userMessageSave(this,modal);
	});
	modal.find("input[name='User_Name']").focus(function(){
		userDialogOpen({
		PageItems:[
			{Key:'Id',Val:'',Type:'radio',Sort:false,HeadCss:'t_c wd40',BodyCss:"t_c"},
			{Key:'Name',Val:'名字',BodyCss:"t_l"},
			{Key:'NickName',Val:'昵称',BodyCss:"t_l"},
			{Key:'Sex',Val:'性别',HeadCss:"wd60",BodyCss:"t_c"},
			{Key:'Status',Val:'状态',HeadCss:"wd60",BodyCss:"t_c"}],
		CallBack:function(items){
			if(items != null ||items.length == 0){
				for(var i=0;i<items.length;i++){
					modal.find("input[name='User_Name']").val(items[i].Name);
					modal.find("input[name='User_Id']").val(items[i].Id);
				}
			}
		}});
	});
	modal.find("input[name='Sender_Name']").focus(function(){
		userDialogOpen({
		PageItems:[
			{Key:'Id',Val:'',Type:'radio',Sort:false,HeadCss:'t_c wd40',BodyCss:"t_c"},
			{Key:'Name',Val:'名字',BodyCss:"t_l"},
			{Key:'NickName',Val:'昵称',BodyCss:"t_l"},
			{Key:'Sex',Val:'性别',HeadCss:"wd60",BodyCss:"t_c"},
			{Key:'Status',Val:'状态',HeadCss:"wd60",BodyCss:"t_c"}],
		CallBack:function(items){
			if(items != null ||items.length == 0){
				for(var i=0;i<items.length;i++){
					modal.find("input[name='Sender_Name']").val(items[i].Name);
					modal.find("input[name='Sender_Id']").val(items[i].Id);
				}
			}
		}});
	});
	return modal;
}

function userMessageSave(sender,modal){
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

function userMessageChangeStatus(sender,id,status){
	if(sender){
		$(sender).prop('disabed',true);
	}
	if(status){
		var sender = this;
		$.ajax({
			url:'?model=usermessage&action=usermessagechangestatus',
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
	var modal = $(template('confirm',{message:'确定禁用该用户?'})).appendTo('body').modal();
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

function userMessageDelete(sender,id){
	if(id == 0 && $("#mainForm :checked[type='checkbox']").length == 0){
		$(template('alert',{message:'未选择消息!'})).appendTo('body').modal();
		return;
	}
	var data = null;
	if(id != 0){
		data = {Id : id};
	}else{
		var ids = [];
		$("#mainForm :checked[type='checkbox']").each(function(i,o){
			ids.push('Ids[]=' + o.value);
		});
		ids = ids.join('&');
		data = ids;
	}
	var modal = $(template('confirm',{message:'确定删除该消息?'})).appendTo('body').modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-yes").click(function(){
		var sender = this;
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=usermessage&action=usermessagedelete',
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

function toggleAll(sender){
	$("#recordList :checkbox").each(function(i,o){
		o.checked=sender.checked;
	});
}

function userMessageDelete(sender,id,userid,senderid){

}	//usermessagestatuschange//usermessagechangestatus