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

mysql_query("DROP TRIGGER if exists avoid_userbag_minus;") or die(mysql_error());


$rs = mysql_query("delimiter //");
$rs = mysql_query("
create trigger avoid_userbag_minus before update on userbag for each row 
begin
if NEW.sums>1000000 or NEW.sums<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.uid,NEW.uid,
		concat('pid: ',OLD.pid,'->',NEW.pid,'; sums: ',OLD.sums,'->',NEW.sums,'; bsum: ',OLD.bsum,'->',NEW.bsum,'; psum: ',OLD.psum,'->',NEW.psum),201
	);
    set NEW.sums=0;
end if;

if NEW.psum>1000000 or NEW.psum<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.uid,NEW.uid,
		concat('pid: ',OLD.pid,'->',NEW.pid,'; sums: ',OLD.sums,'->',NEW.sums,'; bsum: ',OLD.bsum,'->',NEW.bsum,'; psum: ',OLD.psum,'->',NEW.psum),201
	);
    set NEW.psum=0;
end if;

if NEW.bsum>1000000 or NEW.bsum<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.uid,NEW.uid,
		concat('pid: ',OLD.pid,'->',NEW.pid,'; sums: ',OLD.sums,'->',NEW.sums,'; bsum: ',OLD.bsum,'->',NEW.bsum,'; psum: ',OLD.psum,'->',NEW.psum),201
	);
    set NEW.bsum=0;
end if;
end
") or die( "Could not connect: ".mysql_error() );

mysql_query("DROP TRIGGER if exists avoid_player_minus;") or die(mysql_error());

$rs = mysql_query("
create trigger avoid_player_minus before update on player for each row 
begin
if NEW.money>1000000000 or NEW.money<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.id,NEW.id,
		concat('player name: ',OLD.name,'; money: ',OLD.money,'->',NEW.money),202
	);
    set NEW.money=0;
end if;

if NEW.prestige>10000000 or NEW.prestige<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.id,NEW.id,
		concat('player name: ',OLD.name,'; prestige: ',OLD.prestige,'->',NEW.prestige),202
	);
    set NEW.prestige=0;
end if;

if NEW.yb>200000 or NEW.yb<0 then
	insert into gamelog(id,ptime,seller,buyer,pnote,vary)
	values(
		NULL,unix_timestamp(),
		OLD.id,NEW.id,
		concat('player name: ',OLD.name,';s yb: ',OLD.yb,'->',NEW.yb),202
	);
    set NEW.yb=0;
end if;

end
") or die( "Could not connect: ".mysql_error() );
?>