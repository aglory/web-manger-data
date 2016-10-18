function UI_Tips(t,h,m){
	t = t || 'info';
	var dom = $('<div class="alert alert-'+t+'"><strong>'+h+'</strong>'+m+'</div>').appendTo("body")
	.click(function(){
		$(this).hide("slow",function(){$(this).remove();});
	}).mouseover(function(){
		$(this).addClass('mouseover');
	});
	setTimeout(function(){
		if(dom.hasClass('mouseover'))return;
		dom.hide('slow',function(){
			if($(this).hasClass('mouseover'))
				return;
			$(this).remove();
		});
	},2000);
}

function doSearch(pageIndex,sender){
	var frm = $("#frmSubmit");
	if(pageIndex != undefined && pageIndex != null){
		$("#PageIndex").val(pageIndex);
	}
	$("#recordList").html('<tr><td class="loading" colspan="100">&nbsp;</td></tr>');
	if(sender){
		$(sender).prop('disabed',true);
	}
	$.ajax({
		url:frm.attr("action"),
		type:"post",
		data:frm.serialize(),
		//dataType:"json",
		success:function(rest){
			if(sender){
				$(sender).prop('disabed',false);
			}
			if(!rest)return;
			if(!rest.status){
				UI_Tips('错误',rest.message);
				return;
			}
			
		},error:function(){
			if(sender){
				$(sender).prop('disabed',false);
			}
			$("#recordList").html();
		}
	});
}