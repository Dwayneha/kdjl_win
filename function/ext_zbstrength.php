<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: ̷�

*@Write Date: 2008.09.12
*@Update Date: 2008.09.12
*@Usage: װ��ǿ��
*@Note: NO Add magic props.
  ��ģ����Ҫ���ܣ�
  	 װ��ǿ��
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);

$srctime = 5;
#################����һ�����ʱ��################
$time = $_SESSION['tgtimes'.$_SESSION['id']];
if(empty($time))
{	
	$_SESSION['tgtimes'.$_SESSION['id']] = time();
}
else
{
	$nowtime = time();
	$ctime = $nowtime - $time;
	if($ctime < $srctime)
	{
		die("11");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['tgtimes'.$_SESSION['id']] = time();
	}
}
##################�������������#################
$user		= $_pm['user']->getUserById($_SESSION['id']);
//$props		= unserialize($_pm['mem']->get(MEM_PROPS_KEY));
$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);

$pid = intval($_REQUEST['pid']);
$pids = intval($_REQUEST['pids']);
$id = intval($_REQUEST['bid']);
$err = "";
$plus_tms_eft = "";
$baodeng = '';
if(!is_numeric($pid) || empty($pid))
{
	die("0");//û����Ӧ��Ҫǿ����װ��
}

/*if(lockItem($pid) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}*/

