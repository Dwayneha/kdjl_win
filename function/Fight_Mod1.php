<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.29
*@Usage:Fightting Display
*@Note: none
Mem style.
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
define(MEM_BOSS_KEY, $_SESSION['id'] . 'boss');
define(MEM_FIGHT_KEY, $_SESSION['id'] . 'fight'); // 保存战斗信息。
//加速外挂
$time = time();
$sql = "SELECT time FROM fight_log WHERE uid = {$_SESSION['id']} and vary = 2";
$timearr = $_pm['mysql'] -> getOneRecord($sql);
if(is_array($timearr)){
	$ctime = $time - $timearr['time'];
	if($ctime < 1){
		$_SESSION['id'] = '';
		die('操作过快！');
	}else{
		$_pm['mysql'] -> query("UPDATE fight_log SET time = ".time()." WHERE uid = {$_SESSION['id']} and vary = 2");
	}
}else{
	$_pm['mysql'] -> query("INSERT INTO fight_log (uid,time,vary) VALUES({$_SESSION['id']},".time().",2)");
}

//在这里结束
require_once(dirname(__FILE__).'/multi_Monster.php');
$multiMonster->freshup();//清除多怪

$user	= $_pm['user']->getUserById($_SESSION['id']);

$mmgw = $multiMonster->getMultiMonster($user['inmap']);

$userbb = $_pm['user']->getUserPetById($_SESSION['id']);
$bag    = $_pm['user']->getUserBagById($_SESSION['id']);
$fight	=	$_SESSION['fight'.$_SESSION['id']];
$_SESSION['fttime'.$_SESSION['id']] = 10;
$expflag = 0;
$time = time();
$usermap = explode(",",$user['mapinfo']);
foreach($usermap as $v)
{
	$mapinfo = explode(":",$v);
	$time = time();
	if($mapinfo[0] == $user['inmap'] && $mapinfo[1] > $time)
	{
		$mapflag = 1;//地图已经打开
		break;
	}
}
$openmap = explode(",",$user['openmap']);
if($mapflag != 1 && !in_array($user['inmap'],$openmap) && $user['inmap'] < 15)
{
	die("地图开放时间到期，或者地图未开启！");
}
if (!in_array($user['inmap'],$_game['map'])) // 地图限制
{
	/*
	$_pm['mysql']->query("UPDATE player 
							 SET secid=2
						   WHERE id={$_SESSION['id']}");
					*/
	unset($_SESSION['id']);
	$_pm['mem']->memClose();
	echo '<center>您的帐号非法操作，服务器强制断线！</center>';
	exit();
}
//#########################
if (is_array($fight))
{
	   // Check time 
	   $will = (10-time()+$fight['ftime']);
	   if (
	   		$fight['ftime']+10>=time() 
			&& 
		    ($mmgw===false||!is_array($mmgw)||empty($mmgw)||$multiMonster->isFirstMultiMonster)
	   ){


	   	$end='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
</head>
<!--[if IE 6]><script type="text/javascript">try{ document.execCommand("BackgroundImageCache", false, true); } catch(e) {}
</script>
<![endif]-->
<body style="background-color: #FFFCEB;margin-top:0px;">
<center>
  <div style="margin-top:140px;"><img src="http://gimages.xjwa.net/poke/images/ui/fight/loading.gif"/><div id="timev"  style="position:absolute; text-align:center; color:#F98F2C; font-weight:bold;font-size:2em;left: 390px; top: 160px; height: 40px;"></div>
</div>
</center>
</body>
</html>
<script language="javascript">
var readH;
var pt=0;
function loadtime(m){
	if(m<1  && pt==0) 
	{	
		window.clearTimeout(readH);
		window.setTimeout("pause("+m+")",1000);
		return;
	}
	else{
		document.getElementById("timev").innerHTML = m--;
		readH=window.setTimeout("loadtime("+m+");", 1000);
	}
}
function pause(m)
{   if (pt==1) return;
	if(m == 0){
		window.parent.document.getElementById("gw").src="./function/Fight_Mod.php?p='.$_REQUEST['p'].'&s=t";
	}
	pt=1;
 }
loadtime('.$will.');
</script>';
			ob_start('ob_gzip');
			echo $end;
			ob_end_flush();
			exit();
		}
}
//########################


