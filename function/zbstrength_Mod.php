<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: ̷�

*@Write Date: 18%08.09.12
*@Update Date: 18%08.09.12
*@Usage: Shop main ui for zbstrength
*@Note: none
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$props		= unserialize($_pm['mem']->get(MEM_PROPS_KEY));
$userBag	= $_pm['user']->getUserBagById($_SESSION['id']);
//��һ�ν����ҳ�棬��ղ���SESSIONֵ
$csign = $_REQUEST['csign'];
if($csign == "first")
{
	$_SESSION['pid'.$_SESSION['id']] = $_REQUEST['pid'];//ǿ������
	$_SESSION['bid'.$_SESSION['id']] = $_REQUEST['bid'];
	$_SESSION['pids'.$_SESSION['id']] = $_REQUEST['pids'];//��������
}
if(!empty($_REQUEST['pid']) && !empty($_REQUEST['bid']))
{
	$_SESSION['pid'.$_SESSION['id']] = $_REQUEST['pid'];//ǿ������
	$_SESSION['bid'.$_SESSION['id']] = $_REQUEST['bid'];
}
if(!empty($_REQUEST['pids']))
{
	$_SESSION['pids'.$_SESSION['id']] = $_REQUEST['pids'];//��������
}
	
foreach($props as $prop)
{
	//Ҫǿ���ĵ���
	if(!empty($_SESSION['pids'.$_SESSION['id']]) || !empty($_SESSION['pid'.$_SESSION['id']]))
	{
		if($prop['id'] == $_SESSION['pid'.$_SESSION['id']])
		{
			if($prop['varyname'] == 9 && $prop['plusflag'] == 1)
			{
				$pimg = $prop['img'];
				$nid = $prop['pluspid'];
				foreach($props as $nprop)
				{
					if($nprop['id'] == $nid)
					{
						$pneeds = $nprop['name'];//ǿ������Ҫ����Ʒ
					}
				}
				//�õ��û���ǰǿ���Ĵ���
				foreach($userBag as $ub)
				{
					if($ub['id'] == $_SESSION['bid'.$_SESSION['id']])
					{
						$plus_tms_eft = $ub['plus_tmes_eft'];
						//����֮ǰǿ����
						if(!empty($plus_tms_eft))
						{
							$plusarr = explode(",",$plus_tms_eft);
							foreach($harden as $kh => $har)
							{
								$num = $kh + 1;
								if($kh == $plusarr[0])
								{
									$eff = explode(",",$harden[$num]);
									$pmoney = $eff[1];
								}
							}
						}
						else
						{
							$eff = explode(",",$harden[0]);
							$pmoney = $eff[1];
						}
					}
				} //end foreach
			}
		}
	}
	//��������
	if(!empty($_SESSION['pids'.$_SESSION['id']]))
	{
		if($prop['id'] == $_SESSION['pids'.$_SESSION['id']])
		{
			if($prop['varyname'] == 11)
			{
				$himg = $prop['img'];
				if(!empty($prop['usages']))
				{
					$arr = explode("��",$prop['usages']);
					$heffect = $arr[1];
				}
			}
		}
	}
}
/*<tr>
    <td><div class="eqbox"></div></td>
    <td class="txt05"> �������Ҫǿ��װ��
  ��Ҫ���ϣ�<br />
  ���ѽ�ң�<br /></td>
  </tr>*/
