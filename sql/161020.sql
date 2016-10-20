alter table tbUserInfo
add column Score_Cost int not null default 0 comment '积分消耗' after Score,
change column `Status` `Status` int not null default 1 comment '状态(0:禁用,1:启用)',
change column CreateDateTime DateTimeCreate datetime null default null comment '建立时间',
change column ModifyDateTime DateTimeModify datetime null default null comment '修改时间' ;



create table tbUserImageInfo(
	Id int primary key auto_increment,
	User_Id int not null comment '用户编号',
	OrderNumber int not null default 0 comment '图片顺序',
	Src varchar(255) comment '图片路径',
	IsDefault int not null default 0 comment '是否为默认图片(0:不是，1:是)',
	Status int not null default 0 comment '状态(0:停用,1:启用)',
	Description varchar(200) comment '描述',
	DateTimeCreate datetime comment '建立时间',
	DateTimeModify datetime comment '修改时间' 
);