if(!is_numeric($pids))
{
	unLockItem($pid);
	die("1");//�������߳���
}
//�õ���Ҹ�װ��ǿ����Ҫ�ĵ���ID
if(!empty($pid))
{
	$p = $mempropsid[$pid];
	//foreach($props as $p)
	//{
		//if($p['id'] == $pid)
		//{
			$pname = $p['name'];
			$nid = $p['pluspid'];
		//}
	//}
}
$log .= 'װ������ID��'.$id.',���֣�'.$pname.'';
$log .= '-ǿ���ȼ���';
foreach($userBag as $ubag)
{
	if($ubag['pid'] == $nid && $ubag['sums']>0)
	{$rs = $ubag;
		$nsums = $ubag['sums'];//ǿ������Ҫ����Ʒ���û������еĸ���
		$fid = $ubag['id'];
		break;
	}
}
if($nsums < 1)
{
	unLockItem($pid);
	die("4");
}
foreach($userBag as $ubag)
{
	if($ubag['id'] == $id)
	{
		if(empty($ubag['plus_tmes_eft']))
		{
			$nums = 0;//��ǰ���Ҫǿ���Ĵ���
			$num2 = 0;//����ɹ��������ǿ���Ĵ���
			$log .= "0";
		}
		else
		{
			$plus_tms_eft = $ubag['plus_tmes_eft'];
			$log .= $plus_tms_eft;
			$effect = explode(",",$plus_tms_eft);
			foreach($harden as $kh => $vh)
			{
				$khs = $kh + 1;
				if($effect[0] == $kh)
				{
					$nums = $khs;
					$num2 = $khs;//ǿ���Ĵ���
					break;
				}
			}
		}
	}
}
//ֻ��ǿ��15��
if($nums >= 15)
{
	unLockItem($pid);
	die("15");
}
//�ж���ҵ�ǿ������,�Ӷ��õ���Ҹô�ǿ���ļ��� $num
//�õ�����
foreach($harden as $ks => $h)
{
	$a = $nums;
	if($ks == $a)
	{
		$arr = explode(",",$h);
		$num = $arr[0];
		break;
	}
	else
	{
		$num = 6;
	}
}
//��ʹ�ø������ߺ����
if(!empty($pids))
{

	//3.20
	foreach($userBag as $ub)
	{
		if($ub['pid'] == $pids && $ub['sums'] >= 1)
		{
			$flag = $ub['sums'];
			break;
		}
	}
	if(empty($flag))
	{
		unLockItem($pid);
		die("1");
	}
	
	$log .= '-�������ߣ�'.$pids;
	$prop = $mempropsid[$pids];
	//foreach($props as $prop)
	//{
		//if($prop['id'] == $pids)
		//{
			$peffarr = explode(":",$prop['effect']);
			$peffe = explode(",",$peffarr[1]);
			if($peffarr[0] == "suc")
			{
				if(count($peffe) == 1)
				{
					$num = $num + $peffarr[1];
				}
			}
			else if($peffarr[0] == "100suc")
			{
				if($nums < $peffe[1])
				{
					$num = 10;
				}
			}
			else if($peffarr[0] == "baodi")
			{
				$baodi = explode("-","-1");
			}else if($peffarr[0] == "baodeng")
			{
				$baodeng = 1;
			}
		//}
	//}
}
$sj = rand(1,10);
if($sj <= $num)
{
	//װ��ǿ���ɹ������µ����ݿ⣬ͬʱ�۳������Ӧ�Ľ��
	/*foreach($props as $pv)
	{
		if($pv['id'] == $pid)
		{
			$plusget = $pv['plusget'];
			break;
		}
	}*/
	$plusget = $mempropsid[$pid]['plusget'];
	$numarr = explode(",",$harden[$nums]);
	$num = $numarr[0];
	$moneys = $numarr[1];//װ��ǿ��������
	//�����ж���ҵĽ���Ƿ��㹻
	$money = $user['money'];
	if($money < $moneys)
	{
		unLockItem($pid);
		die("3");//��Ҳ���
	}
	//��ȥ�����Ӧ�Ľ��
	$money = $money - $moneys;
	$sql = "UPDATE player 
			SET money = {$money}
			WHERE id = {$_SESSION['id']}";
	$_pm['mysql'] -> query($sql);
	//ǿ������ϵ�����
	$plusarr = explode(",",$plusget);
	$plus = $plusarr[$nums];
	$plusstr = $num2.",".$plus;
	$sql = "UPDATE userbag
			SET plus_tms_eft = '{$plusstr}' 
			WHERE id = {$id}";
	$_pm['mysql'] -> query($sql);
	$err = 10;
}
else
{
	if(is_array($baodi))
	{
		if(!empty($plus_tms_eft))
		{
			$arr = explode(",",$plus_tms_eft);
			/*foreach($props as $pv)
			{
				if($pv['id'] == $pid)
				{
					$plusget = $pv['plusget'];
					break;
				}
			}*/
			$plusget = $mempropsid[$pid]['plusget'];
			$plusarr = explode(",",$plusget);
			$a = $nums - 2;
			$b = $num2 - 2;
			if($a >= 0)
			{
				$plus = $plusarr[$a];
				$plusstr = $b.",".$plus;
				$sql = "UPDATE userbag
						SET plus_tms_eft = '{$plusstr}' 
						WHERE id = {$id}";
			}
			else
			{
				$sql = "UPDATE userbag
						SET plus_tms_eft = 0
						WHERE id = {$id}";
			}
		}
		else
		{
			$sql = "UPDATE userbag
						SET plus_tms_eft = ''
						WHERE id = {$id}";
		}
		$_pm['mysql'] -> query($sql);
	}
	else if($baodeng != 1)
	{
		$sql = "DELETE FROM userbag
				WHERE id={$id}";
		$_pm['mysql'] -> query($sql);
	}
	
	$sql = "INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		    VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$log}',5)";
	$_pm['mysql'] -> query($sql);
	$err = 2;//ʧ��
	
}
//�����ǳɹ�����ʧ�ܣ�������˸������ߣ���Ҫ��ȥ
if(!empty($pids))
{
	foreach($userBag as $v)
	{
		if($v['pid'] == $pids)
		{
			$sum = $v['sums'];
			break;
		}
	}
	/*
	if($sum > 1)
	{
		$sql = "UPDATE userbag
						SET sums = sums - 1
						WHERE uid = {$_SESSION['id']} and pid = {$pids}";
	}
	else
	{
		$sql = "DELETE FROM userbag
				WHERE uid = {$_SESSION['id']} and pid = {$pids}";
	}*/
	$sql = "UPDATE userbag
			   SET sums = abs(sums-1) WHERE uid = {$_SESSION['id']} and pid = {$pids} and sums > 0";
	$_pm['mysql'] -> query($sql);
	$result = mysql_affected_rows($_pm['mysql'] -> getConn());
	if($result != 1){
		unLockItem($pid);
		die("��û����Ӧ����Ʒ��");
	}
}
//��ȥ����ĵ���
/*
if($nsums > 1)
{
	$sql = "UPDATE userbag
			SET sums = sums - 1
			WHERE uid = {$_SESSION['id']} and pid = {$nid};";
}
else
{
	$sql = "DELETE FROM userbag
			WHERE uid = {$_SESSION['id']} and pid = {$nid}";
}*/

$sql = "UPDATE userbag
		SET sums = abs(sums-1) WHERE uid = {$_SESSION['id']} and pid = {$nid} and id = {$fid} and sums > 0;";
$_pm['mysql'] -> query($sql);
$result = mysql_affected_rows($_pm['mysql'] -> getConn());
if($result != 1){
	unLockItem($pid);
	die("��û����Ӧ����Ʒ��");
}
//���session;
$_SESSION['pid'.$_SESSION['id']] = "";
$_SESSION['pids'.$_SESSION['id']] = "";
$_SESSION['bid'.$_SESSION['id']] = "";
unLockItem($pid);
echo $err;
?>