// Get bb info.
$bid = intval($_REQUEST['p']);
$arrobj = new arrays();

$bb = $arrobj->dataGet(array('k' => MEM_BB_KEY, 
					  		 'v' => "if(\$rs['id'] == '{$bid}' && \$rs['uid'] == '{$_SESSION['id']}') \$ret=\$rs;"
					        ),
							$userbb
					  );
if (!is_array($bb))
{
	if (!empty($fight))
	{
		$bid = $_SESSION['fight'.$_SESSION['id']]['bid'];
	}
	else $bid = $user['mbid'];
	$bb = $arrobj->dataGet(array('k' => MEM_BB_KEY, 
					  			 'v' => "if(\$rs['id'] == '{$bid}' && \$rs['uid'] == '{$_SESSION['id']}') \$ret=\$rs;"
								),
							$userbb
					     );
}
if (!is_array($bb))
	{
		die('不能获得宠物数据！');
	}
else
{
	// ============================== 装备效果开始 ==========================================
	//宠物的血量和魔法的最大值的计算（加上装备的效果）；
	$arr = getzbAttrib($bid);
	$bb['srchp'] += $arr['hp'];
	$bb['srcmp'] += $arr['mp'];
	$bb['hp'] += $arr['hp'];
	$bb['mp'] += $arr['mp'];
	/*$sql = "SELECT addmp,addhp FROM userbb WHERE uid = {$_SESSION['id']} and id = {$bid}";
	$add = $_pm['mysql'] -> getOneRecord($sql);
	$bb['srchp'] += $add['addhp'];
	$bb['srcmp'] += $add['addmp'];*/
   // ================================ 装备效果结束 ========================================

	//if ($bb['hp'] <= 0) err($_bbword[rand(0,count($_bbword)-1)]);

	
	
		//金钱版
	if($_SESSION['exptype'.$_SESSION['id']] == 1)
	{
		if((empty($_SESSION['way'.$_SESSION['id']]) || $_SESSION['way'.$_SESSION['id']] == "money") && $user['autofitflag']==1 && $user['sysautosum']>0)
		{
			$_SESSION['fttime'.$_SESSION['id']] = 4;
			if(!empty($arr['hp']) && !empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp/2,addhp={$arr['hp']},addmp={$arr['mp']}/2
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			else if(!empty($arr['hp']) && empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp/2,addhp={$arr['hp']}
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			else if(empty($arr['hp']) && !empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp/2,addmp={$arr['mp']}/2
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			else
			{
				$_pm['mysql'] -> query("UPDATE userbb
					  	 SET hp=srchp,mp=srcmp/2
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			$bb['hp'] = $bb['srchp']; //保证显示是正确的，因为$bb['hp']这个为0，但是已经被修改为最大值了 
			
		}
		//元宝版
		else if($_SESSION['way'.$_SESSION['id']] == "yb" && $user['autofitflag']==1 && $user['maxautofitsum']>0)
		{
			$_SESSION['fttime'.$_SESSION['id']] = 3;
			if(!empty($arr['hp']) && !empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp,addhp={$arr['hp']},addmp={$arr['mp']}
						 WHERE id={$bid} and uid={$_SESSION['id']}");				
			}
			else if(!empty($arr['hp']) && empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp,addhp={$arr['hp']}
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			else if(empty($arr['hp']) && !empty($arr['mp']))
			{
				$_pm['mysql'] -> query("UPDATE userbb
						   SET hp=srchp,mp=srcmp,addmp={$arr['mp']}
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			else
			{
				$_pm['mysql'] -> query("UPDATE userbb
					  	 SET hp=srchp,mp=srcmp
						 WHERE id={$bid} and uid={$_SESSION['id']}");
			}
			$bb['hp'] = $bb['srchp']; //保证显示是正确的，因为$bb['hp']这个为0，但是已经被修改为最大值了 
		}
	}
	else 
	{
		if(!empty($arr['hp']) && !empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
						  SET addhp={$arr['hp']},addmp={$arr['mp']}
						WHERE id={$bid} and uid={$_SESSION['id']}
					 ");
		}
		else if(!empty($arr['hp']) && empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET addhp={$arr['hp']}
					 WHERE id={$bid} and uid={$_SESSION['id']}
				  ");
		}
		else if(empty($arr['hp']) && !empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET addmp={$arr['mp']}
					 WHERE id={$bid} and uid={$_SESSION['id']}
					 ");
		}
	}

	// By field order.
	$bb['wx'] = getWx($bb['wx']);
	$bbinfo = "['{$bb['name']}',{$bb['level']},'{$bb['wx']}',{$bb['ac']},{$bb['mc']},{$bb['hp']},{$bb['mp']},'{$bb['skillist']}','{$bb['imgstand']}','{$bb['imgack']}','{$bb['imgdie']}',{$bid},'{$bb['srchp']}','{$bb['srcmp']}','{$bb['nowexp']}','{$bb['lexp']}']";
}

// Get detail jn info.
$tjn = split(",", $bb['skillist']);
foreach($tjn as $mkey => $n)
{
	$tt = split(":", $n);
	$jlist .= $tt[0] . ",";
}
$jlist =	substr($jlist, 0, -1);
$bjn   =	$_pm['user']->getUserPetSkillById($_SESSION['id']);

if (!is_array($bjn))
{
	for($i = 1;$i++;$i<5){
		$bjn   =	$_pm['user']->getUserPetSkillById($_SESSION['id']);
		if(!is_array($bjn)){
			$bjn   =	$_pm['user']->getUserPetSkillById($_SESSION['id']);
		}else{
			break;
		}
	}
	//Header("Location:Fight_Mod.php?p={$bid}");exit();
}
if (!is_array($bjn)){
	header("refresh:1;url=Fight_Mod.php?p={$bid}");
	exit;
}

$jlistarr = split(',', $jlist);
foreach($bjn as $k => $rs)
{
	if (in_array($rs['sid'], $jlistarr) &&
		$rs['bid'] == $bid && $rs['vary'] != 4
	   )
	{
		if ($rs['value']!='')
		{
			if(strstr($rs['value'],":"))
			{
				$ak = split(":", $rs['value']);
				$rs['value']=$ak[count($ak)-1];
			}
		}
		else $rs['value']=0;
		
		 $rs['value'] = str_replace("%","0",$rs['value']);
		$jnlist .="['{$rs[name]}',{$rs[level]},'{$rs[vary]}',{$rs[wx]},'{$rs[value]}','{$rs[plus]}','{$rs['img']}',{$rs[uhp]},{$rs[ump]},{$rs['sid']}],";
	}
}
$jnlist = substr($jnlist, 0, -1); // []#[];

// from current map choose level limit.
$memmapid = unserialize($_pm['mem']->get('db_mapid'));
$levels = $memmapid[$user['inmap']];
/*$levels = $_pm['mem']->dataGet(array('k' => MEM_MAP_KEY, 
 	  						'v' => "if(\$rs['id'] == '{$user['inmap']}') \$ret=\$rs;"
					));*/

/**###################################
*Level limit lock
###################################*/
if (!is_array($levels) || $levels['level']<1 )
{
	$levels['level']="1,15";
}
$lvl = explode(',', $levels['level']);

$idse = rand($lvl[0], $lvl[1]); // <<<<<<<


/*$gw = $_pm['mem']->dataGetAll(array('k' => MEM_GPC_KEY, 
						   'v' => "if(\$rs['level'] == '{$idse}') \$ret=\$rs;"
					));
*/
if($_GET['flag'] == 'boss'){
	 $gw = $_pm['mysql'] -> getRecords("SELECT * FROM gpc WHERE level = $idse AND boss != 4 AND boss != 3 LIMIT 1");
}else{
	$gw = $_pm['mysql'] -> getRecords("SELECT * FROM gpc WHERE level = $idse AND boss != 4 LIMIT 1");
}

if (count($gw)<1 || $gw['boss'] == 4)
{
	header("refresh:1;url=Fight_Mod.php?p={$bid}");
	exit;
}
else
{	
	
	if (count($gw)==1&&($mmgw===false||!is_array($mmgw)||empty($mmgw))) $gw = $gw[0];
	else
	{
		if($mmgw!==false&&is_array($mmgw)&&!empty($mmgw)){
			$mmonsterStr = '<div id="mmonster" style="position:absolute; left:435px; top:35px; width: 1850px; padding:0px;over-flow:hidden"> <table width="180" border="0">
  <tr>
    <td width="180" align="center" bgcolor="#999900" style="color:#FFFFFF;cursor:pointer;font-size:12px" onclick="if(document.getElementById(\'showmmonsterlist\').style.display==\'none\'){document.getElementById(\'showmmonsterlist\').style.display=\'block\'}else{document.getElementById(\'showmmonsterlist\').style.display=\'none\'}">怪物列表</td>
  </tr>
  <tr id="showmmonsterlist" style="display:none; font-size:12px"  bgcolor="#FFFFFF">
    <td width="180" align="center">
    '.$multiMonster->listStr.'
    </td>
  </tr>
</table> </div>';
			$gw = $mmgw;			
		}else{
			$min	= $gw[0];
			$n		= rand(1, count($gw));
			$gw		= $gw[$n-1];
		}
	}
	echo $gw['name'];exit;
		/*加入遇BOSS的时间限制。*/	
		
	//if ($gw['boss'] == 3 && bossCheck($gw) === false)
	//{
		while($gw['boss'] == 3 && bossCheck($gw) === false){
			$idse = rand($lvl[0], $lvl[1]);
			$gw = $_pm['mysql'] -> getRecords("SELECT * FROM gpc WHERE level = $idse AND boss != 4 AND boss != 3 LIMIT 1");
			$gw = $gw[0];
		}
		//header("refresh:1;url=Fight_Mod.php?p={$bid}&flag=boss");
	//}
	if($user['inmap'] == 2){
		$gpcstr1 = unserialize($_pm['mem'] -> get('gpc_bbAll'));
		$_pm['mem']->set(array('k' => 'gpc_bbAll', 'v' => $gpcstr1.','.$gw['id']));
	}
	
	if($gw['id'] == '71' || $gw['id'] == '72' || $gw['id'] == '37' || $gw['id'] == '39'){
		$gpcstr = unserialize($_pm['mem'] -> get('gpc_bb'));
		$_pm['mem']->set(array('k' => 'gpc_bb', 'v' => $gpcstr.','.$gw['id']));
	}
	$gw['wx'] = getWx($gw['wx']);
$_SESSION['gwcdie'.$_SESSION['id']] = $gw['id'];
	$gwinfo="['{$gw['name']}',{$gw['level']},'{$gw['wx']}',{$gw['ac']},{$gw['mc']},{$gw['hp']},{$gw['mp']},'{$gw['skill']}','{$gw['imgstand']}','{$gw['imgack']}','{$gw['imgdie']}',{$gw['id']}]";
	
	$test = $_SESSION['fight'.$_SESSION['id']];
	//Update fightting stats.
	if (!is_array($test))
	{		
		$_SESSION["fight".$_SESSION['id']]	= array('uid'=>$_SESSION['id'],
						'bid'=>$bid,
						'gid'=>$gw['id'],
						'hp' =>$gw['hp'],
						'mp' =>$gw['mp'],
						'fuzu'=>0,
						'fatting'=>1,
						'boss'=>$gw['boss'],
						'ftime'=>time());
	}
	else
	{
	   // Check time 
	   $will = (10-time()+$fight['ftime']);
	   if ($fight['ftime']+10 >= time()	   
		   && 
		   ($mmgw===false||!is_array($mmgw)||empty($mmgw)||$multiMonster->isFirstMultiMonster)
	   ) {
	   	$end='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
</head>
<!--[if IE 6]><script type="text/javascript">try{ document.execCommand("BackgroundImageCache", false, true); } catch(e) {}
</script>
<![endif]-->
<body style="background-color: #FFFCEB;margin-top:0px;">
<center>
  <div style="margin-top:140px;"><img src="http://gimages.xjwa.net/poke/images/ui/fight/loading.gif"/><div id="timev"  style="position:absolute; text-align:center; color:#F98F2C; font-weight:bold;font-size:2em;left: 390px; top: 160px; height: 40px;"></div>
</div>
</center>
</body>
</html>
<script language="javascript">
var readH;
var pt=0;
function loadtime(m){
	if(m<1  && pt==0) 
	{	
		window.clearTimeout(readH);
		window.setTimeout("pause("+m+")",1000);
		return;
	}
	else{
		document.getElementById("timev").innerHTML = m--;
		readH=window.setTimeout("loadtime("+m+");", 1000);
	}
}
function pause(m)
{   if (pt==1) return;
	if(m == 0){
		window.parent.document.getElementById("gw").src="./function/Fight_Mod.php?p='.$_REQUEST['p'].'&s=t";	
	}
	pt=1;
 }
loadtime('.$will.');
</script>';
			ob_start('ob_gzip');
			echo $end;
			ob_end_flush();
			exit();
		}
		
		$r['bid']		=$bid;
		$r['gid']		=$gw['id'];
		$r['hp']		=$gw['hp'];
		$r['mp']		=$gw['mp'];
		$r['fatting']=1;
		$r['ftime']	=time();
		$r['fuzu']	=0;
		$r['boss']	=$gw['boss'];
		//$fight=$r;
		$_SESSION["fight".$_SESSION['id']]=$r;
	}
}
//$_SESSION["fight".$_SESSION['id']]=$fight;
$bbfzp = "";
$catcharr = "";

// Get bag props.
if (is_array($bag))
{  
	foreach ($bag as $k => $v)
	{
		if ($v['varyname'] == 1 && $v['sums']>0)
		{
			if (empty($bbfzp)) $bbfzp = "['".$v['name']."',".$v['sums'].','.$v['id']."]";
			else $bbfzp .= ",['".$v['name']."',".$v['sums'].','.$v['id']."]";
		}
		else if ($v['varyname'] == 3 && $v['sums']>0)
		{
			if (empty($catcharr)) $catcharr = "['".$v['name']."',".$v['sums'].','.$v['id']."]";
			else $catcharr .= ",['".$v['name']."',".$v['sums'].','.$v['id']."]";
		}
	}
	
}else $bbfzp='0';
//
$user['fightbb'] = $bid;
$_pm['mysql']->query("UPDATE player 
			   SET fightbb={$bid}
			 WHERE id={$_SESSION['id']}
		  ");
//update fight status to memory.
//$_pm['mem']->set(array('k' =>MEM_USER_KEY, 'v' => $user));
//$_pm['mem']->set(array('k' =>MEM_USERBB_KEY, 'v' => $userbb));
//$_pm['mem']->set(array('k' =>MEM_USERBAG_KEY, 'v' => $bag));
$_pm['mem']->memClose();

//###########################
// @Load template.
//###########################

$fn='tpl_fight.html';
$tn = $_game['template'] . $fn;
if (file_exists($tn))
{
	$tpl = file_get_contents($tn);
	
	//#test
	if (WG_CHECK == 1) 
	{
		$mouse = '<script language="javascript">
function mouseCoords(ev)
{
 if(ev.pageX || ev.pageY){
   return {x:ev.pageX, y:ev.pageY};
 }
 return {
     x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
     y:ev.clientY + document.body.scrollTop     - document.body.clientTop
 };
}

function mouseMove(ev)
{
 	ev= ev || window.event;
  	var mousePos = mouseCoords(ev);
    //alert(mousePos.x);
    //alert(mousePos.y);
	var opt = {
    		 method: \'get\',
    		 onSuccess: function(t){
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request(\'../function/exit1c.php?ssid=\'+mousePos.x+mousePos.y, opt);
}
document.onmousemove = mouseMove;
if(window.parent.autoack==true)
{
	/***/
		var opt = {
    		 method: \'get\',
    		 onSuccess: function(t){
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}
	var ajax=new Ajax.Request(\'../function/exit1.php?ssid=\'+window.parent.waittime, opt);
		/***/
}
</script>';
	}
	else $mouse = '';
	$_SESSION['fttime'.$_SESSION['id']] -= $arr['time']; 
	if($_SESSION['fttime'.$_SESSION['id']] < 0)
	{
		$_SESSION['fttime'.$_SESSION['id']] = 0;
	}
	$src = array(
					 "#bbinfo#",
					 "#gwinfo#",
					 "#bbjn#",
					 "#mapcj#",
					 "#petsid#",
					 "#nickname#",
					 "#head0#",
					 "#bbfzp#",
					 "#catcharr#",
					 "#inmap#",
					 "#test#",
					 "#fttime#",
					 "#mmonster#"
					);
		$des = array(
					 $bbinfo,
					 $gwinfo,
					 $jnlist,
					 rand(1,3),
					 $bid,
					 $_SESSION['nickname'],
					 $bb['headimg'],
					 $bbfzp,
					 $catcharr,
					 $user['inmap'],
					 $mouse,
					 $_SESSION['fttime'.$_SESSION['id']],
					 $mmonsterStr
				);

	$fat = str_replace($src, $des, $tpl);
}



// gzip echo. if maybe.
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Content-Type:text/html;charset=GBK');
flush();
ob_start('ob_gzip');
echo $fat;
ob_end_flush();

function err($str)
{
	die('<center>
			<div style="margin-top:100px;padding:5px;font-size:12px; line-height:1.7;width:99%;height:100px;overflow:hidden;">'. $str .'<br/>
				<<<a href="javascript:history.go(-1);">返回村庄</a>
			</div>
		</center>');
		
}

/**
* @Usage:验证BOSS怪物是否有效
* @Param: $gs => array.
* @Return: true false
* @Memo:
   boss_refresh
*/
function bossCheck($gs)
{
	global $_pm;
	$log='';
	if (!is_array($gs) || $gs['boss']!=3) return false;

	$exists = $_pm['mysql']->getOneRecord("SELECT id,rtime,gid,glock,dtime
										     FROM boss_refresh
											WHERE gid={$gs['id']}
											LIMIT 0,1
										 ");
	
	$_pm['mysql']->query("SET autocommit=0");
	$_pm['mysql']->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
	$_pm['mysql']->query("START TRANSACTION");
	if (is_array($exists))
	{
		if (($exists['dtime']+1*3600)>=time() || 
			 ($exists['glock']==1 && ($exists['rtime']+120)>time())
		   ) return false;
		else if( ($exists['dtime']+1*3600)<time() && $exists['glock']==0)
		{
			$_pm['mysql']->query("UPDATE boss_refresh
								     SET rtime=".time().",fightuid={$_SESSION['id']},glock=1
								   WHERE gid={$gs['id']} and (dtime+3600)<".time()."
								");
			$log.="UPDATE boss_refresh
								     SET rtime=".time().",fightuid={$_SESSION['id']},glock=1
								   WHERE gid={$gs['id']} and (dtime+3600)<".time()."
								";
		}
	    else if($exists['glock']==1 && ($exists['rtime']+600)<time())
	    {
			$_pm['mysql']->query("UPDATE boss_refresh
									SET rtime=".time().",fightuid={$_SESSION['id']},glock=1
								  WHERE gid={$gs['id']} and glock=1 and (rtime+600)<".time()."
								");
			$log.="UPDATE boss_refresh
									SET rtime=".time().",fightuid={$_SESSION['id']},glock=1
								  WHERE gid={$gs['id']} and glock=1 and (rtime+600)<".time()."
								";
	    }
		else return false;
		$trs = $_pm['mysql']->getOneRecord("SELECT id
											 FROM boss_refresh
											WHERE gid={$gs['id']} and fightuid={$_SESSION['id']}
											LIMIT 0,1
										 ");
		if (!is_array($trs)) return false;
	}
	else // CREATE boss refresh record log.
	{
		$_pm['mysql']->query("INSERT INTO boss_refresh(gid,rtime,fightuid,glock)
							  VALUES({$gs['id']},".time().",{$_SESSION['id']},1)
							");
	}
	if (!$_pm['mysql']->query("COMMIT")){
			$_pm['mysql']->query("ROLLBACK");
			return false;
		}
    $log = addslashes($log);
	$task = new task();
	$task->saveGword("遇上了沉睡中的[".$gs['name']."]，勇士请赶快去消灭它吧！");
	$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary) VALUES(unix_timestamp(),{$_SESSION['id']},{$_SESSION['id']},'{$log}',3)");
	return true;
}

/*function getgpc($level1,$level2){
	global $_pm;
	$memgpc = unserialize($_pm['mem'] -> get('db_gpcid'));
	if(!is_array($memgpc)){
		$memgpc = unserialize($_pm['mem'] -> get('db_gpcid'));
	}
	if(!is_array($memgpc)){
		return false;
	}
	foreach($memgpc as $k => $v){
		if($v['boss'] == 4 || $v['level'] < $level1 || $v['level'] > $level2 ){
			continue;
		}
		$gpc[$k] = $v;
	}
	if(!is_array($gpc)){
		return false;
	}
}*/
?>
