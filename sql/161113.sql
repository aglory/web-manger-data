
create table tbTopicInfo(
	Id int primary key auto_increment,
	Code varchar(20) not null comment '专题代码',
    Title varchar(200) not null comment '专题标题',
	unique key `UNIQUE_Code` (`Code`)
)engine=InnoDB default charset=utf8 comment='专题信息';

create table tbTopicItemInfo(
	Id int primary key auto_increment,
    Topic_Id int not null comment '专题编号',
    OrderNumber int not null default 0 comment '专题项序号',
    Img varchar(255) comment '专题项图片',
    Title varchar(200) comment '专题项标题',
    Message varchar(800) comment '专题项类容'
)engine=InnoDB default charset=utf8 comment='专题项信息';
