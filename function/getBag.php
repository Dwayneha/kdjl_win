<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: Shop main ui
*@Note: none

1Ϊ�ɽ���
2Ϊ���ɽ���
3Ϊ���ɽ��ײ��ɶ���
0Ϊ�Ե��߱�Ľ�������Ϊ׼
*/

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$user		= $_pm['user']->getUserById($_SESSION['id']);
$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
$_pm['mem']->memClose();
$bagtype = $_REQUEST['bagtype'];
$clean = $_REQUEST['clean'];

if($clean == 1)
{
	if(is_array($userBag))
	{
		foreach($userBag as $key => $value)
		{
			$array[$value['varyname']][] = $value;
		}
	}
}

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
		$bagoption .= "<option selected=selected value='./getBag_Mod.php?bagtype=".$v."'>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./paiProps_Mod.php?getBag=".$v."'>".$arrinfo[$ks]."</option>";
	}
}
##########################���������###############################
/**
* Delete userbag for novalid.
*/
$_pm['mysql']->query("DELETE FROM userbag
				       WHERE sums=0 and bsum=0 and psum=0 and uid={$_SESSION['id']}
					");

$bagUsedCellCT=0;
// Get userbag
$bag='<div class="pack_cont"><ul class="list l1 clearfix">';
if (!is_array($userBag)) $bag .= '��û���κ���Ʒ!';
else if($_REQUEST['style']=='3'){
	if (!is_array($userBag)) $bag = '��û���κ���Ʒ!';
	else
	{
		foreach ($userBag as $k => $rs)
		{
			if ($rs['sums'] < 1 || 
				$rs['id']==0 || 
				$rs['zbing'] == 1) continue;
			
			if (strlen($rs['requires'])>2) 
			{
				$t = split(',', 
						   str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
						  );
				$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
			}
			else $t[0]= $wx= '��';
			$bag .= '
			
 <table class="tit01" id="mybag1">
<tr>
			<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
						<td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="sel(this);copyWord(\''.$rs['name'].'\');bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
						<td width="60px" style="text-align:left">' . $rs['sell'] . '</td>
						<td style="text-align:left" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
					 </tr>
					 </table>';
		}
	}
	exit($bag);
}else{
	if($clean == 1)
	{
		foreach ($array as $k_1 => $rs_1)
		{
			foreach($rs_1 as $k_2 => $rs)
			{
				if ($rs['sums'] < 1 || $rs['zbing']==1) continue;
				if ($rs['sums'] < 1 || $rs['id']==0) continue;
				$bagUsedCellCT++;
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
				if ($rs['varyname'] == 9 && strlen($rs['effect'])>1)
				{
					$effect = zbAttrib($rs['effect']);
				}else $effect='';
		
				if ($_REQUEST['style']==1)
				{
						 $bag .= '<li><a >
							<p class="p1"><img src="../images/ui/bag/'.$rs['varyname'].'.gif" /></p>
							<p class="p2" id="t'.$rs['id'].'" onmouseover="showTip2('.$rs['id'].',0,1,2);" onmouseout="UnBagTip2();" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';copyWorda(\''.$rs['name'].'\');" style="text-align:left;">'.$rs['name'].'</p>
							<p class="p3">' . $_props['varyname'][$rs['varyname']] . '</p>
							<p class="p4">' . $rs['sums'] .'</p>
						 </a></li>';
				}
				else
				{
					$bag .= '<li><a >
							<p class="p1"><img src="../images/ui/bag/'.$rs['varyname'].'.gif" /></p>
							<p class="p2" id="t'.$rs['id'].'"  onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';copyWorda(\''.$rs['name'].'\')" style="text-align:left">'.$rs['name'].'</p>
							<p class="p3">' . $rs['sell'] . '</p>
							<p class="p4" id=s'.$rs['id'].'>' . $rs['sums'] .'</p>
						 </a><li>';
				}
			}
		}
	}
	else
	{
		foreach ($userBag as $k => $rs)
		{
			if ($rs['sums'] < 1 || $rs['zbing']==1) continue;
			if ($rs['sums'] < 1 || $rs['id']==0) continue;
			$bagUsedCellCT++;
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
			if ($rs['varyname'] == 9 && strlen($rs['effect'])>1)
			{
				$effect = zbAttrib($rs['effect']);
			}else $effect='';
	
			if ($_REQUEST['style']==1)
			{
					 $bag .= '<li><a >
						<p class="p1"><img src="../images/ui/bag/'.$rs['varyname'].'.gif" /></p>
						<p class="p2" id="t'.$rs['id'].'" onmouseover="showTip2('.$rs['id'].',0,1,2)" onDblClick="myContextMenu('."'".$rs['name']."'".','.$rs['id'].')" onmouseout="UnBagTip2()" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';copyWorda('."'".$rs['name']."'".');" style="text-align:left;">'.$rs['name'].'</p>
						<p class="p3">' . $_props['varyname'][$rs['varyname']] . '</p>
						<p class="p4">' . $rs['sums'] .'</p>
					 </a></li>';
			}
			else
			{
				$bag .= '<li><a >
						<p class="p1"><img src="../images/ui/bag/'.$rs['varyname'].'.gif" /></p>
						<p class="p2" id="t'.$rs['id'].'"  onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';copyWorda(\''.$rs['name'].'\')" style="text-align:left">'.$rs['name'].'</p>
						<p class="p3">' . $rs['sell'] . '</p>
						<p class="p4" id=s'.$rs['id'].'>' . $rs['sums'] .'</p>
					 </a><li>';
			}
		}
	}
}
if ($bag == '' || $bag == false) $bag = '���İ����ǿյģ�';
$bagInfo = $bagUsedCellCT.'/'.$user['maxbag'];
if ($_REQUEST['style']==1)
{
	$bag = '<div class="close_btn" onclick="ShowBox(\'Tools\',\'1\',\'3\')"></div><div class="i_pack">��ǰ�����ռ䣺'.$bagInfo.'</div>'.' <div class="pack_title">
        	<ul class="list l1"><li><p class="p1">ͼ��</p><p class="p2">��Ʒ����</p><p class="p3">����</p><p class="p4">����</p></li></ul>
        </div>'.$bag.' </ul>
        </div>
		        <div class="pac_btn">
        	<input type="button" class="ico_btn" value="ʹ��" id="inused" onclick="Used();"/>
          <input type="button" id="incangku" class="ico_btn" value="����ֿ�" onclick="putBagProps2Depot();"/>
          <input type="button" class="ico_btn" value="����" onclick="dropBagProps();"/>
          <input type="button" class="ico_btn" value="����" onclick="Clean();" />
        </div>
		';
}
else
{
	$bag = '<table width="93%" border="0" cellspacing="0" cellpadding="2" background="#ffffff">'.$bag.'</table>';
}
//<iframe   src='javascript:false'   style='Z-INDEX:-1; FILTER:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);   LEFT:0px;   VISIBILITY:inherit;   WIDTH:90%;   POSITION:absolute;   TOP:0px;   HEIGHT:290px'>    </iframe>  > 
ob_start('ob_gzip');
echo $bag;
ob_end_flush()
?>
