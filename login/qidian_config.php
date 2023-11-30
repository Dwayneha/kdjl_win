<?php
$www=explode('.',$_SERVER['HTTP_HOST']);
$website='';
for($i=1;$i<count($www);$i++)
{
	$website.=$www[$i].'.';
}
switch ($website)
{
	case 'game.qidian.com.':
	setcook(2,1);
	break;
}

function setcook($userareaid, $userserverid)
 {
 	setcookie("IBW_AreaId", $userareaid,0);
	setcookie("IBW_ServerId", $userserverid,0);
	
}
?>