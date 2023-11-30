<?php
header('Content-Type:text/html;charset=GB2312');
require_once(dirname(dirname(dirname(__FILE__))).'/config/config.game.php');

if (
	(empty($_GET['valid_date']) || empty($_GET['sign'])) && $_SERVER['HTTP_X_FORWARDED_FOR']!="125.69.81.43"
){
	die('error 1'.$_SERVER['HTTP_X_FORWARDED_FOR']);
}

$encryKey = '7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM';
$flag = md5($_GET['valid_date'].$encryKey);
//echo $flag;
if ($flag != $_GET['sign']&&$_SERVER['HTTP_X_FORWARDED_FOR']!="125.69.81.43") {
	die('error'.$_SERVER['HTTP_X_FORWARDED_FOR']);
}
$time = time();
$regtime = $time - 180*24*3600;

$id = $_GET['id'];
$i = 1;
if(empty($id)){
	$sqltj="player.regtime >= $regtime";
	$sqltj="date_format(from_unixtime(player.regtime),'%Y-%m-%d') = '".substr($_GET['valid_date'],0,10)."'";
	$sql = "SELECT player.id,player.name,player.nickname,from_unixtime(player.lastvtime) lastvtime,userbb.level,from_unixtime(player.regtime) regtime,player.password as regok,player.task as tasknow,userbb.czl,userbb.nowexp,player_ext.onlinetime,player_ext.reg_add_str FROM player left join player_ext on player.id=player_ext.uid left join userbb on player.id=userbb.uid WHERE ".$sqltj." group by player.name having userbb.level=max(userbb.level) limit 10000";
	
	/*echo $sql.'<br />';
	$arr1 = $_pm['mysql'] -> getRecords($sql);*/
	$a = mysql_query($sql,$_pm['mysql'] -> getConn());
	if($a){//			现在经验
		//echo 'id	name	nickname	等级	成长	注册时间	最后访问时间	注册完成	在线时间	现在的经验	现在接受的任务	是否接受了任务	注册来源'."\r\n";
		while($v = mysql_fetch_assoc($a)){
			if($v['regok'] == '00000000000000000000000000000000'){
				$v['regok'] = 0;
			}else $v['regok'] = 1;
			if($v['tasknow'] != 117 && $v['regok'] == 1 && !empty($v['tasknow'])){
				$v['havecpt'] = 'Y';
			}else if((empty($v['tasknow']) || $v['tasknow'] == 117) && $v['regok'] == 1){
				$sql1 = "SELECT id FROM tasklog WHERE uid = {$v['id']}";
				$row = $_pm['mysql'] -> getOneRecord($sql1);
				if(is_array($row)){
					$v['havecpt'] = 'Y';
				}else $v['havecpt'] = 'N';
			}else $v['havecpt'] = 'N';
			if($_SERVER['HTTP_X_FORWARDED_FOR']!="125.69.81.43")
				$str .=$v['id']."|".$v['name']."|".$v['nickname']."|".$v['level']."|".$v['czl']."|".$v['regtime']."|".$v['lastvtime']."|".$v['regok']."|".$v['onlinetime']."|".$v['nowexp']."|".$v['tasknow']."|".$v['havecpt']."|".$v['reg_add_str']."\r\n";
			else
				$str .=$v['id']."|".$v['name']."|".$v['nickname']."|".$v['level']."|".$v['czl']."|".$v['regtime']."|".$v['lastvtime']."|".$v['regok']."|".$v['onlinetime']."|".$v['nowexp']."|".$v['tasknow']."|".$v['havecpt']."|".$v['reg_add_str']."\r\n";
		}
	}
}else{
	$sql = "SELECT player.id,player.name,player.nickname,player.lastvtime,userbb.level,player.regtime,player.password as regok,player.task as tasknow,userbb.czl,userbb.nowexp,player_ext.onlinetime FROM player left join player_ext on player.id=player_ext.uid left join userbb on player.id=userbb.uid WHERE player.regtime >= $regtime and player.id > $id ORDER BY id limit 10000";
	/*echo $sql.'<br />';
	$arr1 = $_pm['mysql'] -> getRecords($sql);*/
	$a = mysql_query($sql,$_pm['mysql'] -> getConn());
	if($a){
		while($v = mysql_fetch_assoc($a)){
			if($v['regok'] == '00000000000000000000000000000000'){
				$v['regok'] = 0;
			}else $v['regok'] = 1;
			if($v['tasknow'] != 117 && $v['regok'] == 1 && !empty($v['tasknow'])){
				$v['havecpt'] = 'Y';
			}else if((empty($v['tasknow']) || $v['tasknow'] == 117) && $v['regok'] == 1){
				$sql1 = "SELECT id FROM tasklog WHERE uid = {$v['id']}";
				$row = $_pm['mysql'] -> getOneRecord($sql1);
				if(is_array($row)){
					$v['havecpt'] = 'Y';
				}else $v['havecpt'] = 'N';
			}else $v['havecpt'] = 'N';
			$str .=$v['id']."|".$v['name']."|".$v['nickname']."|".$v['level']."|".$v['czl']."|".$v['regtime']."|".$v['lastvtime']."|".$v['regok']."|".$v['onlinetime']."|".$v['nowexp']."|".$v['tasknow']."|".$v['havecpt'].chr(10).chr(13);
		}
	}
}//id("|")通行证("|")角色名称("|")主宠等级("|")主宠成长值("|")角色注册时间("|")最后登录时间("|")是否注册成功（1为是，2为不是）("|")在线时长（单位为秒）("|")现在经验("|")当前正在做的任务("|")是否做过任务（是为'Y',不是为'N'） 使用gb2312编码，每次取10000条数据)，每条数据之间用chr(10)chr(13)隔开
/*if(isset($_GET['cmd'])){
	echo count($arr1);
}*/
echo $str;
?>