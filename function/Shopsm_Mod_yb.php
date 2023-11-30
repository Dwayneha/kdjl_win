<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: Shop main ui
*@Note: none
*/
session_start();
require_once('../config/config.game.php');
$m = $_pm['mem'];
$u = $_pm['user'];
secStart($m);

$user		= $u->getUserById($_SESSION['id']);
$userBag	= $u->getUserBagById($_SESSION['id']);
$props = unserialize($m->get(MEM_PROPS_KEY));
$bagtype = $_REQUEST['bagtype'];
$basetype = $_REQUEST['basetype'];

#########################�ֿ����Ʒ 9.18 ̷�###########################3
$strings = ",1,2,3,4,5,6,7,8,9,10|11,12,13,14,15,16";
$strinfo = "ȫ������,��������,�������,��׽����,�ռ�����,������,��Ƭ����,��������,�������,װ������,��������,�������,�������,���ܵ���,������,�ϳɵ���";
$arr = explode(",",$strings);
$arrinfo = explode(",",$strinfo);
//����
foreach($arr as $ks => $v)
{
	if($bagtype == $v)
	{
		$bagoption .= "<option selected=selected value='./Shopsm_Mod.php?bagtype=".$v."&basetype=".$basetype." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./Shopsm_Mod.php?bagtype=".$v."&basetype=".$basetype." '>".$arrinfo[$ks]."</option>";
	}
}
//�����̵�
$basestr = ",15,9,1|2|3|4|5|6|7|8,10|11|12|13|14|16";
$baseinfo = "ȫ������,������,����װ��,������Ʒ,�����챦";
$basearr = explode(",",$basestr);
$basearrinfo = explode(",",$baseinfo);
foreach($basearr as $bk => $bv)
{
	if($basetype == $bv)
	{
		$baseoption .= "<option selected=selected value='./Shopsm_Mod.php?basetype=".$bv."&bagtype=".$bagtype." '>".$basearrinfo[$bk]."</option>";
	}
	else
	{
		$baseoption .= "<option value='./Shopsm_Mod.php?basetype=".$bv."&bagtype=".$bagtype." '>".$basearrinfo[$bk]."</option>";
	}
}
##########################���������###############################

if (!is_array($props)) $shop='��û���κ���Ʒ!';
else
{
	foreach ($props as $k => $rs)
	{
	
	#########################�����̵����Ʒ 9.18 ̷�###########################
		if(!empty($basetype))
		{
			$varyname = explode("|",$basetype); 
			if(!in_array($rs['varyname'],$varyname))
			{
				continue;
			}
		}
	##########################���������###############################
	
		if (intval($rs['yb'])<1) continue;
		if (strlen($rs['requires'])>2) 
		{	
			$t = split(',', 
					   str_replace(array('lv','wx'),array('�ȼ�','����'), $rs['requires'])
					  );
			$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
		}
		else $t[0]= $wx= '��';
		
		$effect = zbAttrib($rs['effect']);
		$effect = $effect=='��'?'':$effect.'<BR/>';
		$plus   = zbAttrib($rs['pluseffect']);
		$plus   = $plus=='��'?'':'<font color=green>'.$plus.'</font><BR/>';
		
		$shop .= '<tr>
              		<td width="40%" id="t'.$rs['id'].'" style="cursor:hand;" onmouseover="showTip('.$rs['id'].');this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['yb'].';">'.$rs['name'].'</td>
              		<td width="30%" >' . $rs['yb']/10 . '</td>
              		<td width="30%" >' . $_props['vary'][$rs['vary']] .'</td>
            	 </tr>';
	}
}

$curBagNum=0;
// Get userbag
if (!is_array($userBag)) $bag='��û���κ���Ʒ!';
else
{
	foreach ($userBag as $k => $rs)
	{
		if ($rs['sums'] < 1 || $rs['id']==0 ||  $rs['zbing'] == 1) continue;
		$curBagNum++;
		#########################��������Ʒ 9.18 ̷�###########################
		if(!empty($bagtype))
		{
			$varyname = explode("|",$bagtype); 
			if(!in_array($rs['varyname'],$varyname))
			{
				continue;
			}
		}
		##########################���������###############################
		if (strlen($rs['requires'])>2) 
		{
			$t = split(',', 
					   str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
					  );
			$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
		}
		else $t[0]= $wx= '��';
		$bag .= '<tr>
              		<td width="40%" id="t'.$rs['id'].'" style="cursor:hand;" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
              		<td width="25%" >' . $rs['sell'] . '</td>
              		<td width="35%" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
	}
}

//Word part.
$taskword= taskcheck($user['task'],3);
$m->memClose();

if(empty($shop))
{
	$shop = "û����Ӧ����Ʒ��";
}
if(empty($bag))
{
	$bag = "���ı�����û����Ӧ����Ʒ��";
}
//@Load template.
$tn = $_game['template'] . 'tpl_smshop.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#money#',
				 '#yb#',
				 '#baglimit#',
				 //right attrib.
				 '#shoplist#',
				 '#mybag#',
				 '#word#',
				 '#bagoption#',
				 '#baseoption#'
				);
	$des = array($user['money'],
				 $user['yb'],
				 $curBagNum.'/'.$user['maxbag'],
				 //right part
				 $shop,
				 $bag,
				 $taskword,
				 $bagoption,
				 $baseoption
				);
	$shop = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();
?>