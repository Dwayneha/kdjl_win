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
require_once('../config/config.game.php');
$m = $_pm['mem'];
$u = $_pm['user'];

secStart($m);

define('SPECIAL_GOODS', 99999);

$user		= $u->getUserById($_SESSION['id']);
$userBag	= $u->getUserBagById($_SESSION['id']);

$sjarr = $_pm['mysql'] -> getOneRecord("SELECT sj FROM player_ext WHERE uid = {$_SESSION['id']}");

//$props = unserialize($m->get(MEM_PROPS_KEY));

$style = intval($_GET['style']);
if(empty($style)){//$style��ʾ�̵��С��
	$style = 1;
}
$sjstyle = intval($_GET['sjstyle']);//��ʾ���ĸ����̵�

if(empty($sjstyle)){
	$sjstyle = 1;
}
if($sjstyle==1){
	$sql = "SELECT id,name,yb,vary,stime,varyname,timelimit FROM props WHERE yb > 0 AND stime like '$style%' ORDER BY stime";
	$props = $_pm['mysql']->getRecords($sql);
}else{
	$props = '';
}
//if($sjstyle==2){
	$sql = "SELECT id,name,sj,vary,stime,varyname,timelimit FROM props WHERE sj > 0 AND stime like '$style%' ORDER BY stime";
	$sjprops = $_pm['mysql']->getRecords($sql);


//if($sjstyle==3)

	$sql = "SELECT id,name,vip,vary,stime,varyname,timelimit FROM props WHERE vip > 0 AND stime like '$style%' ORDER BY stime";
	$vipprops = $_pm['mysql']->getRecords($sql);


$sql = "SELECT id,name,yb,vary,stime,varyname,timelimit FROM props WHERE yb > 0 AND stime like '$style%' ORDER BY stime";
$props = $_pm['mysql']->getRecords($sql);
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

##########################���������###############################

if (!is_array($props)) $shop='��û���κδ�����Ʒ!';
else
{
	$i=0;
	$varyname = explode("|",$basetype); 
	foreach ($props as $k => $rs)
	{
		$i++;
        if ($rs['yb'] != SPECIAL_GOODS) { /* only if statement is added by Zheng.Ping */
	
        #########################�����̵����Ʒ 9.18 ̷�###########################
            if(!empty($basetype))
            {
                
                if(!in_array($rs['varyname'],$varyname))
                {
                    continue;
                }
            }
        ##########################���������###############################
        
            if (intval($rs['yb'])<1) continue;
			$num = substr($rs['stime'],1);
			$sparr[$num] = $rs;
			//arsort($sparr);
        } /* if statement is added by Zheng.Ping */
    }

}//��������
if(is_array($sparr)){//��ʱ��ΪKEY������
	foreach($sparr as $rs){
		//�����Զ����¼ܵĹ���
		if(!empty($rs['timelimit'])){
			$limitarr = explode('|',$rs['timelimit']);
			$nowtime = date('YmdHi');
			if(!empty($limitarr[0]) && $nowtime < $limitarr[0]){
				continue;
			}
			if(!empty($limitarr[1]) && $nowtime > $limitarr[1]){
				continue;
			}
		}
		//�����Զ����¼ܵĹ������������
		
		$shop .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
                        <td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="window.parent.showTipEquip('.$rs['id'].',1,window.event);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['yb'].';">'.$rs['name'].'</td>
                        <td width="60px" style="text-align:left">' . $rs['yb'] . '</td>
                        <td style="text-align:left">' . $_props['vary'][$rs['vary']] .'</td>
                     </tr>';
	}
}else{
	$shop = '������Ҫ���ҵ���Ʒ��';
}









if (!is_array($sjprops)) $sjshop='��û���κδ�����Ʒ!';
else
{
	$i=0;
	foreach ($sjprops as $k => $rs)
	{
		$i++;
        if ($rs['sj'] != SPECIAL_GOODS) { /* only if statement is added by Zheng.Ping */
            if (intval($rs['sj'])<1) continue;
			$num = substr($rs['stime'],1);
			$sjsparr[$num] = $rs;
			//arsort($sparr);
        } /* if statement is added by Zheng.Ping */
    }

}//��������
if(is_array($sjsparr)){//��ʱ��ΪKEY������
	foreach($sjsparr as $rs){
		//�����Զ����¼ܵĹ���
		if(!empty($rs['timelimit'])){
			$limitarr = explode('|',$rs['timelimit']);
			$nowtime = date('YmdHi');
			if(!empty($limitarr[0]) && $nowtime < $limitarr[0]){
				continue;
			}
			if(!empty($limitarr[1]) && $nowtime > $limitarr[1]){
				continue;
			}
		}
		//�����Զ����¼ܵĹ������������
		
		$sjshop .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
                        <td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="window.parent.showTipEquip('.$rs['id'].',1,window.event);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['sj'].';">'.$rs['name'].'</td>
                        <td width="60px" style="text-align:left">' . $rs['sj'] . '</td>
                        <td style="text-align:left">' . $_props['vary'][$rs['vary']] .'</td>
                     </tr>';
	}
}else{
	$sjshop = '������Ҫ���ҵ���Ʒ��';
}

