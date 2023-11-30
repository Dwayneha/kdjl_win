<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

@Usage: PAI Display function(PROPS);
@Write date: 2008.05.13
@Update date: 2008.05.23
@Note: 
	1) Loop Init userbag while use have pai props. User as loop object
	2) Get in memory's userbag
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
//exit;
$user	 = $_pm['user']->getUserById($_SESSION['id']);
$userBag	 = $_pm['user']->getUserBagById($_SESSION['id']);
$now = time();
$bagtype = $_REQUEST['bagtype'];
$basetype = $_REQUEST['basetype'];
$mypairet = "";
$sjarr = $_pm['mysql'] -> getOneRecord("SELECT sj,paisj FROM player_ext WHERE uid = {$_SESSION['id']}");

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
		$bagoption .= "<option selected=selected value='./Pai_Mod.php?bagtype=".$v."&basetype=".$basetype." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./Pai_Mod.php?bagtype=".$v."&basetype=".$basetype." '>".$arrinfo[$ks]."</option>";
	}
}
//������
foreach($arr as $ks => $v)
{
	if($basetype == $v)
	{
		$baseoption .= "<option selected=selected value='./Pai_Mod.php?basetype=".$v."&bagtype=".$bagtype." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$baseoption .= "<option value='./Pai_Mod.php?basetype=".$v."&bagtype=".$bagtype." '>".$arrinfo[$ks]."</option>";
	}
}

##########################���������###############################


