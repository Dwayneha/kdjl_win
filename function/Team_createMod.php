<?php

/**

*@Usage: ����ϵͳ����ģ��

*/


require_once('../config/config.game.php');

//define(TEAM_MSG_KEY,	'team_msg' . crc32(session_id()));

secStart($_pm['mem']);



$user		= $_pm['user']->getUserById($_SESSION['id']);

$op = intval($_REQUEST['op']);

switch($op)

{

	case 1: vMember();break;

	case 2: LMember();break;

	

}



// �뿪����

function LMember()

{

	global $user;

	$team = new team();

	$team->outTeam($user['id']);

	die('10');

}



// ������ӡ�

function vMember()

{

	global $_pm, $user;

	$team = new team();



	if (strlen(trim($_REQUEST['u'])) < 3 && $_REQUEST['u']!='GM') return false;

	$userName = @mysql_real_escape_string($_REQUEST['u']);

	

	if ($user['openteam']!=0 && $user['openteam']!=999999999) {die('�����Ƕӳ�������������ң�');return false;}



	$rs = $_pm['mysql']->getOneRecord("SELECT id,lastvtime,online,openteam

										 FROM player 

										WHERE nickname='{$userName}'");

	if (!is_array($rs)) die('��Ҳ����ڣ�');



	if ($rs['openteam'] != 0) die("��� {$userName} �Ѿ��ж��飡");

	else if ($rs['lastvtime']+300 < time()) die("��� {$userName} ��ǰ�����ߣ�");

	else

	{

		// д��������ӱ�ǡ�

		$key = crc32($userName);

		$key = $key<1?1-$key-1:$key;

		$_pm['mem']->set(array('k' =>$key ,'v'=> array($_SESSION['nickname'],$userName)));



		$team->addTeamMember($user['id'], $rs['id']);	//	����������Ϣ��

		// ���¶ӳ���Ϣ��

		$_pm['mysql']->query("UPDATE player

								 SET openteam=999999999 

							   WHERE id={$user['id']}

							 ");

		// ���³�Ա��Ϣ��

		$_pm['mysql']->query("UPDATE player

								 SET openteam={$user['id']} 

							   WHERE id={$rs['id']}");

		

		die('1');

	}

}

?>