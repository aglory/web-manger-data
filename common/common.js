function UI_Tips(t,h,m){
	t = t || 'info';
	h = h || '';
	m = m || '';
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