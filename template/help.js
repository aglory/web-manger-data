template.helper('ceil',function(ps,tc){
	return Math.ceil(parseInt(tc)/parseInt(ps));
});

template.helper('renderpager',function (PageIndex,PageSize,TotalCount){
	PageIndex = parseInt(PageIndex);
	PageSize = parseInt(PageSize);
	TotalCount = parseInt(TotalCount);
	
	var pt =  Math.ceil(TotalCount/PageSize);
	
	var ps = PageIndex-5;
	if(ps<1)ps=1;
	var pe = PageIndex+5;
	if(pe>pt)pe=pt;
	
	var tmp = [];
	
	if(ps!=1){
		tmp.push('<a onclick="doSearch('+(ps-1)+')">..</a>');
	}
	for(var i=ps;i<=pe;i++){
		tmp.push('<a onclick="doSearch('+i+');return false;"'+(i==PageIndex?' class="hover"':'')+'>'+i+'</a>');
	}
	if(pe!=pt){
		tmp.push('<a onclick="doSearch('+(pe+1)+')">..</a>');
	}
	return tmp.join('');
});

template.helper('dateformat',function(txtdateTime,format,d){
	if(txtdateTime == null){
		if(d==undefined) return '-';
		return d;
	}
	if(txtdateTime == '/Date(-62135596800000)/'){
		if(d==undefined) return '-';
		return d;
	}
	var dt = eval(txtdateTime.replace('/Date(','new Date(').replace(')/',')'));
	var r = {
		"yyyy": "" + dt.getFullYear(),
		"yy": "" + dt.getFullYear() % 100,
		"MM": (dt.getMonth() < 9 ? "0" : "") + (dt.getMonth() + 1),
		"M": "" + dt.getMonth() + 1,  
		"dd": (dt.getDate()<10?"0":"")+dt.getDate(),
		"d": ""+dt.getDate(),  
		"hh": (dt.getHours()<10?"0":"")+dt.getHours(), 
		"h": dt.getHours(),
		"mm": (dt.getMinutes() < 10 ? "0" : "") + dt.getMinutes(),
		"m": "" + dt.getMinutes(),
		"ss": (dt.getSeconds() < 10 ? "0" : "") + dt.getSeconds(),
		"s":""+dt.getSeconds()
	};
	for (var i in r) {
		format = format.replace(new RegExp(i), r[i]);
	}
	return format;
});

template.helper("renderEnums",function(i,l,d){
	for(var k in l){
		var item = l[k];
		if(item.Key == i)
			return item.Value;
		continue;
	}
	if(d==undefined)
		d='-';
	return d;
});

template.helper("renderEmpty",function(i,d){
	if(i==null || i.length==0){
		if(d==undefined)
			return '-';
		return d;
	}
	return i;
});

template.helper("renderBool",function(i,d){
	if(i){
		return '是';
	}else if(i==false){
		return '否';
	}
	if(d==undefined)
		return '-';
	return d;
});

template.helper("renderMoney",function(i,d){
	return i;
});


