<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.08.29
*@Update Date: 2008.08.29
*@Usage: ս��ս���ű�
*@Note: none
@Param: 
	>> ����ս���ʱ�����ơ�
*/
session_start();
require_once('../config/config.game.php');
/*if (!defined('BATTLE_TIME_START'))
	define(BATTLE_TIME_START, "20:00");
if (!defined('BATTLE_TIME_END'))
	define(BATTLE_TIME_END, "22:00");
if (!defined('BATTLE_TIME_WEEK'))
	define(BATTLE_TIME_WEEK, 5);*/
//error_reporting(E_ALL&~E_NOTICE);
secStart($_pm['mem']);
$user	= $_pm['user']->getUserById($_SESSION['id']);
//�������
$time = time();
$sql = "SELECT time FROM fight_log WHERE uid = {$_SESSION['id']} and vary = 2";
$timearr = $_pm['mysql'] -> getOneRecord($sql);
if(is_array($timearr)){
	$ctime = $time - $timearr['time'];
	if($ctime < 1){
		$_SESSION['id'] = '';
		die('�������죡');
	}else{
		$_pm['mysql'] -> query("UPDATE fight_log SET time = ".time()." WHERE uid = {$_SESSION['id']} and vary = 2");
	}
}else{
	$_pm['mysql'] -> query("INSERT INTO fight_log (uid,time,vary) VALUES({$_SESSION['id']},".time().",2)");
}
//���������

// ս������ʱ�俪�ء�
$week=date("N", time());
$hourM=date("H:i", time());

$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));

foreach($battletimearr as $bv)
{
	if($bv['titles'] != "battle")
	{
		continue;
	}
	if($week == $bv['days'] && $hourM >= $bv['starttime'] && $hourM <= $bv['endtime'])
	{
		$checkstr = 1;
	}
}
if(empty($checkstr))
{
	die('<center><span style="font-size:12px;">ս��δ������</span></center>');
}

/*if ($week != BATTLE_TIME_WEEK && ($hourM < BATTLE_TIME_START || $hourM > BATTLE_TIME_END) )
{
	die('<center><span style="font-size:12px;">ս��δ������</span></center>'); // record log in here.
}
else if($week == BATTLE_TIME_WEEK && $hourM < BATTLE_TIME_START )
{
	die('<center><span style="font-size:12px;">ս��δ������</span></center>'); // record log in here.
}
else if($week == BATTLE_TIME_WEEK && $hourM > BATTLE_TIME_END )
{
	die("<script>window.parent.Alert('ս���ѽ���,��ӭ���´β���ս�����');window.parent.document.getElementById('gw').src='function/BattleInfo_Mod.php';</script>");
}*/

