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
  	 �ж�װ��ǿ��
*/
require_once('../config/config.game.php');
@session_start();
secStart($_pm['mem']);
//����id
$id = intval($_REQUEST['pid']);
//�õ�Ҫǿ����װ������ߵ������Ϣ
$err = 0;
$rs	= unserialize($_pm['mem']->get('db_propsid'));
$bid = intval($_GET['bid']);
if(!is_array($rs))
{
	die("0");
}
$props = $rs[$id];
if($props['varyname'] != 9 && $props['varyname'] != 11)
{
	die("0");//�õ����ݴ���
}
if($props['varyname'] == 9)
{
	//�жϸ�װ���Ƿ��ǿ��
	if($props['plusflag'] != 1)
	{
		die("1");//��װ������ǿ��
	}
	else
	{
		$ar = $_pm['mysql'] -> getOneRecord("SELECT id FROM userbag WHERE pid = $id AND uid = {$_SESSION['id']} AND zbing != 1 AND id = $bid");
		if(!is_array($ar)){
			die(0);
		}
		$str = '&pid='.$id.'&pids='.$_SESSION['pids'.$_SESSION['id']].'&bid='.$bid;
		echo $str;
		exit;
	}
}else{
	$str = '&pids='.$id.'&pid='.$_SESSION['pid'.$_SESSION['id']].'&bid='.$_SESSION['bid'.$_SESSION['id']];
	echo $str;
	exit;
}
echo $err;
?>