<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.10.09
*@Update Date: 2008.10.09
*@Usage: �ֿ����
*@Note: none
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
$user	 = $_pm['user']->getUserById($_SESSION['id']);
$action = $_REQUEST['action'];
//���ֿ����
if($action == "reg")
{
	$pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
	$repwd = htmlspecialchars(mysql_escape_string($_REQUEST['repwd']));
	$err = "";
	if(!empty($user['ckpwd']) && empty($_SESSION['login'.$_SESSION['id']]))
	{
		die("3");//���Ĳֿ��Ѽ��ܣ���Ҫ�޸ģ�������������룡
	}
	if(empty($pwd))
	{
		die("0");//�����������룡
	}
	if(strlen($pwd) <= 3 || strlen($pwd) > 10)
	{
		die("4");//���볤�Ȳ�һ�£�
	}
	if(empty($repwd))
	{
		die("1");//���������ظ����룡
	}
	if($pwd != $repwd)
	{
		die("2");//�������벻һ�£�
	}
	$err = "10";
	echo $err;
}
//�������룬���µ����ݿ�
else if($action == "do")
{
	if($_SESSION['login'.$_SESSION['id']] != 1)
	{
		$r = $_pm['mysql']->getOneRecord("SELECT ckpwd FROM player WHERE id = {$_SESSION['id']}");
		if(isset($r['ckpwd']) && !empty($r['ckpwd']))
		{
			die("���ȵ�¼");
		}
	}
	$err = "";
	$pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
	$pwd = abs(crc32(md5($pwd)));
	if(empty($pwd))
	{
		die("0");//��Ϣ����
	}
	$sql = "UPDATE player 
			SET ckpwd = $pwd
			WHERE id = {$_SESSION['id']}";
	$_pm['mysql'] -> query($sql);
	$err = 10;
	echo $err;
}
//��½
else if($action == "login")
{
	$err ="";
	if(empty($user['ckpwd']))
	{
		die("2");//����û�����òֿ����룡
	}
	$pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
	if(empty($pwd))
	{
		die("0");//���������룡
	}
	$pwd = abs(crc32(md5($pwd)));
	if($pwd != $user['ckpwd'])
	{
		die("1");//�������
	}
	else
	{
		$err = 10;
		$_SESSION['login'.$_SESSION['id']] = "1";//�Ѿ���½
	}
	echo $err;
}

$_pm['mem']->memClose();
?>