
update tbUserInfo set Sex = 0 where Sex is null;
update tbUserInfo set RoleId = 0 where RoleId is null;

alter table `tbUserInfo` 
change column `Sex` `Sex` int not null default 0 comment '性别',
change column `RoleId` `RoleId` int not null default 0 comment '角色编号' ,
add column `Score` int not null default 0 comment '积分',
add column `Follow` int not null default 0 comment '关注者',
add column `MessageTotal` int not null default 0 comment '消息记录数',
add column `Status` int not null default 0 comment '状态';
