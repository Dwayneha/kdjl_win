<?php 
/**
@Usage:��ñ���ս��ҵ���Ϣ
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user = $_pm['user']->getUserById($_SESSION['id']);
$uname	  = @mysql_real_escape_string($_REQUEST['u']);
$rs = $_pm['mysql']->getOneRecord("SELECT mbid
					    FROM player
					   WHERE nickname='{$uname}'
					   LIMIT 0,1
					");
$bb = $_pm['mysql']->getOneRecord("SELECT level
					    FROM userbb
					   WHERE id={$rs['mbid']}
					   LIMIT 0,1
					");


$_pm['mem']->memClose();

if (is_array($rs))
{
	if ($bb['level']<20) echo 1;
	else {
		$sql = " SELECT id FROM player WHERE nickname = '".$uname."'";
		$uid_info = $_pm['mysql']->getOneRecord($sql);
		$ext = $_pm['mysql']->getOneRecord("SELECT tiaozhan
					    FROM player_ext
					   WHERE uid=".$uid_info['id']);
		if($ext['tiaozhan']==0)	//0Ϊ������
		{
			echo '2';
		}
		else
		{
			echo $rs['mbid'];
		}
	}
}
else echo 0;
//####################
?>