<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.09.25
*@Update Date: 
*@Usage: �����й�
*@Note: none
*/
/*ini_set('display_errors',true);
error_reporting(E_ALL);*/
session_start();
require_once('../config/config.game.php');

secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$petsAll  = $_pm['user']->getUserPetById($_SESSION['id']);
$rs	= unserialize($_pm['mem']->get(MEM_PROPS_KEY));
$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
$action = $_REQUEST['action'];
header('Content-Type:text/html;charset=GBK');

if(lockItem($user['mbid']) === false)
{
	//die('�Ѿ��ڴ����ˣ�');
	sleep(3);
}




//����һ����ȴʱ��
$srctime = 1;
#################����һ�����ʱ��################
$time = $_SESSION['paitimes'.$_SESSION['id']];
if(empty($time))
{	
	$_SESSION['paitimes'.$_SESSION['id']] = time();
}
else
{
	$nowtime = time();
	$ctime = $nowtime - $time;
	if($ctime < $srctime)
	{
		unLockItem($user['mbid']);
		die("��������æ�����Ժ������");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['paitimes'.$_SESSION['id']] = time();
	}
}

//������Ϣ
if($action == 'getinfo'){
	$id = intval($_GET['id']);
	if($id <= 0){
		unLockItem($user['mbid']);
		die('1');
	}
	$mesarr = array(1=>'��Ϣ',2=>'��������',3=>'ð������');
	foreach($petsAll as $v){
		if($v['id'] == $id){
			$mes = $mesarr[$v['tgmes']];
			$tgtime = $v['tgtime'];
			$stime = $v['tgstime'];
		}
	}
	$time = time();
	$ctime = $time - $stime;
	if($ctime < 0){
		$flag = '�ȴ���';
	}else if($ctime < $tgtime){
		$flag = '�й���';
	}else{
		$flag = '�й����';
	}
	$str = '�й�ʱ�䣺'.($tgtime/3600).'Сʱ&nbsp;�йܷ�ʽ:'.$mes.'&nbsp;�й�״̬��'.$flag;
	unLockItem($user['mbid']);
	die($str);
}
if($action == 'times'){
	$id = intval($_GET['id']);
	if($id <= 0){
		unLockItem($user['mbid']);
		die('1');
	}
	foreach($petsAll as $v){
		if($v['id'] == $id){
			$rs = $v;
		}
	}
	$time = time();
	$ctime = $time - $rs['tgstime'];
	if($ctime < 0){
		unLockItem($user['mbid']);
		die('2');
	}else if($ctime < $rs['tgtime']){
		//�۳�ˮ����=��ҽ�ʡʱ�䣨����ҵ���������ʱ��ʣ�й�ʱ�䣬��λΪs��*200sj/3600s
		$sj = round(($rs['tgtime'] - $ctime) * 100 / 3600);
		unLockItem($user['mbid']);
		die('����������ɣ���Ҫ����ˮ����'.$sj.'����ȷ��������');
	}else{
		unLockItem($user['mbid']);
		die("3");
	}
}

if($action == 'timesdo'){
	$id = intval($_GET['id']);
	if($id <= 0){
		unLockItem($user['mbid']);
		die('��������');
	}
	foreach($petsAll as $v){
		if($v['id'] == $id){
			$rs = $v;
		}
	}
	$time = time();
	$ctime = $time - $rs['tgstime'];
	if($ctime < 0){
		unLockItem($user['mbid']);
		die('�ȴ��ĳ��ﲻ�ܼ��٣�');
	}else if($ctime < $rs['tgtime']){
		//�۳�ˮ����=��ҽ�ʡʱ�䣨����ҵ���������ʱ��ʣ�й�ʱ�䣬��λΪs��*200sj/3600s
		$sj = round(($rs['tgtime'] - $ctime) * 100 / 3600);
		$_pm['mysql'] -> query("UPDATE player_ext SET sj = sj - $sj WHERE uid = {$_SESSION['id']} and sj >= $sj");
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			unLockItem($mbid);
			die("1");
		}
		$time1 = $rs['tgtime'] - $ctime;
		$_pm['mysql'] -> query("UPDATE userbb SET tgstime = tgstime - $time1 WHERE id = $id and uid = {$_SESSION['id']}");
		unLockItem($mbid);
		die('������ɣ����Ƿ�ȡ�����ĳ��');
	}else{
		unLockItem($user['mbid']);
		die('�й���ɣ�����Ҫ���٣�');
	}
}

