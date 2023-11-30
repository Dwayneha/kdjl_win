<?php
//合作商提供等级，成长前50名排行榜
header('Content-Type:text/html;charset=GB2312');
require_once(dirname(dirname(dirname(__FILE__))).'/config/config.game.php');

//时间验证
$h = date('h');
if($h != 5){
	die('error');
}

//参数验证
if (empty($_GET['flag']) || empty($_GET['time'])) {
	die('error');
}

if(time() > $_GET['time']){
	die('error');
}
//安全验证
$www=explode('.',$_SERVER['HTTP_HOST']);
$website='';
for($i=1;$i<count($www);$i++)
{
	$website.=$www[$i].'.';
}
$urlJump = "/";
switch ($website)
{
	case 'webgame.com.cn.':
		if(!preg_match('/pm51\d/is',$_SERVER['HTTP_HOST']))
		{
			$key = '7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM';
		}
		else
		{
			$key = '34l5rdo!@#$casdf45';
		}
		break;
	case 'youjia.cn.':
		$key = '4399_Pm_N924caST45qZRWKL';
		break;
	case 'qq496.cn.';
		$key = '4399_Pm_N924caST45qZRWKL';
		break;
	case 'game2.com.cn.':
		$key = 'Ar.RTr:vU6M.WsdP';
		break;
	case 'molidao.com.':
		$key = 'ks98dADd';
		break;
	case 'sgyouxi.com.':
		$key = 'JllSh91GkTS1QU54tk65tgsHVzYT7520';
		break;
	case 'nadaobao.com.':
		$key = 'try2u&kaixinplay';
		break;
	case 'ucbox.com.':
		$key = 'kdjl224hx';
		break;
	case 'kd.myyouxi.com.':
		$key = 'youxi1208!WERWSvs';
		break;
	case '7happygo.com.':
		$key = '996f9b00209e9c4e9f15e998b2c74f6b';
		break;
	case 'yx121.com.':
		$key = '!#$%%$234yx121';
		break;
	case 'game.17k.com.':
		$key = '748554f4f2705eeb863122799927438f';
		break;
	case 'kdjl.youxi.tongxue.com.':
		$key = 'tyu^by*$h#jx';
		break;
	case 'game5.cn.':
		$key = 'Game5ScsaKD';
		break;
	case 'pomoho.com.':
		$key = 'aksdkak23232sadk23232LjFEERJH';
		break;
	case 'koudai.56.com.':
		$key = 'Odcb01zlE1IT5QDi0W8ii2ZkKJFEWPy';
		break;
	case 'pm.youwo.com.':
		$key = '98f92a8f812011e62ea04364c35c2167';
		break;
	case '4yt.net.':
		$key = '4yt_kdjl2009';
		break;
	case 'urgamer.com.':
		$key = 'dcf500390aa64093283b8e31f2482e03';
		break;
	case 'game.com.cn.':
		$key = 'akW2b9rd4Z6vhQGdr8mWe@0k';
		break;
	case 'g.pplive.com.':
		$key = 'pplive^%$^webgame';
		break;
	case 'kd.9917.com.':
		$key = '72^jq&fKc425d1%$k';
		break;
	case 'wan8wan.com.':
		$key = 'wan8wan(*!^@webgame';
		break;
	case 'kdjl2.com.':
		$key = 'xingkong**^#!webgame';
		break;
	case 'jiujiuyou.com.':
		$key = 'KJr4PlGy2rAipLun';
		break;
	case 'kdjl.kugou.com.':
		$key = 'baaea574d98f4e1a8c8613efd46ea386';
		break;
	case 'youxi63.com.':
		$key = 'kd1UysadaKasI1uYMg0oPA';
		break;
	case 'jingling.kuwo.cn.':
		$key = 'kuwojinglingl2321';
		break;
	case 'scol.com.cn.':
		$key = '%$#@*asfav&a52￥%';
		break;
	case 'dole8.com.'://纳奇
		$key = 'ALD@#@AO@)!)DSKD';
		break;
	case 'uc55.cn.'://唐人游
		$key = 'tangren2009';
		break;
	case 'kdjl.37wan.com.'://37wan 
		$key = '!@)(kdjl9002tvM~;;ybC';
		break;
	case 'kd.weelaa.com.':
		$key = 'P$#eN!l@';
		break;
	case 'i3.com.cn.':
		$key = '328bba915afd4992908df22a84e02cd4';
		break;
	case '365webgame.com.':
		$key = '%sadf%kckg*)1278';
		break;
	case 'anyibao.com.':
		$key = 'a54hj4554dgdY$TE%Yhk%&';
		break;
	case 'youxi567.com.':
		$key = 'c94df431e5a785b3a38cdf190df0200c';
		break;
	case '91k6.com.':
		$key = 'webgame&^%!!^kdjl';
		break;
	case 'xs8.cn.':
		$key = 'ReYzKT8Q2zCFNqJK';
		break;
	case '9ishua.com.':
		$key = 'oaioyv53kljd356dsa';
		break;
}

