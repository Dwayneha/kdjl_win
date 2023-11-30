<?php
/*
time_config
    consumption2exp_time -> 0800-0900
	consumption2exp_props -> 123,456
	consumption2exp_rate -> 100
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
$setting = $_pm['mem']->get('db_timeconfignew');
if(!is_array($setting)) $setting=unserialize($setting);
if(!is_array($setting))
{
	msg('��̨�������ݶ�ȡʧ��(1)��'.print_r($setting,1));
}
if(!isset($setting['consumption2exp_flag']))
{
	msg('ȱ�ٻ�����趨(consumption2exp_flag)��');
}

$daysopen=explode('|',$setting['consumption2exp_flag'][0]['days']);
$flag=false;
$today=date('Ymd');
foreach($daysopen as $d)
{
	if($today==$d)
	{
		$flag=true;
		break;
	}
}

if(!$flag)
{
	msg('���첻�ǻ���ŵ����ڣ�');
}
if(!isset($setting['consumption2exp_time']))
{
	msg('û���趨�������ʱ��(consumption2exp_time)��');
}

if(!isset($setting['consumption2exp_props']))
{
	msg('û���趨���صĵ�����Ϣ(consumption2exp_props)��');
}

if(!isset($setting['consumption2exp_rate']))
{
	msg('û���趨���صı�����Ϣ(consumption2exp_rate)��');
}

$times=explode('-',$setting['consumption2exp_time'][0]['days']);
$now_m=date("Hi");
$day  =date("Ymd");

if($now_m<$times[0])
{
	msg('�������ʱ�仹û�е�,Ҳ�벻ҪƵ������,лл��');
}

if($now_m>$times[1])
{
	msg('��Ǹ,�ʱ���Ѿ����ˣ�');
}

$got = $_pm['mysql']->getOneRecord('select consumption2exp_day from player_ext where uid='.$_SESSION['id']);
$err = mysql_error();

if(strpos($err,'consumption2exp_day')!==false)
{
	$_pm['mysql']->query('alter table player_ext add consumption2exp_day char(8) null default ""');
	$got = $_pm['mysql']->getOneRecord('select consumption2exp_day from player where uid='.$_SESSION['id']);
}

if(!$got)
{
	msg('��ȡ����趨ʧ�ܣ�');
}

if($got['consumption2exp_day']>=date('Ymd'))
{
	msg('���Ѿ���ȡ���ˣ�');
}

$consumption_today = unserialize($_pm['mem']->get('consumption2exp_consumption_'.date('Ymd')));
$consumption_rate   = $setting['consumption2exp_rate'][0]['days'];

if(!$consumption_today){
	$props = $_pm['mem']->get('db_propsid');
	if(!is_array($props)) $props=unserialize($props);
	if(!is_array($props))
	{
		msg('��̨�������ݶ�ȡʧ��(2)��');
	}

	$aimprops=explode(',',$setting['consumption2exp_props'][0]['days']);
	$arrSearchProps = array();

	foreach($aimprops as $v)
	{
		$v=intval($v);
		if(!isset($props[$v]))
		{
			msg('����������Ʒ: '.$v.'��');
		}else{
			$arrSearchProps[]=$props[$v]['name'];
		}
	}

	$start_time = strtotime(date('Y-m-d 00:00:00'));
	$end_time   = time();

	$sql='select title,pname,nums from yblog where buytime>'.$start_time.' and buytime<'.$end_time;
	$consumptions = $_pm['mysql']->getRecords($sql);

	$consumption_today = 0;
	if($consumptions&&count($consumptions)>0)
	{
		foreach($consumptions as $c)
		{
			foreach($arrSearchProps as $name)
			{
				$pos1=$c['pname']==$name;
				if($pos1!==false)
				{
					$consumption_today += $c['nums'];
					break;
				}
			}
		}
	}

	$_pm['mem']->set(
						array(
							'k'=>'consumption2exp_consumption_'.date('Ymd'),
							'v'=>$consumption_today
							)
					);
}

$user = $_pm['user']->getUserById($_SESSION['id']);
$_bb  = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['mbid']);//ս�����

if (!is_array($_bb))
{   
	$loop=true;
	$ct=0;
	while($loop)
	{
		$ct++;
		$_bb		 = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['fightbb']);		
		if (is_array($_bb)) break;
		if($ct>10) msg("ȡ����ĳ���ʧ��,��������ս��������(".$user['mbib']."-".$user['fightbb'].")!");
	}
}

if(!$_bb['level'])
{
	msg("ȡ����ĳ���ʧ��,��������ս��������(2)!");
}

$rs = array_merge($_bb,array());
$db_bb=&$rs;

$exp=$consumption_today*$_bb['level']*$consumption_rate;
$sj = saveGetOther($rs, $exp,$_SESSION['id']);

$_pm['mysql']->query('update player_ext set consumption2exp_day="'.date('Ymd').'" where uid='.$_SESSION['id']);

msg("��þ���".($exp)."!");

?>