//�õ���ҵ�ǰ��ѡ�ĳ����״̬
if($action == "change")
{
	$err = "";
	$id = intval($_REQUEST['id']);
	if($petsid < 0)
	{
		unLockItem($user['mbid']);
		die("10");//��Ϣ����
	}
	foreach($petsAll as $pets)
	{
		if($pets['id'] == $id)
		{
			if($pets['tgflag'] == "0")
			{
				$err = 0;//δ�й�
			}
			else if($pets['tgflag'] == "1")
			{
				$times = time();
				$time = $times - $pets['tgstime'];
				if($time < $pets['tgtime'])
				{
					$err = 1;//�й���
				}
				else
				{
					$err = 2;//�й����
				}
			}
			else if($pets['tgflag'] == "2")
			{
				$time = time();
				if($time < $pets['tgstime'])
				{
					$err = 3;//�ȴ���
				}
				else
				{
					$time = $time - $pets['tgstime'];
					if($time < $pets['tgtime'])
					{
						$err = 1;//�й���
					}
					else
					{
						$err = 2;//�й����
					}
				}
			}	
		}
	}
	echo $err;
}

//�йܳ���
if($action == "tuoguan")
{
	//ʱ������(ֻ����22:00 �� 10��00 �����й�)
	$err = "";
	$times = date("H:i:s");
	$timearr = explode(":",$times);
	if($timearr[0] >= 10 && $timearr[0] < 22)
	{
		unLockItem($user['mbid']);
		die("0");//ֻ��22��00--10��00 �ſ����йܣ�
	}
	$pets = intval($_REQUEST['pets']);
	$time = intval($_REQUEST['time']);
	$mes = intval($_REQUEST['mes']);
	$time1 = $timearr[0] + $time;
	if($time1 >= 24)
	{
		$time1 = $time1 - 24;
	}
	if($time1 >= 10 && $time1 < 22)
	{
		unLockItem($user['mbid']);
		die("7");//�����йܽ���ʱ�䡣������ѡ��ʱ��! 
	}
	if($pets <=0 )
	{
		unLockItem($user['mbid']);
		die("1");//��ѡ��Ҫ�йܳ���
	}
	$i = 0;
	foreach($petsAll as $p)
	{
		if($p['tgflag'] > 0)
		{
			$i++;
		}
	}
	if($i >= 3)
	{
		unLockItem($user['mbid']);
		die("5");//�йܸ����Ѵ�����
	}
	if($i >= 1 && $i < 3 && $i == $user['tgmax'])
	{
		unLockItem($user['mbid']);
		die("6");//�йܸ�����Ŀǰ�����ޣ��������ܹ������й�����������������й�����
	}
	
	foreach($petsAll as $pet)
	{	
		if($pet['id'] == $pets)
		{
			if($pet['level'] < 10){
				unLockItem($user['mbid']);
				die('199');
			}
			if(!empty($pet['tgflag']))
			{
				$now = time();
				$time5 = $now - $pet['tgstime'];
				if($pet['tgstime'] > $now)
				{
					unLockItem($user['mbid']);
					die("8");//�ȴ���
				}
				else
				{
					if($time5 < $pet['tgtime'])
					{
						unLockItem($user['mbid']);
						die("3");//��ҵ�ǰ��ѡ�����Ѿ����йܣ�
					}
					else
					{
						unLockItem($user['mbid']);
						die("4");//��ǰ�����й�����ɣ�����ȡ�����й�!
					}
				}
			}
		}
	}
	if($pets >0 && $time > 0 && !empty($mes))
	{
		//�õ�Ҫ���ĵ��й�ʱ��
		if($mes == "1")
		{
			$times = $time;
		}
		else if($mes == "2")
		{
			$times = 2* $time;
		}
		else if($mes == "3")
		{
			$times = 3*$time;
		}
		$tgtime = $time * 3600;
		//�ж��û��Ƿ����㹻���й�ʱ��
		if($user['tgtime'] < $times)
		{
			unLockItem($user['mbid']);
			die("2");//�й�ʧ�ܣ������й�ʱ�䲻�㣡�����Թ����йܾ�������ʱ�䡣
		}
		//��ȥ��ҵ��й�ʱ��
		$sql = "UPDATE player
				SET tgtime = tgtime - {$times}
				WHERE id = {$_SESSION['id']} AND tgtime >= $times";
		$_pm['mysql'] -> query($sql);
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			unLockItem($user['mbid']);
			die("�й�ʱ�䲻��");
		}
		//������Ҹó����״̬
		$time1 = time();
		$sql = "UPDATE userbb 
				SET tgflag = 1,tgstime = {$time1},tgmes = {$mes},tgtime = {$tgtime}
				WHERE id = {$pets}";
		$_pm['mysql'] -> query($sql);
		$err = 10;
	}
	echo $err;
}

