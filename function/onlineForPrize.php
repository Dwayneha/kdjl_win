<?php 
require_once('onlineForPrizeInc.php');

if($onlinem>300){
	$ms=5;
}else if($onlinem>120){
	$ms=4;
}else if($onlinem>60){
	$ms=3;
}else if($onlinem>30){
	$ms=2;
}else if($onlinem>10){
	$ms=1;
}else{
	msg('�������콱ʱ���أ�');
}

$user= $_pm['user']->getUserById($_SESSION['id']);
$_bb = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['mbid']);//ս�����

if(!$_bb)
{
	msg('�뵽������<��������>,�ٵ��һ����������Ϊ��ս���');
}


if($arr['exp_got_step']<$ms)
{
	$prize=explode("\r\n",$setting['onlineforexp']);
	$prizeset='';
	foreach($prize as $p)
	{
		$t1=explode('>',$p);
		$t2=explode('-',$t1[0]);
		if($_bb['level']>=$t2[0]&&$_bb['level']<$t2[1])
		{
			$prizeset=$t1[1];
			break;
		}
	}
	
	if(!$prizeset)
	{
		msg('��̨û�и��ȼ�Ϊ'.$_bb['level'].'�ĳ������趨��');
	}

	$prize=explode(",",$prizeset);
	$prizes=array();
	foreach($prize as $p)
	{
		$ps=explode('|',$p);
		$tmp=array();
		foreach($ps as $ap)
		{
			$t=explode(':',$ap);
			$tmp[$t[0]]=$t[1];
		}
		$prizes[]=$tmp;
	}
	if(count($prizes)!=5)
	{
		msg('��̨��Ʒ���ò���, ��������ȷ��'.print_r($prizes,1).'����');
	}
	$getPrize=$prizes[$arr['exp_got_step']];
	if(empty($getPrize))
	{
		msg('��ȡʧ��, ��'.$arr['exp_got_step'].'����ȡ��');
	}
	
	$user = $_pm['user']->getUserById($_SESSION['id']);
	$totalget = 0;
	foreach($getPrize as $k=>$v)
	{
		$totalget += $v;
	}
	if ($totalget >= $user['maxbag']){
		msg('���ı����ռ䲻�㣬�������������ȡ(1)��');
		die();
	}
	$resbag = $_pm['mysql']->getOneRecord("SELECT count(*) sl FROM userbag WHERE uid = '".$_SESSION['id']."' AND sums > 0 AND zbing = 0");

	if($resbag['sl']+3 >= $user['maxbag'])
	{
		msg('���ı����ռ䲻�㣬�������������ȡ(3)��');
		die();
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
	$_pm['mysql']->query('update player_ext set exp_got_step='.($arr['exp_got_step']+1).' where uid='.$_SESSION['id']);
	if($arr['exp_got_step']==4)
	{
		msg("<!--OK-->��ϲ�����õ��˽�������".$prizeWord."���������߽�����ȫ�����ţ�ף����Ϸ��죡");
	}else{
		msg("<!--OK-->��ϲ����������߽���".$prizeWord."�����������ں��棬����Ŭ���ɡ�");
	}

}else{
	msg('<!--OK-->���Ѿ���ȡ����ˣ�');
}
?>