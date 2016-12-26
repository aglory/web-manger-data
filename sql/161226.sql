
alter table tbUserInfo
modify Id int primary key auto_increment,
add AccountId int not null default 0 comment '登录账号编号' after `Id`,
add `DateTimeCreate` datetime DEFAULT NULL COMMENT '修改时间' after Img;
