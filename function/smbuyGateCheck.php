<?php
/*
*˵�������ϸ����������̣��е���Ҫ��ƽ̨������Ԫ�������е�ֱ������Ϸ�е�����˻��Ͽ۳�Ԫ�����й���
*By Huizheng Yu
*2009-04-17
*/
//header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');
$orderId=substr($_GET['orderId'],0,25);//��Ϸ�������Ѽ�¼ʱ�Ķ�����
$userAccount=substr($_GET['userAccount'],0,25);//�û�ͨ��֤��
$feeMoney=intval($_GET['feeMoney']);//�û����ѽ��
$logDate=$_GET['logDate'];//�û�����ʱ��,��ʽyyyyMMddHHmmss
$sign=$_GET['sign'];//MD5ǩ��
require_once("../login/lib/nusoap.php");
$key="7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM";
$sn= md5($orderId.$userAccount.$feeMoney.$logDate.$key);

if($sn!==$sign)
{
	die('102');
}

$row=$_pm['mysql']->getOneRecord('select pid,pnum,uid,uname,fee,flag from shop_order where order_id="'.$orderId.'" order by id desc limit 1');
if(mysql_error())
{
	die('105');
}

if(!$row)
{
	die('103');
}

if($row['flag']==1)
{
	die('10');
}

if(!$row['fee']!=$feeMoney||$userAccount!=$row['uname'])
{
	die('104');
}

$userid=$row['uid'];
$bid=$row['pid'];
$n=$row['pnum'];


$m	= &$_pm['mem'];
$db = &$_pm['mysql'];
$u	= &$_pm['user'];
$user	= $u->getUserById($userid);
//$bags    = $u->getUserBagById($_SESSION['id']);
$bags    = $u->getUserBagById($userid);

$wp = $_pm['mysql'] -> getOneRecord("SELECT * FROM props WHERE id = $bid and yb > 0");

$now = time();
//$number = $n;
	
$db->query("insert into yblog(title,nickname,yb,buytime,pname,nums)
			values('{$orderId}����ڴ������[7��]����{$wp['name']} {$n} ��.','".$row['uname']."','{$row['fee']}',unix_timestamp(),'{$wp['name']}',{$n})
		  "); // save buy log.

$db->query("update shop_order set flag=1 where order_id='".$orderId."' and uname='".$userAccount."'"); 
		  
######################################���������ӻ��� ̷� 11.10###########################################
//���Ż��֣�����ۼ�����100Ԫ����1�֣�
//��player�����������֣�score���ֶΣ������û�������useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
$useryb = $user['useyb'] + $row['fee'];//�ܵ����ѵ�Ԫ����
$score = intval($useryb / 100);
$useyb = intval($useryb % 100);
#######################################�������������#######################################3

######################################���������ӻ���� ̷� 1.20###########################################
//���Ż��֣�����ۼ�����100Ԫ����1�֣�
//��player�����������֣�score���ֶΣ������û����֣�����useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
$active_useybs = $user['active_useyb'] + $row['fee'];//�ܵ����ѵ�Ԫ����
$active_score = intval($active_useybs / 100);
$active_useyb = intval($active_useybs % 100);
#######################################��������������#######################################3

######################################����������vip ̷� 1.20###########################################
//���Ż��֣�����ۼ�����100Ԫ����1�֣�
//��player�����������֣�score���ֶΣ������û����֣�����useyb�ֶΣ������û�û�л�ȡ���ֵ�Ԫ��
$vipybs = $user['vipyb'] + $row['fee'];//�ܵ����ѵ�Ԫ����
$vip = intval($vipybs / 100);
$vipyb = intval($vipybs % 100);
#######################################��������������#######################################3

$user['yb'] -=$row['fee'];//���ﲻ��ȥƽ̨�����

#########################################################

if ($wp['vary']==2) //���ܵ���
{ 
	for ($i=0; $i<$n; $i++)
	{
		$db->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
					VALUES(
						   {$user['id']},
						   {$bid},
						   {$wp['sell']},
							2,
						   1,
						   unix_timestamp()
						  );
				  ");
	}
}
else
{		
	$arrobj = new arrays();
	$ret = false;
	if(is_array($bags))
	foreach($bags as $k=>$v)
	{
		if($v['uid']==$userid && $v['pid']==$bid) $ret=$v;
	}
	
	if (is_array($ret))
	{

		$db->query("UPDATE userbag
					   SET sums=sums+{$n},stime=".time()."
					 WHERE id={$ret['id']}
				  ");
				  
	}
	else //create new data
	{
		$db->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
					VALUES(
						   {$user['id']},
						   {$bid},
						   {$wp['sell']},
							1,
							{$n},
						   unix_timestamp());
				  ");
				
	}
}

$db->query("update player set yb={$user['yb']},useyb={$useyb},score=score + {$score},vip = vip + {$vip},vipyb = {$vipyb},active_useyb={$active_useyb},active_score=active_score+{$active_score} where id={$userid}");

unset($user,$wp);
$m->memClose();
echo $err;
?>