//vip�̳�
if (!is_array($vipprops)) $vipshop='��û���κδ�����Ʒ!';
else
{
	$i=0;
	foreach ($vipprops as $k => $rs)
	{
		$i++;
        if ($rs['vip'] != SPECIAL_GOODS) { /* only if statement is added by Zheng.Ping */
            if (intval($rs['vip'])<1) continue;
			$num = substr($rs['stime'],1);
			$vipsparr[$num] = $rs;
			//arsort($sparr);
        } /* if statement is added by Zheng.Ping */
    }

}
if(is_array($vipsparr)){
	foreach($vipsparr as $rs){	
		$vipshop .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
                        <td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="window.parent.showTipEquip('.$rs['id'].',1,window.event);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price='.$rs['vip'].';">'.$rs['name'].'</td>
                        <td width="60px" style="text-align:left">' . $rs['vip'] . '</td>
                        <td style="text-align:left">' . $_props['vary'][$rs['vary']] .'</td>
                     </tr>';
	}
}else{
	$vipshop = '������Ҫ���ҵ�vip��Ʒ��';
}
//vip�̳ǽ���


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
					<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"  onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
              		<td width="60px" style="text-align:left" >' . $rs['sell'] . '</td>
              		<td style="text-align:left">' . $rs['sums'] .'</td>
            	 </tr>';
	}
}

//Word part.
$taskword = card();


if(empty($shop))
{
	$shop = "û����Ӧ����Ʒ��";
}
if(empty($bag))
{
	$bag = "���ı�����û����Ӧ����Ʒ��";
}

$msgs = zhaohui(); 
if($msgs)//�����Ȩ�޲Ž��� true
{	
	$msgs_str = substr($msgs,1);
	$array = explode(',', $msgs_str);//Array ( [0] => 35 [1] => 34 ) 

	foreach($array as $array_key=>$array_value)
	{
		$pro.='<div id="prizelist_'.$array_value.'" style="display:none">'.getprizelist($array_value).'</div>';//������Ʒ���ַ��б�
	
	}
	
	$pro.='<div id="prizelist_real" style="padding-top:5px;padding-left:7px;display:none;background-color:#dfd496;position:absolute; left: 157px; top: 50px; width: 184px;overflow:auto;text-decoration:none;"></div>';
	
}
else
{
	$pro='';
}
//��ʱ����
$limitflag = false;
$sql = 'SELECT value2,contents FROM welcome WHERE code = "timelimitbuy"';
$tm = $_pm["mysql"] -> getOneRecord($sql);
if(is_array($tm)){
	$time = date('Y-m-d H:i:s');
	$tarr = explode('|',$tm['value2']);
	$sytime = strtotime($tarr[1]) - time();
	$ssytime = strtotime($tarr[0]) - time();//echo strtotime($tarr[0]);exit; 
	if($sytime < 0){
		$sytime = 0;
	}
	if($ssytime > 0){
		$sytime = -1;
	}//echo $sytime;exit;
	if($time > $tarr[0] && $time < $tarr[1]){
		$p = explode(',',$tm['contents']);//20100915120000
		$v = '';
		foreach($p as $v){
			$va = explode(':',$v);
			$sql = 'SELECT id,varyname,name,zhekouyb FROM props WHERE zhekouyb > 0 AND id = '.$va[0];
			$res = $_pm['mysql'] -> getOneRecord($sql);
			/*$sql = 'SELECT sum(nums) as nums FROM yblog WHERE title ='.$va[0].' AND DATE_FORMAT(buytime,"%Y%m%d%H%i%s") > '.$tarr[0].' AND DATE_FORMAT(buytime,"%Y%m%d%H%i%s") < '.$tarr[1];
			
			$ybarr = $_pm['mysql'] -> getOneRecord($sql);*/
			$zk = unserialize($_pm['mem']->get('zhekou_'.$res['id'].'_num'));
			$s = $va[1];
			if($zk > 0){
				$s = $s - $zk;
			}
			$limitshop .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$res['varyname'].'.gif" /></td>
                        <td width="110px" id="t'.$res['id'].'" style="cursor:pointer;text-align:left" onmouseover="window.parent.showTipEquip('.$res['id'].',1,window.event);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$res[name].'\');sel(this);bid='.($res['id']?$res['id']:0).';price='.$res['zhekouyb'].';">'.$res['name'].'</td>
                        <td width="60px" style="text-align:left">' . $res['zhekouyb'] . '</td>
                        <td style="text-align:left">' . ($s>0?$s:"������") .'</td>
                     </tr>';
		}
	}
}
if($limitshop == ''){
	$limitshop = '��ʱû����ʱ��������Ʒ';
}
//@Load template.
$tn = $_game['template'] . 'tpl_smshop.html';
//die($shop);
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#sj#',
				 '#yb#',
				 '#vip#',
				 '#baglimit#',
				 //right attrib.
				 '#shoplist#',
				 '#mybag#',
				 '#word#',
				 '#bagoption#',
				 '#showprizelist#',
				 '#sjwp#',
				 '#vipshop#',
				 '#sjstyle#',
				 '#style#',
				 '#limitshop#',
				 '#stime#'
				);
	$des = array($sjarr['sj'],
				 $user['yb'],
				 $user['vip'],
				 $curBagNum.'/'.$user['maxbag'],
				 //right part
				 $shop,
				 $bag,
				 $taskword,
				 $bagoption,
				 $pro,
				 $sjshop,
				 $vipshop,
				 $sjstyle,
				 $style,
				 $limitshop,
				 $sytime
				);
	$shop = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
	
$m->memClose();
echo $shop;
ob_end_flush();
?>