//�жϳ���״̬
if($action == "offpets")
{
	$id = intval($_REQUEST['id']);
	if($id <= 0)
	{
		unLockItem($user['mbid']);
		die("0");//��ѡ����Ҫȡ�صĳ��
	}
	foreach($petsAll as $pets)
	{
		if($pets['id'] == $id)
		{
			if($pets['tgflag'] == 0)
			{
				unLockItem($user['mbid']);
				die("1");//����û�н����κ��йܲ���������ȡ�س��
			}
			else if($pets['tgflag'] == 2)
			{
				$time = time();
				if($pets['tgstime'] > $time )
				{
					unLockItem($user['mbid']);
					die("4");//���ڵȴ��У�ȷ��ȡ����
				}
				else
				{
					$ctime = $time - $pets['tgstime'];
					if($ctime < $pets['tgtime'])
					{
						unLockItem($user['mbid']);
						die("3");//��ǰȡ�س����֮ǰ�����й�ʱ�佫ʧЧ��ȷ��ȡ����
					}
					else
					{
						unLockItem($user['mbid']);
						die("2");//�й�����ɣ�������ȡ�����ĳ����ˣ�
					}
				}
			}
			else if(!empty($pets['tgflag']))
			{
				$time = time();
				$ctime = $time - $pets['tgstime'];
				if($ctime < $pets['tgtime'])
				{
					unLockItem($user['mbid']);
					die("3");//��ǰȡ�س����֮ǰ�����й�ʱ�佫ʧЧ��ȷ��ȡ����
				}
				else
				{
					unLockItem($user['mbid']);
					die("2");//�й�����ɣ�������ȡ�����ĳ����ˣ�
				}
			}
		}
	}
}
//ȡ�س���
if($action == "offpet")
{
	$err = "";
	$id = intval($_REQUEST['id']);
	foreach($petsAll as $p)
	{
		if($p['muchang'] == 1)
		{
			$numarr[] = $p['id'];
		}
	}
	if(count($numarr) >= $user['maxmc'] ) 
	{
		unLockItem($user['mbid']);
		die("13");//���������Ѿ�ռ����
	}
	if($id > 0)
	{
		//�ı�״̬�����Ӹ���ҵĵ�ǰ����������Ϣ
		foreach($petsAll as $pets)
		{
			if($pets['id'] == $id)
			{
				$mes = $pets['tgmes'];
				$stime = $pets['tgstime'];
				$time = $pets['tgtime'];
				$level = $pets['level'];
				$czl = $pets['czl'];
				$srchp = $pets['srchp'];
				$srcmp = $pets['srcmp'];
				$ac = $pets['ac'];
				$mc = $pets['mc'];
				break;
			}
		}
		$nowtime = time();

		$ctime = $nowtime - $stime;
		//��õľ�����=����ȼ�*������ɳ���/40��*5000
		if($time <= 0)
		{
			unLockItem($user['mbid']);
			die("0");
		}
		if($ctime < $time)//ȡ���й�ʱ��δ����
		{
			$time = $ctime;
		}
		if($time < 0){
			$time = 0;
		}
		$num = intval($time / 60 / 5);//����Ĵ���
		if($mes == 1)//��Ϣ
		{
			$exp += $level * ($czl / 40) * 2500 * $num;
		}
		else if($mes == 2)//��������
		{
			$exp += $level * ($czl / 40) * 2500 * $num * 2;
		}
		else if($mes == 3)//ð������
		{
			$exp += $level * ($czl / 40) * 2500 * $num * 2.5;
			for($i = 1;$i <= $num;$i++)
			{
				$props[] = giveprops($level);
			}
		}
		else
		{
			unLockItem($user['mbid']);
			die("0");//�����Ϣ����
		}
		//�ж��û������Ƿ�����
		$bagNum=0;
		$arr = array();
		if(is_array($props))
		{
			foreach($props as  $v)
			{
				if(array_key_exists($v['id'],$arr))
				{
					$arr[$v['id']] += $v['sum'];
				}
				else
				{
					$arr[$v['id']] = $v['sum'];
				}
			}
		}
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
		$bagNum += count($arr);
		if($bagNum > $user['maxbag'])
		{
			unLockItem($user['mbid']);
			die('12');//�����ռ䲻�����������������
		}
		if(is_array($arr))
		{
			foreach($arr as $k => $p)
			{
				foreach($userBag as $ub)
				{
					$ids[] = $ub['pid'];
				}
				if(in_array($k,$ids))
				{
					$sql = "UPDATE userbag SET sums = sums+{$p} WHERE uid = {$_SESSION['id']} and pid = {$k}";
				}
				else
				{
					$sql = "INSERT INTO userbag (pid,sums,uid) VALUES ({$k},{$p},{$_SESSION['id']})";
				}
				$_pm['mysql'] -> query($sql);
			}
		}
		//��������
		$t = new task();
		$a = $t->saveExps($exp,$id);
		//�ı�����״̬
		$sql = "UPDATE userbb
				SET tgflag = 0,tgstime = 0,tgtime = 0,tgmes = 0
				WHERE id = {$id}";
		$_pm['mysql'] -> query($sql);
		//����־ 09 06 24
		$time1 = $time / 3600;
		$rearr = $_pm['mysql'] -> getOneRecord("SELECT level,czl,srchp,srcmp,ac,mc,tgflag,tgstime,tgtime,tgmes FROM userbb WHERE id = {$id}");
		$str = 'id:'.$id.'�þ���:'.$exp.'level:'.$level.'->'.$rearr['level'].'�йܷ�ʽ:'.$mes.'stime:'.date("YmdHi",$stime).'->'.$rearr['tgstime'].'�й�ʱ��:'.$time1.'->'.$rearr['tgtime'].'�ɳ�:'.$czl.'->'.$rearr['czl'].'����:'.$srchp.'->'.$rearr['srchp'].'ħ��:'.$srcmp.'->'.$rearr['srcmp'].'����:'.$ac.'->'.$rearr['ac'].'����:'.$mc.'->'.$rearr['mc'];
		$_pm['mysql'] -> query("INSERT INTO gamelog (ptime,seller,buyer,pnote,vary) VALUES (".time().",{$_SESSION['id']},{$_SESSION['id']},'$str',30)");
		
		
		$err = 10;//ȡ�س���ɹ�
	}
	else
	{
		$err = 11;//ȡ�س���ʧ��
	}
	echo $err;
}


