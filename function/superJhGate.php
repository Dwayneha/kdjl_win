<?php 
function logs($note,$vary=103)
{
	global $_pm;
	$sql='insert into gamelog set seller='.$_SESSION['id'].',vary='.intval($vary).',pnote="'.$note.'",ptime='.time();
	$_pm['mysql']->query($sql);
}
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
require_once('../sec/dblock_fun.php');
secStart($_pm['mem']);

$petId=abs($_GET['pid']);
getLock($_SESSION['id']);

$bb = $_pm['mysql']->getOneRecord('select name,wx,level,czl,remaketimes from userbb where uid='.$_SESSION['id'].' and id='.$petId);
if(!$bb)
{
	realseLock();
	die('������ﲻ����ģ�');
}

if($bb['remaketimes']>=10)
{
	realseLock();
	die('���ĳ����Ѿ��ﵽ�ý׶ν������ޣ��޷��ٽ����ˣ�');
}

if($bb['wx']!=7)
{
	realseLock();
	die('��ȷ�����ĳ����Ƿ�Ϊ��ʥ���');
}

$membbid = unserialize($_pm['mem']->get('db_bbname'));
$bbO = $membbid[$bb['name']];

if(!$bbO)
{
	realseLock();
	die('�ڴ����Ҳ���Ҫ�����ĳ����ԭʼ���ݣ�');
}

$bbJhSetting = $_pm['mysql']->getOneRecord('select zs_progress,need_levels,need_props,max_czl from super_jh where pet_id='.$bbO['id']);
if(!$bbJhSetting)
{
	realseLock();
	die('���ݿ���û�иó�����ʥ�������趨��');
}

$p1=abs($_GET['zjsxdj']);
$sql = 'select p.effect,p.id pids,b.id,b.sums from userbag b,props p where b.id='.$p1.' and p.id=b.pid';
$v = $_pm['mysql']->getOneRecord($sql);
$zjsx=array();
if($v)
{
	$str=explode(':',$v['effect']);
	$zjsx[str_replace('zjsxdj_','',$str[0])]=preg_replace("/[^\d]/",'',$v['effect']);
	$sqlDel = 'update userbag set sums='.($v["sums"]-1).' where id='.$v["id"].' and sums>0';
	$_pm['mysql']->query($sqlDel);
}

$nlvls = explode(',',$bbJhSetting['need_levels']);
if(count($nlvls)-1<$bb['remaketimes'])
{
	$limitlvl=$nlvls[0];
}else{
	$limitlvl=$nlvls[$bb['remaketimes']];
}

if($bb['level']<$limitlvl)
{
	realseLock();
	die('����ȼ�('.$limitlvl.')�����������������');
}

$nprops = explode(',',$bbJhSetting['need_props']);
if(count($nprops)<$bb['remaketimes']) 
{
	$npropsIds=$nprops[0];
}else{
	$npropsIds=$nprops[$bb['remaketimes']];
}

$gold=($bbJhSetting['zs_progress']+$bb['remaketimes'])*10000;
//��ǰ�ɳ�+������ȼ�/����ɳ�+������ȼ�/100*����ת���׶Σ���*��1-��������/100��+0.1
//$newCzl=$bb['czl']+($bb['level']/$bb['czl']+$bb['level']/100*$bbJhSetting['zs_progress'])*(1-$bb['remaketimes']/100)+0.1;
$player = $_pm['mysql']->getOneRecord('select id,money from player where id='.$_SESSION['id'].' for update');
if(!$player)
{
	realseLock();
	die('��ȡ�������ʧ�ܣ�');
}else if($player['money']<$gold){
	realseLock();
	die('��Ҳ��㣡');
}
$_pm['mysql']->query('update player set money='.($player['money']-$gold).' where id='.$_SESSION['id']);

$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
$updatePropsLog='������Ʒ';
$npropsIds = explode('|',$npropsIds);

$perCzl=0;

