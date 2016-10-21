(function ($)
{   
    $.fn.pager = function (options)
    {
        var opts = $.extend({}, $.fn.pager.defaults, options);        

        return this.each(function ()
        {
            $(this).html(renderpager(parseInt(options.pageIndex), parseInt(options.pageSize), parseInt(options.recordCount))).find("a[rel]").click(function(e){
				e.preventDefault();
				if(options.pageIndexChanged){
					options.pageIndexChanged({pageIndex:this.rel});
				}
			});
        });
    };

    function renderpager(pageIndex, pageSize, recordCount, pageIndexChanged)
    {
		var btnClass = 'btn btn-sm btn-default';
        var btns = [];
		var size = 4;
        var pageCount = Math.ceil(recordCount / pageSize);

        var pointStart = pageIndex - size;
		var pointEnd = pageIndex + size;
		
		if(pointStart < 1){
			pointStart = 1;
		}
		if(pointEnd > pageCount){
			pointEnd = pageCount;
		}
		
		if(pointStart != 1){
			btns.push('<a class="'+ btnClass +'" rel="1">1</a>');
		}
		
		if(pageIndex == 1){
			btns.push('<a class="'+ btnClass +'" disabled="disabled"><i class="icon-angle-left"></i></a>');
		}else{
			btns.push('<a class="'+ btnClass +'" rel="' + (pageIndex - 1) + '"><i class="icon-angle-left"></i></a>');
		}
		
		for(var i = pointStart ;i < pageIndex;i++){
			btns.push('<a class="'+ btnClass +'" rel="' + i + '">' + i + '</a>');
		}
		
		btns.push('<a class="'+ btnClass +'" disabled="disabled">' + pageIndex + '</a>');
		
		for(var i = pageIndex + 1 ;i <= pointEnd;i++){
			btns.push('<a class="'+ btnClass +'" rel="' + i + '">' + i + '</a>');
		}
		
		if(pageIndex >= pageCount){
			btns.push('<a class="'+ btnClass +'" disabled="disabled"><i class="icon-angle-right"></i></a>');
		}else{
			btns.push('<a class="'+ btnClass +'" rel="' + (pageIndex + 1) + '"><i class="icon-angle-right"></i></a>');
		}
		
		if(pointEnd != pageCount){
			btns.push('<a class="'+ btnClass +'" rel="' + pageCount + '">'+ pageCount +'</a>');
		}
		
		return '<div class="btn-group"><i>'+recordCount+'</i></div>'+'<div class="btn-group">'+btns.join('')+'</div>';
		
    }
    
	$.fn.pager.defaults = {
        pageIndex: 1,
        pageSize: 20
    };

})(jQuery);





