CREATE TABLE `tbUserInfo` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL COMMENT '名称',
  `Password` varchar(50) DEFAULT NULL COMMENT '密码',
  `NickName` varchar(50) DEFAULT NULL COMMENT '昵称',
  `Sex` int(11) DEFAULT NULL COMMENT '性别',
  `Img` varchar(255) DEFAULT NULL COMMENT '头像',
  `CreateDateTime` datetime DEFAULT NULL COMMENT '创建时间',
  `ModifyDateTime` datetime DEFAULT NULL COMMENT '修改时间',
  `RoleId` int default 0 comment '角色编号',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert into tbUserInfo(Name,Password,NickName,RoleId,DateTimeCreate,DateTimeModify)values('admin',md5(111111),'admin',0x7FFFFFFF,now(),now());
