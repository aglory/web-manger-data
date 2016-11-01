function userDialogOpen(opts){
	var html = $(template('userdialog',opts)).appendTo('body');
	var modal = html.modal();
	var userData = null;
	modal.find(".modal-dialog").draggable({handle:".modal-header"});
	
	var frm = modal.find(".mainForm");
	
	modal.find(".btn-yes").click(function(){
		if(opts && opts.CallBack){
			var selectedData = [];
			if(userData!=null){
				frm.find(":checked").each(function(i,o){
					for(var i=0;i<userData.length;i++){
						if(userData[i].Id == parseInt(o.value)){
							selectedData.push(userData[i]);
						}
					}
				});
			}
			if(opts.CallBack(selectedData)!=false){
				modal.modal('hide');
			}
		}
	});
	
	var doQuery = function(queryParams){
		var sender = null;
		if(queryParams)sender = queryParams.sender;
		
		var pageIndex = null;
		var pageSize = null;
		if(queryParams){
			pageIndex = queryParams.pageIndex;
			pageSize = queryParams.pageSize;
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
		userData=null;
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
				
				rest.PageItems = opts.PageItems;
				
				frm.find(".recordList").html(template('userdialogpartial',rest));
				userData = rest.recordList;
				frm.find(".recordStatic>tr:first>td:last").pager({pageIndex:pageIndex,pageSize:pageSize,recordCount:rest.recordCount,pageIndexChanged:doQuery});
			},error:function(x,e,f){
				if(sender){
					$(sender).prop('disabed',false);
				}
				frm.find(".recordList").html();
				frm.find(".recordStatic>tr:first>td:last").html();
			}
		});
	}
	
	frm.find(".btn-query").click(function(e){
		e.preventDefault();
		doQuery({sender:this,pageIndex:1});
	});
	frm.find(".btn-refresh").click(function(e){
		e.preventDefault();
		doQuery({sender:this});
	});
	
	frm.on("click",".btn-sort",function(e){
		$(this).removeClass("icon-sort icon-sort-up icon-sort-down");
		var lis = (frm.find("input[name='PageSort']").val().length==0)? (new Array()) : frm.find("input[name='PageSort']").val().split(",");
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
		frm.find("input[name='PageSort']").val(liv.join(','));
		doQuery();
	});
	doQuery({sender:opts.sender});
}