if (empty($key)) {
	die('error2');
}

$str = $_GET['time'].$key;
$chekflag = md5($str);
if ($chekflag != $_GET['flag']) {
	die('error3');
}

$type = $_GET['type'];

if (empty($type)) {//等级和成长排行都传
	$sql = 'SELECT player.name,player.nickname,userbb.level,userbb.name as bbname FROM player,userbb WHERE player.id = userbb.uid ORDER BY userbb.level desc limit 50';
	$levelarr = $_pm['mysql'] -> getRecords($sql);
	$sql = 'SELECT player.name,player.nickname,userbb.czl,userbb.name as bbname FROM player,userbb WHERE player.id = userbb.uid ORDER BY userbb.czl+0 desc limit 50';
	$czlarr = $arr = $_pm['mysql'] -> getRecords($sql);
}elseif ($type == 'level'){
	$sql = 'SELECT player.name,player.nickname,userbb.level,userbb.name as bbname FROM player,userbb WHERE player.id = userbb.uid ORDER BY userbb.level desc limit 50';
	$levelarr = $_pm['mysql'] -> getRecords($sql);
}else if ($type == 'czl') {
	$sql = 'SELECT player.name,player.nickname,userbb.czl,userbb.name as bbname FROM player,userbb WHERE player.id = userbb.uid ORDER BY userbb.czl+0 desc limit 50';
	$czlarr = $arr = $_pm['mysql'] -> getRecords($sql);
}elseif ($type == 'login'){//一天内的登陆数
	$yes=date("d",time()-3600*24);
	//$sql="date_format("%d",from_unixtime(regtime))='".'';
	$sql = "SELECT player.name,player.nickname,userbb.level,userbb.czl,userbb.name as bbname FROM player,userbb WHERE player.id = userbb.uid and date_format(from_unixtime(lastvtime),'%d') = $yes";
	$loginarr = $_pm['mysql'] -> getRecords($sql);
}else{
	$sql = "SELECT player.name,player.nickname,userbb.level,userbb.czl,userbb.name as bbname FROM player,userbb WHERE player.mbid = userbb.id and player.name = '$type'";
	$single = $_pm['mysql'] -> getOneRecord($sql);
}

if (is_array($levelarr)) {
	foreach ($levelarr as $kk => $lv){
		if (is_array($lv)) {
			foreach ($lv as $k => $lvv){
				$newlevelarr[$kk][$k]=iconv('gbk','utf-8',$lvv);
			}
		}
	}
	echo 'level:'.json_encode($newlevelarr);
}

if (is_array($czlarr)) {
	foreach ($czlarr as $kk => $lv){
		if (is_array($lv)) {
			foreach ($lv as $k => $lvv){
				$newczlarr[$kk][$k]=iconv('gbk','utf-8',$lvv);
			}
		}
	}
	echo 'czl:'.json_encode($newczlarr);
}

if (is_array($loginarr)) {
	foreach ($loginarr as $k => $v){
		if (is_array($v)) {
			foreach ($v as $kk => $vv){
				$newloginarr[$k][$kk] = iconv('gbk','utf-8',$vv);
			}
		}
	}
	echo 'login:'.json_encode($newloginarr);
}

if (is_array($single)) {
	foreach ($single as $k => $v){
		if (!empty($v)) {
			$newsingle[$k] = iconv('gbk','utf-8',$v);
		}
	}
	echo 'single:'.json_encode($newsingle);
}
?>