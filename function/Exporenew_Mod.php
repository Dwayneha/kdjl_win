<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.07.13
*@Usage: Map
*@Note: none
*/
require_once('../config/config.game.php');
unset($_SESSION['catch_gw_info']);
$_SESSION['fight'.$_SESSION['id']]['gid'] = 0;
//����ץ���ǲ�Ѫ�����������ң������ʹ��Ŀǰ����ң�2009-01-30���򲻻��������ط���$_SESSION['GoToCity']Ϊһ��ʱ�䣬����ս��ʱ�Ϳ����ж�������ʹ����ң�
$_SESSION['GoToCity'] = NULL;
unset($_SESSION['GoToCity']);
secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);

//������ͼ
$sql = "SELECT * FROM map WHERE gpclist = '0'";
$fuben = $_pm['mysql'] -> getRecords($sql);
//@Load template.
if($_GET['n'] == 2){
	$tn = $_game['template'] . 'tpl_mapnew1.html';
}else{
	$tn = $_game['template'] . 'tpl_mapnew.html';
}
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	if(is_array($fuben))
	{
		foreach($fuben as $ks => $vs)
		{
			$fbid[] = $vs['id'];
		}
	}
	foreach($fbid as $k1 => $v1)
	{
		$src[$v1] = "#{$v1}#";
		$des[$v1] = $v1;
	}
	
	$map = $user['openmap'];

	// Fix maybe error.
	if ($map == '') $map = 1;

	$maparr = split(',', $map);
	foreach($maparr as $k => $v)
	{
		$src[$v] = "#{$v}#";
		$mapsrc[$v] = "#map{$v}#";
		$des[$v] = $v;
		$mapdes[$v] = $v;
	}
	for($x=100;$x<=200;$x++)
	{
		if(isset($src[$x])) continue;
		else{
			$src[$x] = "#{$x}#";
			$des[$x] = 0;
			$mapsrc[$x] = "#map{$x}#";
			$mapdes[$x] = "03";
		}
	}
	$mapret = str_replace($src, $des, $tpl);
	$mapret = str_replace($mapsrc,$mapdes,$mapret);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $mapret;
ob_end_flush();
?>