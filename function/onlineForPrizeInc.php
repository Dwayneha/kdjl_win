<?php
/*
time_config
    onlineforexp -> 0-10>1:2|2:1,3:2|4:1,5:2|6:1,7:2|8:1,9:2|12:1
10-20>1:2|2:1,3:2|4:1,5:2|6:1,7:2|8:1,9:2|12:1
20-30>1:2|2:1,3:2|4:1,5:2|6:1,7:2|8:1,9:2|12:1
30-230>1:2|2:1,3:2|4:1,5:2|6:1,7:2|8:1,9:2|12:1
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=GBK');
require_once('../sec/dblock_fun.php');
function msg($m)
{
	realseLock();
	die($m);
}

$a = getLock($_SESSION['id']);
if(!is_array($a)){
	msg('�벻Ҫ������,лл��');
}

$setting = $_pm['mem']->get('db_welcome');
if(!is_array($setting)) $setting=unserialize($setting);
if(!is_array($setting))
{
	msg('��̨�������ݶ�ȡʧ��(1)��');
}
$callback=false;
foreach($setting as $row)
{
	if($row['code']=='onlineforexp'){
		$setting['onlineforexp']=$row['contents'];
		break;
	}
}

if(!$setting['onlineforexp'])
{
	msg('�û�п�����');
}

require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
$s=new socketmsg();
$s->sendMsg('updateUserOnline',$_SESSION['id']);

$arr = $_pm['mysql']->getOneRecord('select exp_got_step,last_logintime,onlinetime_today,last_online_day,last_onlinetime,onlinetime from player_ext where uid='.$_SESSION['id']);

if(!$arr)
{
	msg('��ȡ�������ʧ�ܣ�');
}

$tdStr=date('Ymd');
if($arr['last_online_day']!=$tdStr)
{
	if(date('Ymd',$arr['last_logintime'])!=$tdStr&&$arr['last_logintime']>10000000)
	{//�һ���ͷ��ҵ������
		$sql='update player_ext set exp_got_step=0,last_online_day="'.date('Ymd').'",onlinetime_today="'.(date("H")*3600+date("i")*60+date("s")).'",last_onlinetime=onlinetime where uid='.$_SESSION['id'];
		$_pm['mysql'] -> query($sql);
	}else{//�϶�������ҵ�
		$sql='update player_ext set exp_got_step=0,last_online_day="'.date('Ymd').'",onlinetime_today=0,last_onlinetime=onlinetime where uid='.$_SESSION['id'];
		$_pm['mysql'] -> query($sql);
		msg('�������콱ʱ���أ�');
	}
}else{
	$sql='update player_ext set onlinetime_today=onlinetime_today+onlinetime-last_onlinetime,last_onlinetime=onlinetime where uid='.$_SESSION['id'];
	$_pm['mysql'] -> query($sql);
}
realseLock();
$a = getLock($_SESSION['id']);
$arr = $_pm['mysql']->getOneRecord('select exp_got_step,onlinetime_today from player_ext where uid='.$_SESSION['id']);	

$onlinem=ceil($arr['onlinetime_today']/60);
//$onlinem=ceil($arr['onlinetime_today']);//���Ե�ʱ��,һ���ӵ�һ����

?>