// ===========ս��������鿪ʼ============
$ends = $_pm['mysql']->getOneRecord("SELECT hp,id
									  FROM battlefield
									 WHERE hp=0
									 LIMIT 0,1
								   ");
if (is_array($ends))
{
	die("<script>window.parent.Alert('ս���ѽ���,��ӭ���´β���ս�����');window.parent.document.getElementById('gw').src='function/BattleInfo_Mod.php';</script>");
}
// ===========ս������������==========

// ս���ȼ����
// �������Լ�����Ӫ���õ��Է���Ӫ

/*$cUser = $_pm['mysql']->getOneRecord("SELECT pos,bid,levels
										FROM battlefield_user
									   WHERE uid={$_SESSION['id']}
									");
									
									
$battleinfo = $_pm['mysql']->getOneRecord("SELECT level_get
                                             FROM battlefield
											WHERE id={$cUser['pos']}
										 ");
//$_REQUEST['bcode']
//10-29:10:1|0:1,30-59:20:1|0:1,60-99:30:2|0:1,100-149:40:2|0:1,150-199:50:3|0:1,200-499:60:3|0:1
$c = explode($_REQUEST['bcode'].':',$battleinfo['level_get']);
$d = explode(',',$c[1]);
$e = explode('|',$d[0]);
$onepart = explode(':',$e[0]);
$twopart = explode(':',$e[1]);
$_pm['mysql']->query("UPDATE battlefield_user
											 SET lastvtime=unix_timestamp(),
												 addjgvalue={$onepart[0]},
												 ackvalue={$onepart[1]},
												 failjgvalue={$twopart[0]},
												 failackvalue={$twopart[1]},
												 bid={$user['mbid']},
												 levels='{$_REQUEST['bcode']}'
										   WHERE uid={$_SESSION['id']}
										 ");*/

$cUser = $_pm['mysql']->getOneRecord("SELECT pos,bid,levels
										FROM battlefield_user
									   WHERE uid={$_SESSION['id']}
									");

$cUser1 = $_pm['mysql']->getOneRecord("SELECT czl
										FROM userbb
									   WHERE id={$user['mbid']}
									");
$czlarr = explode('-',$_REQUEST['bcode']);//echo $cUser1['czl'].'<hr />';print_r($czlarr);exit;
if($cUser1['czl'] < $czlarr[0] || $cUser1['czl'] > $czlarr[1]){
	//die("<center><span style='font-size:12px;'>���ĳɳ�����".$_REQUEST['bcode']."�䣬���ܽ������ս��! <span onclick=\"window.parent.$('gw').src='/function/BattleInfo_Mod.php';\" style='cursor:pointer;'><b><<������Ӫ</b></span></span></center>");
	die('<script language="javascript">window.parent.Alert("���ĳɳ�����'.$_REQUEST['bcode'].'�䣬���ܽ������ս��!");window.parent.$("gw").src="/function/BattleInfo_Mod.php"</script>');
}


/*if ($_REQUEST['bcode']!=$cUser['czl'])
{
	die("<center><span style='font-size:12px;'>���ĳɳ����ڴ����䣬���ܽ������ս��! <span onclick=\"window.parent.$('gw').src='/function/BattleInfo_Mod.php';\" style='cursor:pointer;'><b><<������Ӫ</b></span></span></center>");
}*///



$userbb = $_pm['user']->getUserPetById($_SESSION['id']);
$fight	= $_SESSION['fight'.$_SESSION['id']];


// �����ѱ���ŵ���ң�ֱ��������
if ($user['secid']>0) // ��ͼ����
{
	unset($_SESSION['id']);
	$_pm['mem']->memClose();
	echo '<center>�����ʺŷǷ��������ѱ����ᣡ</center>';
	exit();
}
$_pm['mysql']->query('update player set inmap=0 where id='.$_SESSION['id']);

//########################################################
if (is_array($fight))
{
	   // Check time 
	   $will = (10-time()+$fight['ftime']);
	   $will = 10;
	   if ($fight['ftime']+10>=time()) {
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
  <div style="margin-top:140px;"><img src="../images/ui/fight/loading.gif"/><div id="timev"  style="position:absolute; text-align:center; color:#F98F2C; font-weight:bold;font-size:2em;left: 390px; top: 160px; height: 40px;"></div>
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
		window.setTimeout("pause()",100);
		return;
	}
	else{
		document.getElementById("timev").innerHTML = m--;
		readH=window.setTimeout("loadtime("+m+");", 1000);
	}
}
function pause()
{   if (pt==1) return;
	window.parent.document.getElementById("gw").src="./function/BattleFight_Mod.php?bcode='.$_REQUEST['bcode'].'&s=t";		   
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
$bid=$cUser['bid'];
$arrobj = new arrays();
$bb = $arrobj->dataGet(array('k' => MEM_BB_KEY, 
					  		 'v' => "if(\$rs['id'] == '{$cUser['bid']}' && \$rs['uid'] == '{$_SESSION['id']}') \$ret=\$rs;"
					        ),
							$userbb
					  );
					 
if (!is_array($bb))
{
	$bb = $arrobj->dataGet(array('k' => MEM_BB_KEY, 
					  			 'v' => "if(\$rs['id'] == '{$user['mbid']}' && \$rs['uid'] == '{$_SESSION['id']}') \$ret=\$rs;"
								),
							$userbb
					     );
	if (!is_array($bb))
	{
		die('���ܻ�ó������ݣ�');
	}
}
else
{
	// ============================== װ��Ч����ʼ ==========================================
	//�����Ѫ����ħ�������ֵ�ļ��㣨����װ����Ч������
	$arr = getzbAttrib($bid);
	$bb['srchp'] += $arr['hp'];
	$bb['srcmp'] += $arr['mp'];
	$bb['hp'] += $arr['hp'];
	$bb['mp'] += $arr['mp'];
   // ================================ װ��Ч������ ========================================
	//if ($user['autofitflag']==1 && ($user['maxautofitsum']>0 || $user['sysautosum']>0))
	//{
		if(!empty($arr['hp']) && !empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET hp=srchp,mp=srcmp,addhp={$arr['hp']},addmp={$arr['mp']}
					 WHERE id={$bid} and uid={$_SESSION['id']}
				  ");
		}
		else if(!empty($arr['hp']) && empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET hp=srchp,mp=srcmp,addhp={$arr['hp']}
					 WHERE id={$bid} and uid={$_SESSION['id']}
				  ");
		}
		else if(empty($arr['hp']) && !empty($arr['mp']))
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET hp=srchp,mp=srcmp,addmp={$arr['mp']}
					 WHERE id={$bid} and uid={$_SESSION['id']}
				  ");
		}
		else
		{
			$_pm['mysql']->query("UPDATE userbb
					   SET hp=srchp,mp=srcmp
					 WHERE id={$bid} and uid={$_SESSION['id']}
				  ");
		}
	//}
	/*else
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
	}*/

	// By field order.
	$bb['wx'] = getWx($bb['wx']);
	if($bb['hp'] == 0)
	{
		$bb['hp'] = $bb['srchp'];
	}
	$bbinfo = "['{$bb['name']}',{$bb['level']},'{$bb['wx']}',{$bb['ac']},{$bb['mc']},{$bb['hp']},{$bb['mp']},'{$bb['skillist']}','{$bb['imgstand']}','{$bb['imgack']}','{$bb['imgdie']}',{$bid},'{$bb['srchp']}','{$bb['srcmp']}','{$bb['nowexp']}','{$bb['lexp']}']";
}

// ��ü�����ϸ��Ϣ
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
	Header("Location:BattleFight_Mod.php?bcode=".$_REQUEST['bcodel']);exit();
}

$jlistarr = split(',', $jlist);
foreach($bjn as $k => $rs)
{
	if (in_array($rs['sid'], $jlistarr) &&
		$rs['bid'] == $bid
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

// ��������ս����ID��
$allUser = $_pm['mysql']->getRecords("SELECT uid,bid 
										FROM battlefield_user 
									   WHERE levels='{$cUser['levels']}' and pos!={$cUser['pos']} and lastvtime>unix_timestamp('".date("Y-m-d",time())."')");
if(!is_array($allUser) || empty($allUser)) {
	//die("<center><span style='font-size:12px;'>û�����κεо��� <span onclick=\"window.parent.$('gw').src='/function/BattleInfo_Mod.php';\" style='cursor:pointer;'><b><<������Ӫ</b></span></span></center>");
	die('<script language="javascript">window.parent.Alert("û�����κεо���");window.parent.$("gw").src="/function/BattleInfo_Mod.php";</script>');
}

$rid = rand(1, count($allUser))-1;
if (array_key_exists($rid, $allUser))
	$buserarr = $allUser[$rid];
else {Header("Location:BattleFight_Mod.php?bcode={$_REQUEST['bcode']}");exit();}

// ��ȡ����ս��ҵĳ�����Ϣ��
$gw	= $_pm['mysql']->getOneRecord("SELECT *
									FROM userbb
								   WHERE id={$buserarr['bid']}
								"); 
if (!is_array($gw))
{
	die('����');
}

$gw['wx'] = getWx($gw['wx']);
//����0Ѫ�����
if(empty($gw['hp']))
{
	$gw['hp'] = $gw['srchp'];
}
$gwinfo="['{$gw['name']}',{$gw['level']},'{$gw['wx']}',{$gw['ac']},{$gw['mc']},{$gw['srchp']},{$gw['mp']},'{$gw['skill']}','{$gw['imgstand']}','{$gw['imgack']}','{$gw['imgdie']}',{$gw['id']}]";

$test = $_SESSION['fight'.$_SESSION['id']];
//Update fightting stats.
if (!is_array($test))
{		
	$_SESSION["fight".$_SESSION['id']]	= array('uid'=>$_SESSION['id'],
					'bid'=>$bid,
					'gid'=>$gw['id'],
					'hp' =>$gw['srchp'],
					'mp' =>$gw['srcmp'],
					'fuzu'=>0,
					'fatting'=>1,
					'boss'=>0,
					'ftime'=>time());
}
else{
	 // Check time 
	   $will = (10-time()+$fight['ftime']);
	   $will = 10;
	   if ($fight['ftime']+10 >= time()) {
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
  <div style="margin-top:140px;"><img src="../images/ui/fight/loading.gif"/><div id="timev"  style="position:absolute; text-align:center; color:#F98F2C; font-weight:bold;font-size:2em;left: 390px; top: 160px; height: 40px;"></div>
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
		window.setTimeout("pause()",100);
		return;
	}
	else{
		document.getElementById("timev").innerHTML = m--;
		readH=window.setTimeout("loadtime("+m+");", 1000);
	}
}
function pause()
{   if (pt==1) return;
	window.parent.document.getElementById("gw").src="./function/Fight_Mod.php?p='.$_REQUEST['p'].'&s=t";		   
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
	$r['hp']		=$gw['srchp'];
	$r['mp']		=$gw['srcmp'];
	$r['fatting']=1;
	$r['ftime']	=time();
	$r['fuzu']	=0;
	$r['boss']	=0;
	//$fight=$r;
	$_SESSION["fight".$_SESSION['id']]=$r;
}

$bbfzp = "";
$catcharr = "";

$bbfzp='0';
$_pm['mem']->memClose();

//###########################
// @Load template.
//###########################

$fn='tpl_battle_fight.html';
$tn = $_game['template'] . $fn;

if (file_exists($tn))
{
	$tpl = file_get_contents($tn);

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
					 "#fuser#"
					);
		$des = array(
					 $bbinfo,
					 $gwinfo,
					 $jnlist,
					 rand(1,3),
					 $cUser['levels'],
					 $_SESSION['nickname'],
					 $bb['headimg'],
					 $bbfzp,
					 $catcharr,
					 $user['inmap'],
					 $mouse,
			         $gw['username']
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
?>
