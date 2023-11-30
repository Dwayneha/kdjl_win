<?php
require_once('./config/config.game.php');
$sid = $_SERVER['session_id'];
//$sid = session_id();
$sql = "select uid,username,nickname,sid,guild_id,team_id,lock_time,admin,vip from chat_login_auth where sid='{$sid}'";
$rs =  $_pm['mysql']->getOneRecord($sql);
$sql = "select id,password from player where id='{$rs['uid']}'";
$rs1 =  $_pm['mysql']->getOneRecord($sql);
$currtime = time();
if($rs1['password']>$currtime){
	$rs['lock_time'] = $rs1['password'];
}

	 if($rs['uid']>67366){
		    $sql = " SELECT count(uid) as count FROM tasklog WHERE taskid =1726  and uid='".$rs['uid']."'";
             $res = $_pm['mysql'] -> getOneRecord($sql);
             if($res['count']<1){
                 $rs['lock_time'] = 1577894400;
                 }
		}
               
//die();
echo  ' |'.$rs['uid'].'|'.iconv("GBK", "UTF-8", $rs['username']).'|'.iconv("GBK", "UTF-8",$rs['nickname']).'|'.$rs['sid'].'|'.$rs['guild_id'].'|'.$rs['team_id'].'|'.$rs['lock_time'].'|'.$rs['admin'].'|'.$rs['vip'];