// �������Ҫ�Ż������ǻ��档
$paiProps	= $_pm['mysql']->getRecords("SELECT b.id as id,
									  b.uid as uid,
									  b.vary as vary,
									  b.psell as psell,
									  b.pstime as pstime,
									  b.petime as petime,
									  b.psum as psum,
									  p.name as name,
									  p.varyname as varyname,
									  p.effect as effect,
									  p.requires as requires,
									  p.sell as sell,
									  p.img as img,
									  p.pluseffect as pluseffect,
									  p.id as a
								 FROM userbag as b,props as p
								WHERE p.id = b.pid  and b.psell>0 and b.psum>0 and b.petime>'{$now}'
								ORDER BY b.pstime DESC
								LIMIT 0,60
							");
if (is_array($paiProps))
{
	foreach ($paiProps as $k => $rs)
	{
		
		#########################�ֿ����Ʒ 9.18 ̷�###########################
		if(!empty($basetype))
		{
			$varyname = explode("|",$basetype); 
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

		$pairet .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="110px" id="t'.$rs['id'].'" style="cursor:hand; text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['psell'].';">'.$rs['name'].'('.$rs['psum'].')</td>
              		<td width="60px" style=" text-align:left">' . $rs['psell'] . '</td>
              	
            	 </tr>';
	}
}
else $pairet = '<tr><td colspan=3>��ʱ��û����������Ʒ,����ʱ�������ɣ�</td></tr>';







// �������Ҫ�Ż������ǻ��档
$sjpaiProps	= $_pm['mysql']->getRecords("SELECT b.id as id,
									  b.uid as uid,
									  b.vary as vary,
									  b.psj as psj,
									  b.pstime as pstime,
									  b.petime as petime,
									  b.psum as psum,
									  p.name as name,
									  p.varyname as varyname,
									  p.effect as effect,
									  p.requires as requires,
									  p.sell as sell,
									  p.img as img,
									  p.pluseffect as pluseffect,
									  p.id as a
								 FROM userbag as b,props as p
								WHERE p.id = b.pid  and b.psj>0 and b.psum>0 and b.petime>'{$now}'
								ORDER BY b.pstime DESC
								LIMIT 0,60
							");
if (is_array($sjpaiProps))
{
	foreach ($sjpaiProps as $k => $rs)
	{
		if (strlen($rs['requires'])>2) 
		{
			$t = split(',', str_replace(array('lv','wx'),array('�ȼ�','����'),$rs['requires']));
			$wx = str_replace($_props['wxs'],$_props['wxd'],$t[1]);
		}
		else $t[0]=$wx='��';

		$sjpairet .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="110px" id="t'.$rs['id'].'" style="cursor:hand; text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['psj'].';">'.$rs['name'].'('.$rs['psum'].')</td>
              		<td width="60px" style=" text-align:left">' . $rs['psj'] . '</td>
              		
            	 </tr>';
	}
}
else $sjpairet = '<tr><td colspan=3>��ʱ��û��ˮ����������Ʒ,����ʱ�������ɣ�</td></tr>';






$bg = 0;
// Get userbag
if (!is_array($userBag)) $bag='���İ����ǿյ�!';
else
{
	foreach ($userBag as $k => $rs)
	{
		if ($rs['sums'] < 1 || $rs['id']==0 || $rs['zbing'] == 1) continue;
		if (strlen($rs['requires'])>2) 
		{
			$t = split(',', str_replace(array('lv','wx'),array('�ȼ�','����'),$rs['requires']));
			$wx = str_replace($_props['wxs'],$_props['wxd'],$t[1]);
		}
		else $t[0]=$wx='��';
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
		$bag .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="110px" id="t'.$rs['id'].'" style="cursor:hand; text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';" onmouseout="window.parent.UnTip();;this.style.border=0" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
              		<td width="60px" style=" text-align:left">' . $rs['sell'] . '</td>
              		<td style=" text-align:left" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
	}
}

if(is_array($userBag))
{
	foreach($userBag as $k => $rs)
	{
		if($rs['psum'] > 0 && ($rs['psell'] > 0 || $rs['psj'] > 0))
		{
			if($rs['petime'] < time())
			{
				$str = "�ѹ���";
			}
			else
			{
				$str = date("H:i:s",$rs['petime']);
			}
			if($rs['psell'] > 0){
				$pprice = $rs['psell'].'���';
			}else{
				$pprice = $rs['psj'].'ˮ��';
			}
			$mypairet .= '<tr>
			<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
						<td width="110px" id="t'.$rs['id'].'" style="cursor:hand; text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0" onclick="sel(this);pid = '.$rs['pid'].';bid='.($rs['id']?$rs['id']:0).';price='.$rs['psell'].';">'.$rs['name'].'('.$rs['psum'].')</td>
						<td width="60px" style=" text-align:left" >' .$pprice. '</td>
						<td style=" text-align:left">' . $str .'</td>
					 </tr>';
		}
	}
	if($mypairet == "")
	{
		$mypairet .= '<tr><td colspan=3>��ʱ����û��������Ʒ,�����������ɣ�</td></tr>';
	}
}

//Word part.
$taskword= taskcheck($user['task'],7);
$_pm['mem']->memClose();
unset($db);

if(empty($shop))
{
	$shop = "û����Ӧ����Ʒ��";
}
if(empty($bag))
{
	$bag = "���ı�����û����Ӧ����Ʒ��";
}

//@Load template.

$atype = $_GET['atype'];
$tn = $_game['template'] . 'tpl_pai.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#money#',
				 '#sj#',
				 '#baglimit#',
				 //right attrib.
				 '#shoplist#',
				 '#mybag#',
				 '#word#',
				 '#bagoption#',
				 '#baseoption#',
				 '#paimoney#',
				 '#myshoplist#',
				 '#sjwp#',
				 '#atype#',
				 '#paisj#'
				);
	$des = array($user['money'],
				 $sjarr['sj'],
				 $bg.'/'.$user['maxbag'],
				 //right part
				 $pairet,
				 $bag,
				 $taskword,
				 $bagoption,
				 $baseoption,
				 $user['paimoney'],
				 $mypairet,
				 $sjpairet,
				 $atype,
				 $sjarr['paisj']
				);
	$shop = str_replace($src,$des,$tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();
?>
