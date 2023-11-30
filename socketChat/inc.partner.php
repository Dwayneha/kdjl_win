<?php
$ptAndId=array(
'.kdjl.youxi.tongxue.com'=>1,
'.qq496.cn'=>2,
'.webgame.com.cn'=>3,
'.pg88.cn'=>3,
'.pomoho.com'=>5,
'.game2.com.cn'=>6,
'.youxi567.com'=>7,
'.molidao.com'=>9,
'.ucbox.com'=>10,
'.nadaobao.com'=>11,
'.yx121.com'=>12,
'.kd.myyouxi.com'=>13,
'.game.17k.com'=>14,
'.g.pplive.com'=>15,
'.koudai.56.com'=>16,
'.game5.cn'=>18,
'.game.com.cn'=>19,
'.4yt.net'=>20,
'.pm.youwo.com'=>21,
'.urgamer.com'=>22,
'.kdjl2.com'=>23,
'.wan8wan.com'=>24,
'.jiujiuyou.com'=>25,
'.kd.9917.com'=>26,
'.kdjl.kugou.com'=>27,
'.youxi63.com'=>28,
'.jingling.kuwo.cn'=>29,
'.kd.weelaa.com'=>30,
'.uc55.cn'=>31,
'.scol.com.cn'=>32,
'.76.217.56'=>33,
'.i3.com.cn'=>34,
'.9ishua.com'=>35,
'.365webgame.com'=>36,
'.kdjl.37wan.com'=>37,
'.73.178.153'=>38,
'.xs8.cn'=>39,
'.titan24.com'=>40,
'.008wan.com'=>41,
'.webgame.comicyu.com'=>42,
'.360quan.com'=>43,
'.game.tom.com'=>44
);



function getMemcacheSetting()
{
	$data = file_get_contents(dirname(dirname(__FILE__)).'/config/config.game.php');	
	preg_match("/_mem\[['\"]host['\"]\][^'\"]+['\"]([^\"']+)['\"]/",$data,$out);
	preg_match("/_mem\[['\"]port['\"]\][^'\"]+['\"]([^\"']+)['\"]/",$data,$out1);
	return array('host'=>$out[1],'port'=>$out1[1]);
}

function sstr($str)
{
	$ends = array('.com.cn','.cn','.com','.net','.net.cn');
	$strs = str_replace($ends,'',strtolower($str));
	$strs = explode('.',$strs);
	return $strs[0].$strs[count($strs)-1];
}

function getSocketPort($str)
{	
	global $ax,$az;
	$maxNum = 45100;
	$rtn = abs(crc32(sstr($str)));
	$x = intval($rtn/$maxNum);
	
	while($maxNum>10000&&($x<10000||$x>50000))
	{
		$maxNum -= 7000;
		$x 		 = intval($rtn/$maxNum);
	}	

	if($x/63>$maxNum)
	{
		$x=$x/63;
	}
	if($x/33>$maxNum)
	{
		$x=$x/33;
	}
	if($x/13>$maxNum)
	{
		$x=$x/13;
	}
	if($x/7>$maxNum)
	{
		$x=$x/7;
	}
	if($x/3>$maxNum)
	{
		$x=$x/3;
	}

	while($x>50000)
	{
		$x=$x/1.26;
	}
	$rtn      = floor($x);
	if($rtn<10000)
	{
		$rtn = substr('10000',0,5-strlen($rtn)).$rtn;
	}	
	
	return $rtn;
}