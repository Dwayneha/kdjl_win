<?php
ini_set('display_errors',true);
error_reporting(E_ALL);
require_once('../config/config.game.php');
require_once('../sec/dblock_fun.php');

require_once('config_prize.php');


 $str = '&roulette=error&rouletteStr=Ħ�������𻵣�ת�����ˣ�����������ҵ��ɣ�';
 echo iconv('gbk','utf-8',$str);
 die();


$a = getLock($_SESSION['id']);
if(!is_array($a))
{
	realseLock();
	unLockItem($id);
	die('��������æ�����Ժ����ԣ�');
}


$res = $_pm['mysql'] -> getOneRecord(" SELECT yb from player WHERE id = '".$_SESSION['id']."' LIMIT 1" );
if(!is_array($res) || $res['yb'] < 10 )
{
	$str = '&roulette=error&rouletteStr=����Ԫ������(һ����Ҫ����10Ԫ��)';
	echo iconv('gbk','utf-8',$str);
	unLockItem($id);
	die();
}
//��鱳���ռ�
$user = $_pm['user']->getUserById($_SESSION['id']);
$bags = $_pm['user']->getUserBagById($_SESSION['id']);
$bagNum=0;
if(is_array($bags))
{
	foreach($bags as $x => $y)
	{
		if($y['sums']>0 and $y['zbing'] == 0) 
		{
			$bagNum++;		
		}
	}
}
$snum = $user['maxbag'] - $bagNum;
if($snum < 3)
{
	unLockItem($id);
	$str = '&roulette=error&rouletteStr=���ı����ռ䲻��,��������3��λ��';
	echo iconv('gbk','utf-8',$str);
	die();
}
if($bagNum >= $user['maxbag'])
{
	unLockItem($id);
	$str = '&roulette=error&rouletteStr=���ı����ռ䲻��';
	echo iconv('gbk','utf-8',$str);
	die();
}

$sql = "UPDATE player SET yb = yb - 10 WHERE id = {$_SESSION['id']} AND yb >= 10";
$_pm['mysql']->query($sql);

//$prize_id = -2;
//���㽱Ʒ
$luck_num = rand(1,20000);
$db_welcome = unserialize($_pm['mem']->get('db_welcome'));
foreach($db_welcome as $key => $val)
{
	if($val['code'] == 'zp_prize_info')
	{
		$infomation = $val['contents'];
		break;
	}
}
$arr = explode(',',$infomation);
foreach($arr as $info)
{
	$mid = explode(':',$info);
	$size = explode('-',$mid[1]);
	if($luck_num >= $size[0] && $luck_num <= $size[1])
	{
		$prize_id = $mid[0];
		$result = $prize_info[$mid[0]];
		break;
	}
}
//����
$task = new task();
$task->saveGetPropsMore($prize_id_config[$prize_id],1);

$vip = $res['yb']-10;
$str = "��ϲ�����:".$result." ��1(��ʣԪ��:".$vip.")";
$str = "&roulette=".$prize_id."&rouletteStr=".$str;
echo iconv('gbk','utf-8',$str);
//��־
$log = 'ʹ��10Ԫ������Ħ���֣��õ�:'.$result."(��ʣԪ��:".$vip.")";
$_pm['mysql'] -> query("INSERT INTO gamelog (seller,buyer,ptime,pnote,vary) VALUES({$_SESSION['id']},{$_SESSION['id']},".time().",'$log',236)");
$_SESSION['need_gonggao'] = 1;
realseLock();
unLockItem($id);
?>
