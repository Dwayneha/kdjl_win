<?php
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
require_once('../sec/dblock_fun.php');
secStart($_pm['mem']);
function logs($note,$vary=103)
{
	global $_pm;
	$sql='insert into gamelog set seller='.$_SESSION['id'].',vary='.intval($vary).',pnote="'.$note.'",ptime='.time();
	$_pm['mysql']->query($sql);
}
$petId=abs(intval($_GET['pid']));
$value=abs(intval($_GET['v']));
getLock($_SESSION['id']);

$bb = $_pm['mysql']->getOneRecord('select name,wx,level,czl,remaketimes from userbb where uid='.$_SESSION['id'].' and id='.$petId);
if(!$bb)
{
	realseLock();
	die('������ﲻ����ģ�');
}
if($bb['wx']!=7)
{
	realseLock();
	die('������ﲻ�ܽ���ת����');
}

$membbid = unserialize($_pm['mem']->get('db_bbname'));
$bbO = $membbid[$bb['name']];

if(!$bbO)
{
	realseLock();
	die('�ڴ����Ҳ���Ҫ�����ĳ����ԭʼ���ݣ�');
}

$bbJhSetting = $_pm['mysql']->getOneRecord('select max_czl from super_jh where pet_id='.$bbO['id']);
if(!$bbJhSetting)
{
	realseLock();
	die('���ݿ���û�иó�����ʥ�������趨��');
}

$zhCzl=$_pm['mysql']->getOneRecord('select czl_ss from player_ext where uid='.$_SESSION['id']);
if($err=mysql_error())
{
	if(strpos($err,'czl_ss')!==false)
	{
		$_pm['mysql']->query('alter table player_ext add czl_ss int(11) null default 0;');
	}
	$zhCzl['czl_ss']=0;
}


if($value>$zhCzl['czl_ss'])
{
	realseLock();
	die('ʣ��ɳ�������');
}

$sqlPlayer = 'update player_ext set czl_ss='.($zhCzl['czl_ss']-ceil($value)).' where uid='.$_SESSION['id'];
$_pm['mysql']->query($sqlPlayer);

$extraMsg='';

if($value+$bb['czl']>$bbJhSetting['max_czl'])
{
	$value=$bbJhSetting['max_czl']-$bb['czl'];
	$extraMsg='(�ó������ɳ�����:'.$bbJhSetting['max_czl'].')';
}

$sqlBb = 'update userbb set czl='.($bb['czl']+$value).' where id='.$petId;
$_pm['mysql']->query($sqlBb);
logs("ת��{$value}�ɳ���{$petId}");
if($err=mysql_error())
{
	$_pm['mysql']->query("rollback");
	die($err);
}

realseLock();
die('OK');
?>
