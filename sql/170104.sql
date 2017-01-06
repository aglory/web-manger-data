
alter table tbCategoryImageInfo
add column Level int not null default 1 comment '等级',
add column Status int not null default 1 comment '状态',
add column DateTimeCreate datetime comment '创建时间',
add column DateTimeModify datetime comment '修改时间';

alter table tbImageInfo
add Column Level int not null default 1 comment '等级',
add column Status int not null default 1 comment '状态',
add column DateTimeCreate datetime comment '创建时间',
add column DateTimeModify datetime comment '修改时间';