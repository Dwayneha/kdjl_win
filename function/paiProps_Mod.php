<?php
require_once('../config/config.game.php');
secStart($_pm['mem']);
$user = $_pm['user']->getUserById($_SESSION['id']);
$userBag = $_pm['user']->getUserBagById($_SESSION['id']);
$bagtype = $_REQUEST['bagtype'];
$mypairet = "";

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
		$bagoption .= "<option selected=selected value='./paiProps_Mod.php?bagtype=".$v."'>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./paiProps_Mod.php?bagtype=".$v."'>".$arrinfo[$ks]."</option>";
	}
}
##########################���������###############################

if(is_array($userBag))
{
	foreach($userBag as $k => $rs)
	{
		if($rs['psum'] > 0 && $rs['psell'] > 0)
		{
			if($rs['petime'] < time())
			{
				$str = "�ѹ���";
			}
			else
			{
				$str = date("H:i:s",$rs['petime']);
			}
			$mypairet .= '<tr>
						<td width="40%" id="t'.$rs['id'].'" style="cursor:pointer;" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0" onclick="sel(this);pid = '.$rs['pid'].';bid='.($rs['id']?$rs['id']:0).';price='.$rs['psell'].';">'.$rs['name'].'('.$rs['psum'].')</td>
						<td width="30%" >' . $rs['psell'] . '</td>
						<td width="30%" >' . $str .'</td>
					 </tr>';
		}
	}
	if($mypairet == "")
	{
		$mypairet .= '<tr><td colspan=3>��ʱ����û��������Ʒ,�����������ɣ�</td></tr>';
	}
}




$bg = 0;
// Get userbag
if (!is_array($userBag)) $bag='���İ����ǿյ�!';
else
{
	foreach ($userBag as $k => $rs)
	{
		if ($rs['sums'] < 1 || $rs['id']==0 || $rs['zbing'] == 1) continue;
		$bg++;
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
			$t = split(',', str_replace(array('lv','wx'),array('�ȼ�','����'),$rs['requires']));
			$wx = str_replace($_props['wxs'],$_props['wxd'],$t[1]);
		}
		else $t[0]=$wx='��';
		
		$bag .= '<tr>
              		<td width="40%" id="t'.$rs['id'].'" style="cursor:pointer;" onmouseover="showTip('.$rs['pid'].');this.style.border=\'solid 1px #DFD496\';" onmouseout="window.parent.UnTip();;this.style.border=0" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
              		<td width="25%" >' . $rs['sell'] . '</td>
              		<td width="35%" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
	}
}

//Word part.
$taskword= taskcheck($user['task'],7);
$_pm['mem']->memClose();
unset($db);
if(empty($bag))
{
	$bag = "���ı�����û����Ӧ����Ʒ��";
}
//@Load template.
$tn = $_game['template'] . 'tpl_paiProps.html';
if (file_exists($tn))
{
	$tpl = file_get_contents($tn);
	
	$src = array('#money#',
				 '#yb#',
				 '#baglimit#',
				 //right attrib.
				 '#myshoplist#',
				 '#mybag#',
				 '#word#',
				 '#paimoney#',
				 '#bagoption#'
				);
	$des = array($user['money'],
				 $user['yb'],
				 $bg.'/'.$user['maxbag'],
				 //right part
				 $mypairet,
				 $bag,
				 $taskword,
				 $user['paimoney'],
				 $bagoption
				);
	$shop = str_replace($src, $des, $tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();
?>