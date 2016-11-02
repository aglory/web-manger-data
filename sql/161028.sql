create table tbUserScoreLogInfo(
	Id int primary key not null auto_increment,
	User_Id int not null default 0,
	Type int not null default 0 comment '积分变动类型',
	Number int not null default 0 comment '积分变动数量',
	Mark varchar(400) null comment '备注',
	DateTimeCreate datetime not null comment '创建时间'
) engine ='archive'  default charset=utf8 comment '用户积分记录';

create table tbUserFollowRelativeInfo(
	Id int not null auto_increment,
	User_Id int not null default 0,
	Follow_Id int not null default 0,
	Status int not null default 0 comment '状态（1：请求，2：同意，3拒绝）',
	DateTimeCreate datetime not null comment '创建时间',
	DateTimeModify datetime not null comment '修改时间',
	primary key (Id),
	unique key `UNIQUE_User_Id_Follow_Id` (User_Id,Follow_Id)
) engine ='innodb'  default charset=utf8 comment '用户积分记录';

create table tbUserMessageInfo(
	Id int primary key not null auto_increment,
	User_Id int not null default 0,
	Sender_Id int not null default 0,
	Flag int not null default 0 comment '标记',
	Message varchar(400) null comment '消息',
	DateTimeCreate datetime not null comment '创建时间',
	DateTimeModify datetime not null comment '修改时间',
	Status int not null default 0 comment '状态'
)engine ='innodb'  default charset=utf8 comment='用户消息信息';
