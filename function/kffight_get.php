<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %xueyuan%

*@Write Date: 2011.08.31
*@Update Date: /
*@Usage: ���ս���콱ҳ��
*�����̨�����ӿ�
*/
ini_set('display_errors',true);
error_reporting(E_ALL);
require_once('../config/config.game.php');
require_once('../login/curl.php');
header('Content-Type:text/html;charset=GBK');
$mem_welcome = unserialize($_pm['mem']->get('db_welcome'));
if(!is_array($mem_welcome))
{
	die("�ڴ����");
}
$user = $_pm['user']->getUserById($_SESSION['id']);
$bag = $_pm['user']->getUserBagById($_SESSION['id']);
$bagNum=0;
if(is_array($bag))
{
	foreach($bag as $x => $y)
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
	die('�������������ո��ӣ�');
}
$interface = "http://pmmg1.webgame.com.cn/interface/kffight_get.php";
$respone = curl_get($interface."?username=".urlencode($_SESSION['nickname'])."&host=".$_SERVER['HTTP_HOST']);
switch($respone)
{
	case 'no_stat' :
	{
		die("�뱾�ξ���֮����ȡ����");
	}
	case 'noopen' :
	{
		die("����ս����δ����");
	}
	case 'nobm' :
	{
		die("���ϴ�û�в���");
	}
	case 'has' :
	{
		die("���Ѿ���ȡ����Ӧ������,��л���Ĳμ�");
	}
	case '5':
	{
		foreach($mem_welcome as $info)
		{
			if( $info['code'] == 'kf_join_prize')
			{		
				$kf_task = new task;
				$kf_task->saveGetProps($info['contents']);
				$prize_name = "���뽱";
				break;
			}
		}
	}
}
foreach($mem_welcome as $info)
{
	if($info['code'] == 'kf_fight_prize_config')
	{
		$ts_arr = explode('|',$info['contents']);
		foreach($ts_arr as $key => $val)
		{
			$prize_arr[$key+1] = explode(',',$val);
		}
	}
}
$respone_info = explode('|',$respone);
switch($respone_info[0])
{
	case 1 :
	{
		$prize_name = '��һ�׶�-';break;
	}
	case 2 :
	{
		$prize_name = '�ڶ��׶�-';break;
	}
	case 3 :
	{
		$prize_name = '�����׶�-';break;
	}
}
switch($respone_info[1])
{
	case 1 :
	{
		$prize_name .= '[�ھ���]';break;
	}
	case 2 :
	{
		$prize_name .= '[�Ǿ���]';break;
	}
	case 3 :
	{
		$prize_name .= '[������]';break;
	}
	case 4 :
	{
		$prize_name .= '[��Ӣ��]';break;
	}
}
$kf_task = new task;
$kf_task->saveGetProps($prize_arr[$respone_info[0]][$respone_info[1]-1]);
die("�콱�ɹ�,����".$prize_name."��Ʒ�Ѿ����Ž����ı���");
?>