<?php 
ini_set('display_errors','on');
error_reporting(E_ALL);
require('config/config.mysql.php');
if(isset($_GET['dbg'])){
	echo '<b>'.__FILE__.'-->'.__LINE__.'</b><br/><pre>=';
	var_dump($_mysql	);
	echo '</pre>';
}
$conn=mysql_connect($_mysql['host'], $_mysql['user']	, $_mysql['pass']) or     die("Could not connect: " . mysql_error());
mysql_select_db($_mysql['db']	,$conn) or die("Could not connect: " . mysql_error());

mysql_query("drop procedure if exists `clear_dead_user`;");
mysql_query("drop procedure if exists `do_clear_user`;");
mysql_query("drop procedure if exists `check_clear_row`;");


$rs = mysql_query("delimiter //");
$rs = mysql_query("CREATE PROCEDURE `clear_dead_user`(in param1 INT)
BEGIN
DECLARE done,int_end_time,int_count,int_level,int_uid,myint_level,myint_czl,int_czl,int_yb,int_lastvtime,int_start_time INT(11) DEFAULT 0;
DECLARE int_step INT(11) DEFAULT 10;
DECLARE str_name,str_nickname,str_vtime varchar(60) default '';
DECLARE cur_1 CURSOR FOR
SELECT id,name,nickname,lastvtime,if(lastvtime=0,'N/A',date_format(from_unixtime(lastvtime),'%Y/%m/%d %H:%i')) FROM player where lastvtime>int_start_time and  lastvtime<int_end_time order by lastvtime desc;

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

set int_end_time=UNIX_TIMESTAMP()-param1*30*24*3600;

create table if not exists tmp_user_to_del(
   `uid` int(11) null default 0,
   `lvl` int(5) null default 0,
   `czl` int(5) null default 0,
   `yb` int(5) null default 0,
   `name` varchar(50) null default '',
   #`nickname` varchar(50) null default '',
   `vtime` int(11) null default 0,
   #`vtimestr` varchar(25) null default '',
   primary key(uid)
);

create table if not exists tmp_bb_to_del(
   `bid` int(11) null default 0,
   primary key(bid)
);

if param1=3 then
    set param1=1;
    set int_level=30;
	set int_czl=8;
    set int_start_time=0;
elseif param1=1 then
    set param1=0;
    set int_level=3;
	set int_czl=2;
    set int_start_time=UNIX_TIMESTAMP()-2*30*24*3600;
elseif param1=2 then
    set param1=0;
    set int_level=15;
	set int_czl=3;
    set int_start_time=UNIX_TIMESTAMP()-3*30*24*3600;
end if;

if param1=1 then
	#select 'Empty table now!!';
	truncate tmp_user_to_del;
    truncate tmp_bb_to_del;
end if;

#select param1,int_end_time s,date_format(from_unixtime(int_start_time),'%Y/%m/%d %H:%i') stime,date_format(from_unixtime(int_end_time),'%Y/%m/%d %H:%i') etime,int_level;

open cur_1;
  fetch cur_1 into int_uid,str_name,str_nickname,int_lastvtime,str_vtime;
repeat
  set int_count=int_count+1; 
  select level from userbb where uid=int_uid order by level desc limit 1 into myint_level;
  if myint_level<int_level then
  	select czl from userbb where uid=int_uid order by czl+0 desc limit 1 into myint_czl;
  	if myint_czl<int_czl then
      		set int_yb=0;
    		  select yb from yblog where nickname like binary str_name limit 1 into int_yb;
   		   if int_yb=0 and int_uid>0 then
			#,str_vtime ,str_nickname
			replace into tmp_user_to_del values(int_uid,myint_level,myint_czl,yb,str_name,int_lastvtime) ;
			replace into tmp_bb_to_del(bid) select id from userbb where uid=int_uid;
      		end if;
	 end if;
  end if;
  set done=0;
  fetch cur_1 into int_uid,str_name,str_nickname,int_lastvtime,str_vtime;
until done=1 end repeat;

#if param1=0 then
	#select count(*) total_dead_user,(select count(*) total_dead_bb from tmp_bb_to_del) from tmp_user_to_del;	
#end if;

if param1>0 then
	call clear_dead_user(param1);
end if;

end
",$conn) or die("Could not connect: " . mysql_error());

mysql_query("CREATE PROCEDURE `do_clear_user`()
BEGIN
DECLARE int_skill,int_bb,int_player,int_tasklog,int_userbag INT(11) DEFAULT 0;

delete from skill using skill,tmp_bb_to_del where skill.bid =tmp_bb_to_del.bid;
SELECT ROW_COUNT() into int_skill;

delete from userbb using userbb,tmp_bb_to_del where userbb.id =tmp_bb_to_del.bid;
SELECT ROW_COUNT() into int_bb;

delete from player using player,tmp_user_to_del where player.id =tmp_user_to_del.uid ;
SELECT ROW_COUNT() into int_player;

delete from tasklog using tasklog,tmp_user_to_del where tasklog.uid =tmp_user_to_del.uid ;
SELECT ROW_COUNT() into int_tasklog;

delete from userbag using userbag,tmp_user_to_del where userbag.uid =tmp_user_to_del.uid ;
SELECT ROW_COUNT() into int_userbag;

delete from userbag where sums = 0 and bsum = 0 and psum = 0;

select int_skill,int_bb,int_player,int_tasklog,int_userbag;

end") or die( "Could not connect: ".mysql_error() );

mysql_query("CREATE PROCEDURE `check_clear_row`(in param1 INT)
begin
PREPARE stmt1 FROM 'select @int_first_user_id:=uid from tmp_user_to_del order by uid limit  ?,1';

SET @int_limits =  param1;

EXECUTE stmt1 USING @int_limits;
deallocate prepare stmt1;

select
	player.id,player.name,
	date_format(from_unixtime(player.lastvtime),'%Y/%m/%d %H:%i') lastvtime,
	(select concat(userbb.level,' - ',userbb.czl) lvl_and_czl from userbb where tmp_user_to_del.uid=userbb.uid order by userbb.level desc limit 1) bb_level,
	(select yb from yblog where player.name=yblog.nickname order by yb desc limit 1) yblog
from
	tmp_user_to_del,player
where
	tmp_user_to_del.uid=player.id and tmp_user_to_del.uid<=@int_first_user_id and tmp_user_to_del.uid+200>=@int_first_user_id;

select  @int_first_user_id;

end") or die( "Could not connect: ".mysql_error() );
?>
OK