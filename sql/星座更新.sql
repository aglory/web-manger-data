
update tbUserInfo set Birthday = '1996-1-1', CivilState = 1,BodyHeight = 155,BodyWeight=80,EducationalHistory=6 where Id>=10;

update tbUserInfo set Birthday= date_add(Birthday,INTERVAL floor(rand()*8*360*-1) DAY),BodyHeight=BodyHeight+floor(rand()*10),BodyWeight=BodyWeight+floor(rand()*30),DateTimeModify = adddate(now(),INTERVAL floor(rand()*7*-1) DAY)where id>=10;  

update tbUserInfo set DateTimeModify = adddate(DateTimeModify,INTERVAL floor(rand()*7) DAY)  where id>10 and DateTimeModify is null;



update tbUserInfo set DateTimeModify = adddate(DateTimeModify,INTERVAL floor(datediff(now(),DateTimeModify)*rand()*10) DAY)  where id>10 and DateTimeModify is not null;

update tbUserInfo set Career = '在校学生' where Birthday>='1994-9-1' and Id>10;
update tbUserInfo set Career = '助理' where Birthday<'1994-9-1' and Id>10 and Id % 10=0;
update tbUserInfo set Career = '人事专员' where Birthday<'1994-9-1' and Id>10 and Id % 10=1;
update tbUserInfo set Career = '公关' where Birthday<'1994-9-1' and Id>10 and Id % 10=2;
update tbUserInfo set Career = '文员' where Birthday<'1994-9-1' and Id>10 and Id % 10=3;
update tbUserInfo set Career = '教师' where Birthday<'1994-9-1' and Id>10 and Id % 10=4;
update tbUserInfo set Career = '策划' where Birthday<'1994-9-1' and Id>10 and Id % 10=5;
update tbUserInfo set Career = '网销' where Birthday<'1994-9-1' and Id>10 and Id % 10=6;
update tbUserInfo set Career = '客服' where Birthday<'1994-9-1' and Id>10 and Id % 10=7;
update tbUserInfo set Career = '前台' where Birthday<'1994-9-1' and Id>10 and Id % 10=8;
update tbUserInfo set Career = '护士' where Birthday<'1994-9-1' and Id>10 and Id % 10=9;

update tbUserInfo set Constellation = 1 where month(Birthday)*100+day(Birthday) between 321 and 420;
update tbUserInfo set Constellation = 2 where month(Birthday)*100+day(Birthday) between 421 and 520;
update tbUserInfo set Constellation = 3 where month(Birthday)*100+day(Birthday) between 521 and 620;
update tbUserInfo set Constellation = 4 where month(Birthday)*100+day(Birthday) between 621 and 720;
update tbUserInfo set Constellation = 5 where month(Birthday)*100+day(Birthday) between 721 and 820;
update tbUserInfo set Constellation = 6 where month(Birthday)*100+day(Birthday) between 821 and 920;
update tbUserInfo set Constellation = 7 where month(Birthday)*100+day(Birthday) between 921 and 1020;
update tbUserInfo set Constellation = 8 where month(Birthday)*100+day(Birthday) between 1021 and 1120;
update tbUserInfo set Constellation = 9 where month(Birthday)*100+day(Birthday) between 1121 and 1220;
update tbUserInfo set Constellation = 10 where month(Birthday)*100+day(Birthday) >= 1221 or month(Birthday)*100+day(Birthday) <=120;
update tbUserInfo set Constellation = 11 where month(Birthday)*100+day(Birthday) between 121 and 220;
update tbUserInfo set Constellation = 12 where month(Birthday)*100+day(Birthday) between 221 and 320;