if(!empty($_SESSION['pid'.$_SESSION['id']])){
	//ǿ��¯
	$id = $_SESSION['bid'.$_SESSION['id']];
	$strshop .= '<tr>
    <td><div class="eqbox" onmouseover="showTip('.$id.',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" ondblclick="takeoff('.$_SESSION['pid'.$_SESSION['id']].')"><input name="pid" type="hidden" id="pid" value='.$_SESSION['pid'.$_SESSION['id']].' /><img src="'.IMAGE_SRC_URL.'/props/'.$pimg.'"></div></td>
    <td class="txt05"> �������Ҫǿ��װ��
  ��Ҫ���ϣ�'.$pneeds.'<br />
  ���ѽ�ң�'.$pmoney.'<br /></td>
  </tr>';
}else{
	$strshop .= '<tr>
    <td><div class="eqbox"></div></td>
    <td class="txt05"> �������Ҫǿ��װ��
  ��Ҫ���ϣ�<br />
  ���ѽ�ң�<br /></td>
  </tr>';
}
/*<tr>
    <td><div class="eqbox"></div></td>
    <td class="txt05">����ǿ���������ߣ��Ǳ��룩
  ����Ч����<br /></td>
  </tr>*/
if(!empty($_SESSION['pids'.$_SESSION['id']])){
	$strshop .= '<tr>
    <td><div class="eqbox" onmouseover="showTip('.$_SESSION['pids'.$_SESSION['id']].');this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" ondblclick="takeoff('.$_SESSION['pids'.$_SESSION['id']].')"><input name="pids" type="hidden" id="pids" value='.$_SESSION['pids'.$_SESSION['id']].' /><img src="'.IMAGE_SRC_URL.'/props/'.$himg.'"></div></td>
    <td class="txt05">����ǿ���������ߣ��Ǳ��룩
  ����Ч����'.$heffect.'<br /></td>
  </tr>';
}else{
	$strshop .= '<tr>
    <td><div class="eqbox"></div></td>
    <td class="txt05">����ǿ���������ߣ��Ǳ��룩
  ����Ч����<br /></td>
  </tr>';
}




/*if(!empty($_SESSION['pid'.$_SESSION['id']]))
{
	//ǿ��¯
	$id = $_SESSION['bid'.$_SESSION['id']];
	$shop .= '<tr height="54">';
	$shop .= '<td width="18%" align="center" style="cursor:pointer;" background="'.IMAGE_SRC_URL.'/ui/shop/qh02.gif" onmouseover="showTip('.$id.',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" ondblclick="takeoff('.$_SESSION['pid'.$_SESSION['id']].')"><input name="pid" type="hidden" id="pid" value='.$_SESSION['pid'.$_SESSION['id']].' /><input name="bid" type="hidden" id="bid" value='.$_SESSION['bid'.$_SESSION['id']].' /><img src="'.IMAGE_SRC_URL.'/props/'.$pimg.'"></td>';//ͼ
	$shop .= '<td align="left">&nbsp;&nbsp;�������Ҫǿ��װ��<br />
			&nbsp;&nbsp;��Ҫ���ϣ�'.$pneeds.'<br />&nbsp;&nbsp;���ѽ�ң�'.$pmoney.'</td>';
	$shop .= '</tr>';
	//����ո�
	$shop .= '<tr>';
	$shop .= '<td width="18%" align="center">&nbsp;</td>';
	$shop .= '<td align="left">&nbsp;</td>';
	$shop .= '</tr>';
}
else
{
	//ǿ��¯
	$shop .= '<tr height="54">';
	$shop .= '<td width="18%" align="center" background="'.IMAGE_SRC_URL.'/ui/shop/qh02.gif"></td>';//ͼ
	$shop .= '<td align="left">&nbsp;&nbsp;�������Ҫǿ��װ��<br />
			&nbsp;&nbsp;��Ҫ���ϣ�<br />&nbsp;&nbsp;���ѽ�ң�</td>';
	$shop .= '</tr>';
	//����ո�
	$shop .= '<tr>';
	$shop .= '<td width="18%" align="center">&nbsp;</td>';
	$shop .= '<td align="left">&nbsp;</td>';
	$shop .= '</tr>';
}
if(!empty($_SESSION['pids'.$_SESSION['id']]))
{
	//��������
	$shop .= '<tr  height="54">';
	$shop .= '<td width="18%" align="center" style="cursor:pointer;" background="'.IMAGE_SRC_URL.'/ui/shop/qh02.gif" onmouseover="showTip('.$_SESSION['pids'.$_SESSION['id']].');this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" ondblclick="takeoff('.$_SESSION['pids'.$_SESSION['id']].')"><input name="pids" type="hidden" id="pids" value='.$_SESSION['pids'.$_SESSION['id']].' /><img src="'.IMAGE_SRC_URL.'/props/'.$himg.'"></td>';//ͼƬ
	$shop .= '<td align="left">&nbsp;&nbsp;����ǿ���������ߣ��Ǳ��룩<br />
			&nbsp;&nbsp;����Ч����'.$heffect.'<br /></td>';
	$shop .= '</tr>';
	$shop .= '<tr>';
	$shop .= '<td width="18%" align="center">&nbsp;</td>';
	$shop .= '<td align="left">&nbsp;</td>';
	$shop .= '</tr>';
}
else
{
	//��������
	$shop .= '<tr height="54">';
	$shop .= '<td width="18%" align="center" background="'.IMAGE_SRC_URL.'/ui/shop/qh02.gif"></td>';//ͼƬ
	$shop .= '<td align="left">&nbsp;&nbsp;����ǿ���������ߣ��Ǳ��룩<br />
			&nbsp;&nbsp;����Ч����<br /></td>';
	$shop .= '</tr>';
	$shop .= '<tr>';
	$shop .= '<td width="18%" align="center">&nbsp;</td>';
	$shop .= '<td align="left">&nbsp;</td>';
	$shop .= '</tr>';
}
$shop .= '<tr>
    	 <td colspan="2" align="center">
		 <input type="button" hidefocus onclick="sell();" 
style="cursor:pointer;width:102px;height:47px;background-image:url('.IMAGE_SRC_URL.'/ui/compose/hc06.gif);font-weight:bold;" id="snb" 
				value="������Ʒ" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" hidefocus onclick="harden();" 
style="cursor:pointer;width:102px;height:47px;background-image:url('.IMAGE_SRC_URL.'/ui/compose/hc06.gif);font-weight:bold;" id="snb" 
				value="��ʼǿ��" >
		 </td>
  		 </tr>';*/
