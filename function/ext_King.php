<?php 
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=GBK');

$user		= $_pm['user']->getUserById($_SESSION['id']);

$op = $_REQUEST['op'];
if ($op == 'getauto')
{
	if ($user['sysautotime']==0 || $user['sysautotime']<mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time())))
	{
	 	$user['sysautosum']	+=	800;
		$user['sysautotime']=	time();
		$_pm['mysql']->query("UPDATE player
							     SET sysautosum={$user['sysautosum']},
									 sysautotime={$user['sysautotime']}
							   WHERE id={$_SESSION['id']}
						");
		echo "��ϲ������ȡ�Զ�ս��Ѱ�ֽ����ɹ�!";
		//$u->updateMemUser($_SESSION['id']);
	}
	else echo "�������Ѿ���ȡ���ˣ�ÿ��ֻ��һ���ޣ�<br/>����������һ�������һῼ���ر�����";	
}
$_pm['mem']->memClose();
//####################
?>