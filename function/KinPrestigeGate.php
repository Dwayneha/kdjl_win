<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: ̷�

*@Write Date: 2008.11.24
*@Update Date: 
*@Usage: ����ϵͳ��
*@Memo: ����ϵͳ��

*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
$user = $_pm['user']->getUserById($_SESSION['id']);
$num = intval($_REQUEST['num']);
if(lockItem($_SESSION['id']) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}

$err = 0;
if($num <= 0)
{
	unLockItem($_SESSION['id']);
	die("1");//������д��Ҫ���ɵ�������
}
if($user['prestige'] < $num)
{
	unLockItem($_SESSION['id']);
	die("2");//����ǰ����������
}
$sql = "UPDATE player SET prestige = prestige - $num,jprestige = jprestige + $num WHERE id = {$_SESSION['id']} and prestige >= $num";
$_pm['mysql'] -> query($sql);
$err = 10;
echo $err;
$_pm['mem']->memClose();
unLockItem($_SESSION['id']);
?>