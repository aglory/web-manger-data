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
			$("#recordList").html(template('homemanager',rest));
			$("#recordStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doQuery});
		},error:function(){
			if(sender){
				$(sender).prop('disabled',false);
			}
			$("#recordList").html();
			$("#recordStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doQuery});
			
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

function changeUserCountScore(sender){
	userCountScoreRender(sender,{model:{User_Id:0}});
}

function userCountScoreRender(sender,model){
	var html = $(template('userscoreeditor',EnumConfig(model))).appendTo('body');
	var modal = html.modal();
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	modal.find(".btn-save").click(function(){
		userCountScoreSave(this,modal);
	});
	return modal;
}

function userCountScoreSave(sender,modal){
	var data = new FormData(modal.find("form")[0]);
	if($("#chkAll:checked").length==0){
		if($("#recordList :checked").length==0)
			return;
		$("#recordList :checked").each(function(i,o){
			console.info(o.name);
			data.append(o.name,o.value);
		});
		if(sender){
			$(sender).prop('disabed',true);
		}
		$.ajax({
			url:'?model=home&action=userscoresave',
			type:'post',
			dataType:'json',
			contentType: false,
			processData: false,
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
				modal.modal('hide');
				doQuery();
			},error:function(){
				if(sender){
					$(sender).prop('disabed',false);
				}
			}
		});
	}
}

