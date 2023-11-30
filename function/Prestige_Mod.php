<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.07.14
*@Usage: Shop main ui
*@Note: none
*/
require_once('../config/config.game.php');
$m = $_pm['mem'];
$u = $_pm['user'];
secStart($m);

$user		= $u->getUserById($_SESSION['id']);
$props		= unserialize($m->get(MEM_PROPS_KEY));
$userBag	= $u->getUserBagById($_SESSION['id']);
$type = $_REQUEST['type'];
$bagtype = $_REQUEST['bagtype'];
if (!is_array($props)) $preshop='��û���κ���Ʒ!';
else
{
	foreach ($props as $k => $rs)
	{
		if ($rs['prestige']==0 || 
			$rs['id'] ==0 || 
			$rs['varyname']==9) continue;

		if ($rs['requires']!=0) 
		{
			$t = split(',', 
					   str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
				      );
			$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
		}
		else $t[0]= $wx= '��';
		$preshop .= '<tr>
		<td width="15%" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="45%" id="t'.$rs['id'].'" style="cursor:pointer;" onmouseover="window.parent.showTipEquip('.$rs['id'].',1,window.event);;this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['buy'].';prestige='.$rs['prestige'].';">'.$rs['name'].'</td>
              		<td width="35%" >' . $rs['prestige'] . '</td>
              		<td width="20%" >' . $_props['vary'][$rs['vary']] .'</td>
            	 </tr>';
	}
}

$curBagNum = 0;
#########################��������Ʒ 9.18 ̷�###########################3
$strings = ",1,2,3,4,5,6,7,8,9,10|11,12,13,14,15,16";
$strinfo = "ȫ������,��������,�������,��׽����,�ռ�����,������,��Ƭ����,��������,�������,װ������,��������,�������,�������,���ܵ���,������,�ϳɵ���";
$arr = explode(",",$strings);
$arrinfo = explode(",",$strinfo);
foreach($arr as $ks => $v)
{
	if($bagtype == $v)
	{
		$bagoption .= "<option selected=selected value='./Prestige_Mod.php?bagtype=".$v."&type=".$type." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./Prestige_Mod.php?bagtype=".$v."&type=".$type." '>".$arrinfo[$ks]."</option>";
	}
}
//�����̵��
$strings = ",1,13,5,7|8,2|3|4|6|10|11|12|14|15|16";
$strinfo = "ȫ������,�ָ�ҩ��,���ܵ���,������,�����������,�ӻ�";
$arr = explode(",",$strings);
$arrinfo = explode(",",$strinfo);
foreach($arr as $ks => $v)
{
	if($type == $v)
	{
		$options .= "<option selected=selected value='./Prestige_Mod.php?type=".$v."&bagtype=".$type." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$options .= "<option value='./Prestige_Mod.php?type=".$v."&bagtype=".$type." '>".$arrinfo[$ks]."</option>";
	}
}

##########################���������###############################
if (!is_array($userBag)) $bag='��û���κ���Ʒ!';
else
{
	foreach ($userBag as $k => $rs)
	{
		if ($rs['sums'] < 1 || 
			$rs['id']==0 || 
			$rs['zbing'] == 1) continue;
	########################��������Ʒ 9.18 ̷�###########################3
		if(!empty($bagtype))
		{
			$varyname = explode("|",$bagtype); 
			if(!in_array($rs['varyname'],$varyname))
			{
				continue;
			}
		}
########################���������###########################3
		if (strlen($rs['requires'])>2) 
		{
			$t = split(',', 
					   str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
					  );
			$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
		}
		else $t[0]= $wx= '��';
		$bag .= '<tr>
		<td width="15%" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="40%" id="t'.$rs['id'].'" style="cursor:pointer;" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.$rs['id'].';price='.$rs['sell'].';prestige='.$rs['prestige'].';">'.$rs['name'].'</td>
              		<td width="25%" >' . $rs['sell'] . '</td>
              		<td width="35%" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
		$curBagNum++;
	}
}


//Word part.
$taskword= taskcheck($user['task'], 5);

$m->memClose();



//@Load template.
if(empty($bag))
{
	$bag = "��û����Ӧ�ĵ��ߣ�";
}
if(empty($preshop))
{
	$preshop = "û����Ӧ����Ʒ��";
}
$tn = $_game['template'] . 'tpl_prestige.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#money#',
				 '#yb#',
				 '#baglimit#',
				 //right attrib.
				 '#preshoplist#',
				 '#mybag#',
				 '#word#',
				 '#options#',
				 '#bagoption#',
				 '#prestige#'
				);
	$des = array($user['money'],
				 $user['yb'],
				 $curBagNum.'/'.$user['maxbag'],
				 //right part
				 $preshop,
				 $bag,
				 $taskword,
				 $options,
				 $bagoption,
				 $user['prestige']
				);
	$preshop = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $preshop;
ob_end_flush();
?>