<?php 
/*
 * ���ļ���20081217ֻ��������memcache���Ѿ��޸�Ϊֱ���޸�������������memcache
 */


//if ( !isset($_SESSION['id']) || intval($_SESSION['id']) < 0 ) exit();

require_once('../config/config.game.php');

//if($_SESSION['username']=="leinchu"){
	$wgUser = unserialize($_pm['mem']->get("wgUser"));
	$wgUserList = $wgUser['wgList'];	
if(!empty($wgUserList))
	foreach($wgUserList as $rs){
		if(isset($rs['visitorder']))
		{
			echo $rs['visitorder'][0].'->'.$rs['visitorder'][2]."<br/>";
		}
		if(isset($rs['mustvisit']))
		{
			echo '<u>'.$rs['mustvisit'][0]."</u><br/>";
		}
	}
//}

?>