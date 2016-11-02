function EnumConfig(model){
	var enums = {
		EducationalHistory:[
			{Key:1,Value:"小学"},
			{Key:2,Value:"初中"},
			{Key:3,Value:"中专"},
			{Key:4,Value:"高中"},
			{Key:5,Value:"大专"},
			{Key:6,Value:"本科"},
			{Key:7,Value:"研究生"},
			{Key:8,Value:"博士"}],
		Constellation:[
			{Key:1,Value:"白羊座"},
			{Key:2,Value:"金牛座"},
			{Key:3,Value:"双子座"},
			{Key:4,Value:"巨蟹座"},
			{Key:5,Value:"狮子座"},
			{Key:6,Value:"处女座"},
			{Key:7,Value:"天秤座"},
			{Key:8,Value:"天蝎座"},
			{Key:9,Value:"射手座"},
			{Key:10,Value:"魔羯座"},
			{Key:11,Value:"水瓶座"},
			{Key:12,Value:"双鱼座"}],
		CivilState:[
			{Key:1,Value:"未婚"},
			{Key:2,Value:"已婚"},
			{Key:3,Value:"离异"}]
	}
	if(model){
		model.Enums = enums;
	}else{
		model = enums;
	}
	return model;
}