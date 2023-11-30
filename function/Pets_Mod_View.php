<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.07.14
*@Usage: pets info
*@Note: none
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);
$dbn  = $GLOBALS['_pm']['mysql'];
$sql = sprintf("select id from player where nickname='".$_REQUEST['uid']."' limit 1");
$res  = $dbn->getOneRecord($sql);
$userid=$res[id];
$user		= $_pm['user']->getUserById($userid);
$petsAll	= $_pm['user']->getUserPetById($userid);
$bag		= $_pm['user']->getUserBagById($userid);
$sk			= $_pm['user']->getUserPetSkillById($userid);
$skillsys	= unserialize($_pm['mem']->get(MEM_SKILLSYS_KEY));
if (isset($_REQUEST['pid']) && intval($_REQUEST['pid'])>0)
	 $pid = intval($_REQUEST['pid']);
else $pid=0;
$kk = 0;
$pd = 0;
if(is_array($petsAll))
{
	foreach ($petsAll as $k =>$rs) // Will filter in muchang pets for current user.
	{
		if ($rs['muchang'] == 1) continue;
		if (intval($_REQUEST['pid']) == $rs['id'])
		{
			$selid	= $rs['id'];
			$pd		= $rs;
			$user['mbid']= $rs['id'];
		}
		if(empty($pid))
		{
			if ($kk == 0 ) 
			{
				$sel = 100;
				$selidinit= $rs['id'];
				$pdinit= $rs;
			}
			else $sel = 50;
		}
		else
		{
			if($rs['id'] == $pid)
			{
				$sel = 100;
				$selidinit= $rs['id'];
				$pdinit= $rs;
			}
			else $sel = 50;
		}
		$pets[$kk++] = "<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display1({$rs['id']},this,\"".$_REQUEST['uid']."\");' style='cursor:pointer;filter:alpha(opacity={$sel})' id='i{$kk}'>";
		if ($kk==3) break;
	}
}