foreach($npropsIds as $str)
{
	$items=explode(':',$str);
	$bag = $_pm['mysql']->getOneRecord('select id,pid,sums from userbag where pid='.abs($items[0]).' and sums>='.abs($items[1]).' and uid='.$_SESSION['id'].' limit 1 for update');
	if($mempropsid[$bag['pid']])
	{
		if(strpos($mempropsid[$bag['pid']]['effect'],'ssjh:')!==false)
		{
			$chance=explode('|',str_replace('ssjh:','',$mempropsid[$bag['pid']]['effect']));
			$perCzl=rand($chance[0]*100,$chance[1]*100);
		}
	}

	if(!$bag)
	{
		$_pm['mysql']->query("rollback");
		realseLock();
		die('��Ʒ���㣡');
	}else{
		$sqlb='update userbag set sums='.($bag['sums']-abs($items[1])).' where id='.$bag['id'];
		$_pm['mysql']->query($sqlb);
		//echo $sqlb.mysql_error();
		$updatePropsLog.=$bag['pid'].'='.abs($items[1]).'����';
	}
}

//$newCzl=$bb['czl']*(1+$perCzl/100);
$newCzl=$bb['czl']+$perCzl/100;
if($newCzl>$bbJhSetting['max_czl'])
{
	$newCzl=$bbJhSetting['max_czl'];
}
$newCzl = number_format($newCzl,1,'.','');

$updatePropsLog.='��ң�'.$player['money'].'�����У������٣�'.$gold.'���ɳ���'.$bb['czl'].'->'.$newCzl.'��';

$db_bb=$_pm['user']->getUserPetByIdS($_SESSION['id'],$petId);
$wx_sx=$_pm['mysql']->getOneRecord('select * from wx where id='.$db_bb['wx']);

if(!$wx_sx)
{
	$_pm['mysql']->query("rollback");
	realseLock();
	die('���ҳ��������趨ʧ�ܣ�');
}

//$arrSx=array('hp','mp','ac','mc','speed','hits','miss');

//[��ǰ����*��0.3+��������/30��+��ǰ����*��������*����׶�/���ɳ�*7��]*���ٷְ�+���߰ٷֱȣ�
$hp = round($db_bb['srchp']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['hp'])));
$mp = round($db_bb['srcmp']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['mp'])));
$ac = round($db_bb['ac']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['ac'])));
$mc = round($db_bb['mc']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['mc'])));
$speed = round($db_bb['speed']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['speed'])));
$hits = round($db_bb['hits']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['hits'])));
$miss = round($db_bb['miss']*(0.3+($db_bb['remaketimes']+1)/30+($db_bb['remaketimes']+1)*$bbJhSetting['zs_progress']/($db_bb['czl']*$wx_sx['miss'])));
$logCurrent='����ǰ���ԣ�hp='.$db_bb['hp'].',mp='.$db_bb['mp'].',ac='.$db_bb['ac'].',mc='.$db_bb['mc'].',speed='.$db_bb['speed'].',hits='.$db_bb['hits'].',miss='.$db_bb['miss'].';';
foreach($zjsx as $k=>$v)
{
	$$k*=1+$v/100;
}

//$_pm['mysql']->query('update userbb set  where id='.$petId);
$sqlsx="UPDATE userbb
			   SET
			   	   remaketimes=remaketimes+1,
				   level=1,czl=".$newCzl.",
				   lexp=100,
				   nowexp=0,
				   ac	=	{$ac},
				   mc	=	{$mc},
				   srchp=	{$hp},
				   hp	=	{$hp},
				   srcmp=	{$mp},
				   mp	=	{$mp},				   
				   hits	=	{$hits},
				   miss	=	{$miss},
				   speed=	{$speed}
			 WHERE id={$db_bb['id']} and uid={$db_bb['uid']}
		   ";
$_pm['mysql']->query($sqlsx);

if($e=mysql_error())
{
	$updatePropsLog='����ʧ�ܣ�ִ�лع������ݣ�'.$updatePropsLog;
	$_pm['mysql']->query("rollback");
	realseLock();
	logs($updatePropsLog);
	echo $sqlsx."<br>";
	die('��������'.$e.'��');
}else{
	logs($updatePropsLog.'<br>'.$logCurrent.'<br/>���Ա仯SQL:'.$sqlsx);
}
		   
realseLock();
die('OK');
?>