//�鿴����
if($action == "show")
{
	$id = intval($_REQUEST['id']);
	if($id <= 0)
	{
		unLockItem($user['mbid']);
		die("��ѡ��һ������!");//��ѡ��һ����Ҫ�鿴�ĳ��
	}
	foreach($petsAll as $pet)
	{
		if($pet['id'] == $id)
		{
			if(empty($pet['tgflag']))
			{
				$str = "�ó��ﻹû���йܻ����Ѿ�ȡ�أ�";
				echo $str;
				exit;
			}
			$time = time();
			if($time < $pet['tgstime'])
			{
				$str = "��û�е��й�ʱ�䣬�����ܲ鿴��";
				echo $str;
				exit;
			}
			$nowtime = time();
			$ctime = $nowtime - $pet['tgstime'];
			if($ctime > $pet['tgtime'])
			{
				$time = $pet['tgtime'];
			}
			else
			{
				$time = $ctime;
			}
			$num = $time / 60 / 5;
			if($pet['tgmes'] == 1)//��Ϣ
			{
				$exp += $pet['level'] * ($pet['czl'] / 40) * 2500 * $num;
			}
			else if($pet['tgmes'] == 2)//��������
			{
				$exp += $pet['level'] * ($pet['czl'] / 40) * 2500 * $num * 2;
			}
			else if($pet['tgmes'] == 3)//ð������
			{
				$exp += $pet['level'] * ($pet['czl'] / 40) * 2500 * $num * 2.5;
				for($i = 1;$i <= $num;$i++)
				{
					$props[] = giveprops($pet['level']);
				}
			}
			$str = "�йܳ��".$pet['name']."\n";
			$str .= "�й�ǰ����ȼ���".$pet['level']."\n";
			//$str .= "��ǰ����ȼ�";
			$str .= "�й�ʱ�䣺".($pet['tgtime'] / 3600)."Сʱ\n";
			$str .= "��ǰ���й�ʱ�䣺".round($time / 60)."����\n";
			$str .= "�йܻ�þ��飺".round($exp)."\n";
			$str .= "��������Ʒ";
			echo $str;
		}	
	}
}




