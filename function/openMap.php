<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: ������
*@Note: none
*/

header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');
secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$userBag = $_pm['user']->getUserBagById($_SESSION['id']);
if (!is_array($userBag)) die('��û�д򿪸õ�ͼ��Կ��!');

$n = intval($_REQUEST['open']);
if ($_pm['user']->check(array('int' => $n)) === true )
{
	$item = split(',', $user['openmap']);
	if (in_array($n, $item)) die('�õ�ͼ�Ѿ�����!');
	
	$patter = 'openmap:' . $n;
	$valid = false;
	foreach ($userBag as $k => $v)
	{
		if ($v['effect'] == $patter)
		{
			$pid	= $v['id'];
			$psum = $v['sums'];
			if(empty($psum))
			{
				die("���İ�����û�д򿪸õ�ͼ��Կ�ף�");
			}
			$valid	= true;
			break;
		}
	}

	if ($valid === true)
	{
		// del a props for current map.
		$_pm['mysql']->query("UPDATE userbag SET sums = abs(sums-1)
							   WHERE id={$pid} and uid={$_SESSION['id']} and sums > 0
							 ");
		$user['openmap'] .= ','.$n;

		$_pm['mysql']->query("UPDATE player 
								 SET openmap='{$user['openmap']}' 
								WHERE id={$_SESSION['id']}");

		echo "��ͼ�򿪳ɹ�!";

		//$_pm['user']->updateMemUser($_SESSION['id']);
		//$_pm['user']->updateMemUserbag($_SESSION['id']);
	}
	else echo "��ͼ��ʧ�ܣ���ȷ�ϰ������д򿪸õ�ͼ��Կ��!";				
}
?>