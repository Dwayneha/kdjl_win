<?php
session_start();
header('Content-Type:text/html;charset=GBK'); 
// Cancel display online player count
/*if(in_array($_SESSION['username'],$_gm['name']) ) {
}else{
exit();
}*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
$min = 300;

/*
$rs = $_pm['mysql']->getOneRecord("
							select 
								count(id) olu
							from 
								player 
							where lastvtime>unix_timestamp()-{$min}
						 ");
*/
$domainPrefix = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],"."));
//echo $domainPrefix.'_online_user';
if(substr($domainPrefix,0,5) == 'kdjls')
{
	$domainPrefix = 'pm'.substr($domainPrefix,5);
}
$domainPrefix = 'pokeelf';
$rs = unserialize($_pm['mem']->get($domainPrefix.'_online_user'));
echo $rs+300;

$setting = $_pm['mem']->get('db_timeconfignew');
if(!is_array($setting)) $setting=unserialize($setting);

if(!is_array($setting))
{
	echo '<!--��̨�������ݶ�ȡʧ��(1)��'.print_r($setting,1).'-->';die();
}

if(!isset($setting['consumption2exp_time']))
{
	echo '<!--û���趨�������ʱ��(consumption2exp_time)��'.'-->';die();
}
	
$times=explode('-',$setting['consumption2exp_time'][0]['days']);
$now_m=date("Hi");
if($now_m<$times[0])
{
	echo '<!--�������ʱ�仹û�е�,Ҳ�벻ҪƵ������,лл��-->';die();
}

if($now_m>$times[1])
{
	echo '<!--��Ǹ,�ʱ���Ѿ����ˣ�-->';die();
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

if($now_m<$times[0]||$now_m>$times[1]||!$flag)
{
	echo '<!--'.$now_m.'<'.$times[0].'||'.$now_m.'>'.$times[1].'||'.$flag.'-->';
}else{
	echo '<!--consumption2exp-->';
}
?>