// save mbid.
$_pm['mysql']->query("UPDATE player
						 SET mbid={$user['mbid']}
					   WHERE id={$userid}
					");
// refresh cache
//$_pm['user']->updateMemUser($userid);

if(!is_array($pd))
{
	$selid	= $selidinit;
	$pd		= $pdinit;
}

$petszb = array();
if (is_array($bag))
{
	foreach ($bag as $k => $rs)
	{
		if ($rs['varyname'] == 9 && $rs['zbing'] == 1 && $rs['zbpets'] == $pd['id'])
		{
			if ($rs['requires'] != '') 
			{
				$t = split(',', 
					       str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
					      );
				$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
			}
			else $t[0]= $wx= '��';
			
			$zbeffect = zbAttrib($rs['effect']);
			$petszb[$rs['postion']] = '<img src="'.IMAGE_SRC_URL.'/props/'.$rs['img'].'" border=0  onmouseover="showTip('.$rs['id'].','.$pd['id'].',1,2)"  onmouseout="window.parent.UnTip()"  onclick="copyWord(\''.$rs[name].'\');" style="cursor:pointer" />';
		}
	}
}	

for ($i=1; $i<=10; $i++)
{
	if ($petszb[$i] == '') $petszb[$i] = $_props['postion'][$i];
}

// Get jn in here.
if (!is_array($sk)) $jnlist= '������û��ѧϰ���ܣ�';
else
{
	$jnlist='';
	foreach ($sk as $k => $rs)
	{
		if (!is_array($rs) || $rs['bid'] != $selid) continue;
		
		if ($rs['level']==10 || $pd['level']<$rs['level']) $uplevel='';
		else $uplevel='<input type="button" value="����" style="background-image:url('.IMAGE_SRC_URL.'/ui/shop/gm13.gif);border:0px;width:39px;height:15px;color:#2F291D;" onclick="sjJn(\''.$rs['sid'].'\');"/>';

		$jnlist .= '<span onclick="copyWord(\''.$rs[name].'\');"> <b>' .$rs['name']. '</b> </span>'.$rs['level']. ' �� 
		<br/>';
	}
}

// Get sk book in here.
if (!is_array($bag)) $jnbook= '<option value="0">������û�м�����</option>';
else
{
	foreach ($bag as $k => $rs)
	{
		foreach ($skillsys as $x => $y)
		{	
			if ($rs['pid'] == $y['pid'] && $y['wx'] == $pd['wx'])
			{
				$jnbook .= '<option value="'.$y['id'].'">'.$y['name'].'</option>';
			}
		}
	}
}
if ($jnbook == '') $jnbook= '<option value="0">������û�м�����</option>';

if ($pd['kx']=='') $kx= array();
else $kx = explode(",", $pd['kx']);

$att =getzbAttrib($bag, $selid);
$_pm['mem']->memClose();
//@Load template.
$tn = $_game['template'] . 'tpl_bb_view.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	$empty = '<img src="'.IMAGE_SRC_URL.'/ui/muchang/cwzl26.gif" />';
	
	$src = array(
				 '#nickname#',
				 '#bbname#',//add by DuHao
				 '#headimg#',
				 '#vary#',
				 '#sex#',
				 '#pets#',
				 '#success#',
				 '#money#',
				 '#yb#',
				 '#one#',
				 '#two#',
				 '#three#',
				 '#pinfo#',
				 '#pinfos#',
				 '#bigimg#',
				 '#1#',
				 '#2#',
				 '#3#',
				 '#4#',
				 '#5#',
				 '#6#',
				 '#7#',
				 '#8#',
				 '#9#',
				 '#10#',
				 '#jnlist#',
				 '#jk#',
				 '#mk#',
				 '#sk#',
				 '#hk#',
				 '#tk#',
				 '#subyl#',
				 '#subsl#',
				 '#subdl#',
				 '#subxl#',
				 '#subhl#',
				 '#subfl#',
				 '#subkl#',
				 '#remaketimes#',
				 '#pid#',
				 '#jnbook#'
				);
	$des = array(
				 $user['nickname'].'<br/>������<font color=green>'.$pd['name'].'</font>',
				  $pd['name'],//add by DuHao
				 '2'.$user['headimg'],
				 $user['vary'],
				 $user['sex'],
				 count($petsAll),
				 0,
				 $user['money'],
				 $user['yb']?$user['yb']:0,
				 $pets[0],
				 $pets[1]?$pets[1]:$empty,
				 $pets[2]?$pets[2]:$empty,
				 '�ȼ���'.$pd['level'].'<br/>'.
				 '��ǰ���飺'.$pd['nowexp'].'<br />'.
				 '�������飺'.$pd['lexp'].'<br />'.
				 '���У�'.getWx($pd['wx']).'<br/>'.
				 '����: '.($pd['hp']+$att['hp']).'/'.($pd['srchp']+$att['hp']).'<br/>'.
				 'ħ��: '.($pd['mp']+$att['mp']).'/'.($pd['srcmp']+$att['mp']).'<br/>'.
				 '������'.($pd['ac']+$att['ac']).'<br/>'.
				 '������'.($pd['mc']+$att['mc']).'<br/>'.
				 '���У�'.($pd['hits']+$att['hits']).'<br/>'.
				 '���ܣ�'.($pd['miss']+$att['miss']).'<br/>'.
				 '�ٶȣ�'.($pd['speed']+$att['speed']).'<br/>'.
				 '�ɳ���'.$pd['czl'],
				  '�ȼ���'.$pd['level'].'<br/>'.
				 '���У�'.getWx($pd['wx']).'<br/>'.
				 '����: '.($pd['hp']+$att['hp']).'/'.($pd['srchp']+$att['hp']).'<br/>'.
				 'ħ��: '.($pd['mp']+$att['mp']).'/'.($pd['srcmp']+$att['mp']).'<br/>'.
				 '������'.($pd['ac']+$att['ac']).'<br/>'.
				 '������'.($pd['mc']+$att['mc']).'<br/>'.
				 '���У�'.($pd['hits']+$att['hits']).'<br/>'.
				 '���ܣ�'.($pd['miss']+$att['miss']).'<br/>'.
				 '�ٶȣ�'.($pd['speed']+$att['speed']).'<br/>'.
				 '�ɳ���'.$pd['czl'],
				 $pd['imgstand'],
				 $petszb[1],
				 $petszb[2],
				 $petszb[3],
				 $petszb[4],
				 $petszb[5],
				 $petszb[6],
				 $petszb[7],
				 $petszb[8],
				 $petszb[9],
				 $petszb[10],
				 $jnlist,
				 $kx[0],
				 $kx[1],
				 $kx[2],
				 $kx[3],
				 $kx[4],
				 $pd['subyl'],
				 $pd['subsl'],
				 $pd['subdl'],
				 $pd['subxl'],
				 $pd['subhl'],
				 $pd['subfl'],
				 $pd['subkl'],
				 $pd['remaketimes'],
				 $pd['id'],
				 $jnbook
				);
	$bbatib = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $bbatib;
ob_end_flush();
?>