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
$petId=abs($_GET['pid']);
getLock($_SESSION['id']);

$cishu=$_pm['mysql']->getOneRecord("select chouqu_chongwu from player_ext where uid={$_SESSION['id']}");
if(strpos($cishu['chouqu_chongwu'],','.$petId.',')!==false)
{
	realseLock();
	die("��������ȡ���ɳ�,�����ٳ�ȡ!");
}

$bb = $_pm['mysql']->getOneRecord('select name,wx,level,czl,remaketimes from userbb where uid='.$_SESSION['id'].' and id='.$petId);
if(!$bb)
{
	realseLock();
	die('������ﲻ���ڣ�');
}
if($bb['wx']>6)
{
	realseLock();
	die('�ó��ﲻ�ܳ�ȡ!');
}


/*
if($bb['wx']!=6)
{
	if($bb['czl']<40){
		realseLock();
		die('��ϵ����ɳ�С��40���ܳ�ȡ!');
	}
}
*/

if($bb['czl']<30)
{
	realseLock();
	die('�ɳ�С��30�Ĳ��ܳ�ȡ��');
}

$p1=abs($_GET['pid1']);
$p2=abs($_GET['pid2']);

$sql = 'select p.effect,p.id pids,b.id,b.sums from userbag b,props p where (b.id='.$p1.' or b.id='.$p2.') and p.id=b.pid';
$rows = $_pm['mysql']->getRecords($sql);
if($bb['wx']!=6&&$rows[0]['pids']!=3383&&$rows[1]['pids']!=3383)
{
	realseLock();
	die('ȱ����ϵ�����ȡ�ı�����ߣ�');
}

$swapRateInc=0;
$swapRateIncFixed=0;
$wpLog='';
if(count($rows)>0)
{
	foreach($rows as $k=>$v)
	{
		if($p1==$p2&&$v["pids"]==3383)
		{
			$_pm['mysql']->query("rollback");
			die('�벻Ҫʹ��������ϵ�����ȡʯ��');
		}
		if($bb['wx']>5&&$v["pids"]==3383)
		{
			$_pm['mysql']->query("rollback");
			die('����ϵ���ﲻ��ʹ����ϵ�����ȡʯ��');
		}
		if($bb['wx']<6&&$v["pids"]!=3383)
		{
			$_pm['mysql']->query("rollback");
			die('��ϵ���ﲻ��ʹ�����ӱ������ߣ�');
		}
		if(strpos($v["effect"],'inczhl:')!==false&&$v["sums"]>0)
		{
			$str = str_replace('inczhl:','',$v["effect"]);
			if(strpos($str,'a')===false){
				$swapRateInc+=abs($str);
			}else{
				$swapRateIncFixed+=abs($str);
			}
			$wpLog.=' ['.$v["pids"].'] ';
			$sqlDel = 'update userbag set sums='.($v["sums"]-1).' where id='.$v["id"].' and sums>0';
			$_pm['mysql']->query($sqlDel);
			$v["sums"]-=1;
			if($p1==$p2)//ѡ��ͬһ����Ʒ
			{
				if(strpos($v["effect"],'inczhl:')!==false&&$v["sums"]>0)
				{
					$str = str_replace('inczhl:','',$v["effect"]);
					if(strpos($str,'a')===false){
						$swapRateInc+=abs($str);
					}else{
						$swapRateIncFixed+=abs($str);
					}
					
					$wpLog.=' ['.$v["pids"].'] ';
					$sqlDel = 'update userbag set sums='.($v["sums"]-1).' where id='.$v["id"].' and sums>0';
					$_pm['mysql']->query($sqlDel);
				}
			}
		}
	}
}

if($bb['czl']<65)
{
	$swapRate=rand(10,20);
}
else if($bb['czl']<85)
{
	$swapRate=rand(30,50);
}
else if($bb['czl']<100)
{
	$swapRate=rand(50,65);
}
else if($bb['czl']<110)
{
	$swapRate=65;
}
else if($bb['czl']<115)
{
	$swapRate=70;
}
else if($bb['czl']<120)
{
	$swapRate=75;
}
else
{
	$swapRate=80;
}

if($bb['wx']!=6)
{
	$swapRate=rand(5,15);
}

$swapRate+=$swapRateInc;

if($bb['czl']*($swapRate/100)>600)
{
	$czl=600/$swapRate*100;
}else{
	$czl=$bb['czl'];
}
if($swapRate>100)
{
	$swapRate=100;
}

if($bb['czl']<600)
{
	$money=$bb['czl']*10000;
}else{
	$money=6000000;
}

$czl=ceil($czl*($swapRate/100));//����ת��

$czl+=$swapRateIncFixed;
if($czl>600) $czl=600;
$rowP=$_pm['mysql']->getOneRecord('select money from player where id='.$_SESSION['id'].' for update');
if($rowP['money']<$money)
{
	$_pm['mysql']->query("rollback");
	die("���Ľ�Ҳ���(��Ҫ:".$money.")");
}

$sql = 'update player set money='.number_format($rowP['money']-$money,0,'.','').' where id='.$_SESSION['id'];
$_pm['mysql']->query($sql);

$couqu_res = $_pm['mysql'] -> getOneRecord("SELECT chouqu_chongwu FROM player_ext where uid= ".$_SESSION['id']);
if(empty($couqu_res['chouqu_chongwu']) )
{
	$sql = 'update player_ext set czl_ss=czl_ss+'.abs($czl).',chouqu_chongwu="'.",".$petId.",".'" where uid='.$_SESSION['id'];
	$_pm['mysql']->query($sql);
}
else
{
	$sql = 'update player_ext set czl_ss=czl_ss+'.abs($czl).',chouqu_chongwu=concat(chouqu_chongwu,",","'.$petId.'",",") where uid='.$_SESSION['id'];
	$_pm['mysql']->query($sql);
}

if($err=mysql_error())
{
	if(strpos($err,'czl_ss')!==false||strpos($err,'chouqu_chongwu')!==false)
	{
		$_pm['mysql']->query('alter table player_ext add czl_ss int(11) null default 0;              ');
		$_pm['mysql']->query('alter table player_ext add chouqu_chongwu varchar(255) null default "";');
		$_pm['mysql']->query($sql);
	}
}

if($bb['wx']<6){
	$_pm['mysql']->query('update userbb set name=concat(name,"-",uid),uid=0 where id='.$petId);
}else{
	$_pm['mysql']->query('update userbb set czl=1 where id='.$petId);
}
logs('����ȡ�ĳ���id='.$petId.',��ȡ��:'.abs($czl).',ʹ����Ʒ'.($wpLog==''?'��':$wpLog));

if($err=mysql_error())
{
	$_pm['mysql']->query("rollback");
	die($err);
}

realseLock();
die('OK'.$czl);
?>