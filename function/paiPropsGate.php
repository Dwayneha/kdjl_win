<?php

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);
if (!defined(MAX_PAI_VALIDTIME))
define(MAX_PAI_VALIDTIME, 10800);
$err = 0;
$user = $_pm['user'] -> getUserById($_SESSION['id']);
$userBag = $_pm['user'] -> getUserBagById($_SESSION['id']);
$bid = intval($_REQUEST['bid']);
$sql = "SELECT paisj,sj FROM player_ext WHERE uid = {$_SESSION['id']}";
$sjarr = $_pm['mysql'] -> getOneRecord($sql);
if(is_array($sjarr)){
	$user['sj'] = $sjarr['sj'];
	$user['paisj'] = $sjarr['paisj'];
}else $user['sj'] = $user['paisj'] = 0;
//����һ����ȴʱ��
$srctime = 5;
#################����һ�����ʱ��################
$time = $_SESSION['checktimes'.$_SESSION['id']];
if(empty($time))
{	
	$_SESSION['checktimes'.$_SESSION['id']] = time();
}
else
{
	$nowtime = time();
	$ctime = $nowtime - $time;
	if($ctime < $srctime)
	{
		die("100");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['checktimes'.$_SESSION['id']] = time();
	}
}

//ȡ��
if($_REQUEST['action'] == "")
{
	if($bid == "" || $bid < 1)
	{
		die('0');
	}
	
	//�ж��û������Ƿ�����
	$bagNum=0;
	
	if(is_array($userBag))
	{
		foreach($userBag as $x => $y)
		{
			if($y['sums']>0 and $y['zbing'] == 0) 
			{
				$bagNum++;		
			}
		}
	}

	if($bagNum >= $user['maxbag'])
	{
		die('1');
	}

	$sql = "SELECT psum FROM userbag WHERE uid = {$_SESSION['id']} and id = {$bid}";
	$row = $_pm['mysql'] -> getOneRecord($sql);
	if($row['psum'] == 0)
	{
		die('2');
	}
	$sql = "UPDATE userbag 
			SET sums = sums + psum,psum = 0,pstime = 0,petime = 0,psell = 0,psj = 0 
			WHERE uid = {$_SESSION['id']} and id = {$bid}";
	$_pm['mysql'] -> query($sql);
	$err = 3;
	$_pm['mem']->memClose();
	echo $err;
}
//ȡǮ
else if($_REQUEST['action'] == "money")
{
	if($user['paimoney'] <= 0 && $user['paisj'] <= 0)
	{
		die('0');
	}
	$sql = "UPDATE player
			SET money = money + paimoney,paimoney = 0
			WHERE id = {$_SESSION['id']}";
	$_pm['mysql'] -> query($sql);
	$sql = "UPDATE player_ext
			SET sj = sj + paisj,paisj = 0
			WHERE uid = {$_SESSION['id']}";
	$_pm['mysql'] -> query($sql);
	$err = 1;
	$_pm['mem']->memClose();
	echo $err;
}
//��������
else if($_REQUEST['action'] == "sale")
{
	$err = 5;
	$sql = "SELECT psum,petime 
			FROM userbag
			WHERE pid = {$bid} and uid = {$_SESSION['id']}";
	$bag = $_pm['mysql'] -> getOneRecord($sql);
	if(is_array($bag))
	{
		if($bag['psum'] <= 0)
		{
			die("1");//û��Ҫ������������Ʒ
		}
		else
		{
			if($bag['petime'] < time())
			{
				$time = time();
				$et  = $time + MAX_PAI_VALIDTIME;
				$sql = "UPDATE userbag set pstime = {$time},petime = {$et} WHERE uid = {$_SESSION['id']} and pid = {$bid}";
				$_pm['mysql'] -> query($sql);
			}
			else
			{
				die("0");//����������
			}
		}
	}
	echo "5";
}
?>