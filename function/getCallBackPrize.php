<?php
/*
time_config
    callback -> 0-10>1:2,2:1#0-10>1:2,2:1#0-10>1:2,2:1#0-10>1:2,2:1
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

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//$echo = '<'.microtime_float()."-";
$a = getLock($_SESSION['id']);
//$echo .= ''.microtime_float().">\r\n";
//echo $echo;


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
	if($row['code']=='callback'){
		$callback=$row['contents'];
		break;
	}
}

if(!$callback)
{
	msg('�û�п�����');
}

$day=time()-$_SESSION['lastvtime'];
$getM=$_pm['mem']->get('callgeted_'.$_SESSION['id']);

if($day<30*24*3600||$getM)
{
	msg('���ź������Ѿ���ȡ�������߲����ʸ�');
}

$user= $_pm['user']->getUserById($_SESSION['id']);
$_bb = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['mbid']);//ս�����

if(!$_bb)
{
	msg('�뵽������<��������>,�ٵ��һ����������Ϊ��ս���');
}

//0-10>1:2,2:1#10-30>3:2,4:1#30-90>5:2,6:1#90-10000>7:2,8:1
$settings=explode('#',$callback);

$getPrize=array();
foreach($settings as $se)
{
	$t1=explode('>',$se);
	$t2=explode('-',$t1[0]);
	if(
		$_bb['czl']>=$t2[0]
		&&
		$_bb['czl']<$t2[1]
	)
	{
		$t3=explode(',',$t1[1]);
		foreach($t3 as $t4)
		{
			$t5=explode(':',$t4);
			if(count($t5)==2)
			{
				$getPrize[$t5[0]]=$t5[1];
			}
		}
		break;
	}	
}

if(empty($getPrize))
{
	msg('��̨��Ʒ���ò���, û�и��ɳ�Ϊ:'.$_bb['czl'].'���趨��Ʒ��');
}

$totalget = 0;
foreach($getPrize as $k=>$v)
{
	$totalget += $v;
}

if ($totalget >= $user['maxbag']){
	msg('���ı����ռ䲻�㣬�������������ȡ(��ҪԼ:'.$totalget.')��');
}

$props = $_pm['mem']->get('db_propsid');
if(!is_array($props)) $props=unserialize($props);
if(!is_array($props))
{
	msg('��̨��Ʒ���ݶ�ȡʧ�ܣ�');
}

$task=new task();
$prizeWord='';
foreach($getPrize as $k=>$v)
{
	$rtn=$task->saveGetPropsMore($k,$v);
	if($rtn==='200')
	{
		$_pm['mysql']->query("rollback");		
		msg('���ı����ռ䲻�㣬�������������ȡ(2)��');
	}
	$prizeWord.=$props[$k]['name'].' '.$v.'����';
}

$swfData=iconv('gbk','utf-8','��ӭ����������'.$_SESSION['nickname'].'���ص����ڴ����顿�ļ�ͥ������˻ع齱����'.substr($prizeWord,0,-2).'��');
require_once('../socketChat/config.chat.php');	
require_once('../kernel/socketmsg.v1.php');
$s=new socketmsg();
$s->sendMsg('an|'.$swfData);
$_SESSION['lastvtime']=time();
session_write_close();
$_pm['mem']->set(array('k'=>'callgeted_'.$_SESSION['id'],'v'=>1));
msg("<!--OK-->��ӭ���Ļع飬��ϲ�����������һع��Ľ�����Ʒ��".$prizeWord."ף����Ϸ��죡");

?>