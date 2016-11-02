

create table tbAccountInfo (
	Id int primary key auto_increment,
	Account varchar(50) default null comment '账号',
	Name varchar(50) default null COMMENT '名称',
	Password varchar(50) default null COMMENT '密码',
	Salt int default 0 comment '盐值',
	RoleId int default 0 comment '角色编号',
	SourceId int default 0 comment '来源编号',
	Status int default 0 comment '状态(0:禁用,1:启用)',
	DateTimeCreate datetime default null COMMENT '创建时间',
	DateTimeModify datetime default null COMMENT '修改时间',
	unique key UNIQUE_Name (Account)
) engine=InnoDB default charset=utf8 comment='用登录账号信息';


set @salt =floor (rand()*100000);

insert into tbAccountInfo (Id,Account,Password,Name,RoleId,Status,Salt,DateTimeCreate,DateTimeModify)
select Id,Name,md5(concat(Password,@salt)),NickName,RoleId,Status,@salt,DateTimeCreate,DateTimeModify from tbUserInfo;


alter table tbUserInfo
change column Id Id int not null unique,
drop primary key ,
drop index UNIQUE_Name;

update tbUserInfo set Name = NickName;

alter table tbUserInfo
drop Password,
drop DateTimeCreate,
drop Status,
drop RoleId;

create table tbUserStatisticsInfo(
	Id int not null unique default 0,
	CountFollow int not null default 0 comment '关注数量', 
	CountFollowed int not null default 0 comment '被关注数量',
	CountView int not null default 0 comment '被查看数量',
	CountScore int not null default 0 comment '积分数量',
	CountPoint int not null default 0 comment '点数数量',
	CountMessage int not null default 0 comment '消息数量'
) engine=innodb default charset=utf8 comment '用户统计信息';

insert into tbUserStatisticsInfo(Id,CountFollowed,CountScore)
select Id,Score,Follow from tbUserInfo;

alter table tbUserInfo
drop Score,
drop Follow,
drop MessageTotal;

create table tbUserConfiguration(
	Id int not null unique default 0,
	ConfigurationProtected int not null default 0 comment '设置保密等级',
	ConfigurationVewCost int not null default 0 comment '观看消耗积分'
) engine=innodb default charset=utf8 comment '用户配置信息';

insert into tbUserConfiguration(Id)
select Id from tbUserInfo;

alter table tbUserInfo
drop Score_Cost;


alter table tbUserInfo
add BodyHeight int not null default 0 comment '身高',
add BodyWeight int not null default 0 comment '体重',
add Birthday datetime comment '生日',
add EducationalHistory int not null default 0 comment '学历',
add Constellation int not null default 0 comment '星座',
add CivilState int not null default 0 comment '婚姻状况',
add Career varchar(50) comment '职业',
add InterestAndFavorites varchar(80) comment '兴趣爱好',
add Description varchar(800) comment '描述',
add ContactWay varchar(50) comment '联系方式',
add ContactQQ varchar(20) comment '联系QQ',
add ContactEmail varchar(50) comment '联系邮箱',
add ContactMobile varchar(50) comment '联系电话';

