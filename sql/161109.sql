
alter table tbUserMessageInfo
drop column Status,
add Status_User int not null default 0 comment '收件人状态',
add Status_Sender int not null default 0 comment '发件人状态';
