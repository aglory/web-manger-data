create table tbCategoryImageInfo(
	Id int primary key auto_increment,
	Title varchar(200) comment '标题',
	Tag varchar(200) comment '标签',
	Img varchar(255) comment '分类图片地址',
	ExtenseId varchar(200) comment '扩展主键',
	Src varchar(255) comment '采集地址',
	Scrawled int not null default 0 comment '是否已经采集'
)engine=InnoDB default charset=utf8 comment='图片分类信息';

create table tbImageInfo(
	Id int primary key auto_increment,
	CategoryId int not null default 0 comment '分类编号',
	Title varchar(200) comment '标题',
	Img varchar(255) comment '图片地址',
	ExtenseId varchar(200) comment '扩展主键',
	Src varchar(255) comment '采集地址',
	Scrawled int not null default 0 comment '是否已经采集'
)engine=InnoDB default charset=utf8 comment='图片分类信息';

create table tbCategoryImageRelation(
	CategoryId int not null default 0 comment '分类编号',
	ImageId int not null default 0 comment '图片编号',
	primary Key UnionKey(CategoryId,ImageId) comment '主键'
)engine=InnoDB default charset=utf8 comment='图片分类关系信息';