//�Զ��й�
if($action == "auto")
{
	//ʱ������(ֻ����22:00 �� 10��00 �����й�)
	$err = "";
	$times = date("H:i:s");
	$timearr = explode(":",$times);
	if($timearr[0] >= 10 && $timearr[0] < 22)
	{
		$date = date("Y-m-d");
		$autotime = strtotime($date." 22:00:00");
		//�õ���ʼ�йܵ�ʱ��
	}
	else
	{
		$autotime = time();
	}
	$pets = intval($_REQUEST['pets']);
	$time = intval($_REQUEST['time']);
	$mes = intval($_REQUEST['mes']);
	$time1 = $autotime + $time;
	if($time1 >= 24)
	{
		$time1 = $time1 - 24;
	}
	if($time1 >= 10 && $time1 < 22)
	{
		unLockItem($user['mbid']);
		die("7");//�����йܽ���ʱ�䡣������ѡ��ʱ��!
	}
	if($pets <=0 )
	{
		unLockItem($user['mbid']);
		die("1");//��ѡ��Ҫ�йܳ���
	}/**/
	$i = 0;
	foreach($petsAll as $p)
	{
		if($p['tgflag'] > 0)
		{
			$i++;
		}
	}
	if($i >= 3)
	{
		unLockItem($user['mbid']);
		die("5");//�йܸ����Ѵ�����
	}
	if($i >= 1 && $i < 3 && $i == $user['tgmax'])
	{
		unLockItem($user['mbid']);
		die("6");//�йܸ�����Ŀǰ�����ޣ��������ܹ������й�����������������й�����
	}
	
	foreach($petsAll as $pet)
	{	
		if($pet['id'] == $pets)
		{
			if($pet['level'] < 10){
				unLockItem($user['mbid']);
				die('199');
			}
			if(!empty($pet['tgflag']))
			{
				$time = time() - $pet['tgstime'];
				if($pet['tgstime'] > $now)
				{
					unLockItem($user['mbid']);
					die("8");//�ȴ���
				}
				else
				{
					if($time < $pet['tgtime'])
					{
						unLockItem($user['mbid']);
						die("3");//��ҵ�ǰ��ѡ�����Ѿ����йܣ�
					}
					else
					{
						unLockItem($user['mbid']);
						die("4");//��ǰ�����й�����ɣ�����ȡ�����й�!
					}
				}
			}
		}
	}
	if($pets >0 && $time > 0 && !empty($mes))
	{
		//�õ�Ҫ���ĵ��й�ʱ��
		if($mes == "1")
		{
			$times = $time;
		}
		else if($mes == "2")
		{
			$times = 2* $time;
		}
		else if($mes == "3")
		{
			$times = 3*$time;
		}
		$tgtime = $time * 3600;
		//�ж��û��Ƿ����㹻���й�ʱ��
		if($user['tgtime'] < $times)
		{
			unLockItem($user['mbid']);
			die("2");//�й�ʧ�ܣ������й�ʱ�䲻�㣡�����Թ����йܾ�������ʱ�䡣
		}
		//��ȥ��ҵ��й�ʱ��
		$sql = "UPDATE player
				SET tgtime = tgtime - {$times}
				WHERE id = {$_SESSION['id']} AND tgtime >= $times";
		$_pm['mysql'] -> query($sql);
		
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			unLockItem($mbid);
			die("�й�ʱ�䲻��");
		}
		//������Ҹó����״̬
		$sql = "UPDATE userbb 
				SET tgflag = 2,tgstime = {$autotime},tgmes = {$mes},tgtime = {$tgtime}
				WHERE id = {$pets}";
		$_pm['mysql'] -> query($sql);
		$err = 10;
	}
	echo $err;
}
unLockItem($user['mbid']);
$_pm['mem']->memClose();
unLockItem($user['mbid']);

//���ݳ���ĵȼ����������
//$level ����ĵȼ�

function giveprops($level)
{
	global $tuoguan;
	foreach ($tuoguan as $k => $v)
	{
		$lv = explode("-",$k);
		//���ݳ���ĵȼ��ȵ��ó���ĵ���
		if($level <= $lv[1] && $level >= $lv[0])
		{
			$arr = explode(",",$v);
			break;
		}
	}
	foreach($arr as $arrs)
	{
		$info[] = explode(":",$arrs);
	}
	foreach($info as $infos)
	{
		if(rand(1,$infos[1]) == 1)
		{
			$props['id'] = $infos[0];
			$props['sum'] = $infos[2];
		}
	}
	return $props;
}
?>