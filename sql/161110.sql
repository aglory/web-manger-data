
alter table tbUserScoreLogInfo
add TotalNumber int not null default 0 comment '积分总数';

alter table tbUserMessageInfo
add column ReplayId int not null default 0 comment '回复编号';