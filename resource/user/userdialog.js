function doQueryUser(opts){
	var sender = null;
	var pageIndex = null;
	var pageSize = null;
	var frm = null;
	if(opts){
		sender = opts.sender;
		pageIndex = opts.pageIndex;
		pageSize = opts.pageSize;
		if(opts.form)frm = $(opts.form);
	}
	
	if(frm == null)
		return;
	
	if(pageIndex != undefined && pageIndex != null){
		frm.find("input[name='PageIndex']").val(pageIndex);
	}
	if(pageSize != undefined && pageSize != null){
		frm.find("input[name='PageSize']").val(pageSize);
	}
	var pageIndex = parseInt(frm.find("input[name='PageIndex']").val());
	var pageSize = parseInt(frm.find("input[name='PageSize']").val());
	frm.find(".recordList").html('<tr><td colspan="'+(opts.PageItems.length + 1)+'"><i class="icon-spinner icon-spin icon-2x pull-left">&nbsp;</i></td></tr>');
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
			frm.find(".recordList").html(template('userdialogpartial',rest));
			opts.recordCount = rest.recordCount;
			opts.pageIndexChanged = doQueryUser;
			frm.find(".recordStatic>tr:first>td:last").pager(opts);
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
			opts.find(".recordList").html();
			opts.find(".recordStatic>tr:first>td:last").html();
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

function userDialogOpen(opts){
	var html = $(template('userdialog',opts)).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		if(opts && opts.callback){
			opts.callback();
		}
	});
	return modal;
	
	
	var html = $(template('usereditor',model)).appendTo('body');
	html.find("input[type='checkbox']").bootstrapSwitch({ onText : '男',offText : '女'});
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		userSave(this,modal);
	});
	return modal;
}