$strCurBagNum=0;
$k = $rs = '';
if (!is_array($userBag)) $strbag='���İ����ǿյ�!';
else
{
	foreach ($userBag as $k => $rs)
	{
		if ($rs['sums'] < 1 || $rs['id']==0 || $rs['zbing'] == 1) continue;
		
		##################ֻ��ʾװ���;��������ĵ���#########################
		$strCurBagNum++;
		if($rs['varyname'] != 9 && $rs['varyname'] != 11 && $rs['varyname'] != 10) continue;
		if (strlen($rs['requires']) > 2) 
		{
			$t = split(',', 
					   str_replace(array('lv','wx'), array('�ȼ�','����'), $rs['requires'])
					  );
			$wx = str_replace($_props['wxs'], $_props['wxd'], $t[1]);
		}
		else $t[0]= $wx= '��';
		
		if ($rs['varyname'] == 9) $zbeffect = zbAttrib($rs['effect']) . '<br/>';
		
		$strbag .= '<tr>
              		<td width="18%%" id="t'.$rs['id'].'" style="cursor:pointer;" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="sel(this);bid='.$rs['id'].';price='.$rs['sell'].';pid='.$rs['pid'].';">'.$rs['name'].'</td>
              		<td width="18%" >' . $rs['sell'] . '</td>
              		<td width="18%%" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
	}
	
}

$taskword= taskcheck($user['task'], 9);
$_pm['mem']->memClose();
unset($u, $m);

//@Load template.
$tn = $_game['template'] . 'tpl_zbstrength.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#money#',
				 '#yb#',
				 '#baglimit#',
				 //right attrib.
				 '#strshoplist#',
				 '#strmybag#',
				 '#word#',
				);
	$des = array($user['money'],
				 $user['yb'],
				 $strCurBagNum.'/'.$user['maxbag'],
				 //right part
				 $strshop,
				 $strbag,
				 $taskword
				);
	$shop = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();

?>