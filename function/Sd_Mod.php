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
secStart($_pm['mem']);
$user		= $_pm['user']->getUserById($_SESSION['id']);
$petsAll	= $_pm['user']->getUserPetById($_SESSION['id']);
$bag		= $_pm['user']->getUserBagById($_SESSION['id']);
$membbname = unserialize($_pm['mem']->get('db_bbname')); 

$strKeepCzl='';
$incZhl='';
$zjsxdj="'";
foreach($bag as $v)
{
	if(strpos($v["effect"],'keepczl:')!==false)
	{
		$strKeepCzl.='<option value="'.$v['id'].'">'.$v['name'].'</option>';
	}
	
	if(strpos($v["effect"],'inczhl:')!==false)
	{
		$incZhl    .='<option value="'.$v['id'].'">'.$v['name'].'</option>';
	}
	
	if(strpos($v["effect"],'zjsxdj_')!==false)
	{
		$zjsxdj    .='<option value="'.$v['id'].'">'.$v['name'].'</option>';
	}
}
$zjsxdj.="'";

$petsAlls	= unserialize($_pm['mem']->get(MEM_BB_KEY));
if (isset($_REQUEST['pid']) && intval($_REQUEST['pid'])>0)
$pid = intval($_REQUEST['pid']);
else $pid=0;
$comkk = 0;
$zskk = 0;
$style = $_GET['style'];
$petsSS=array();
$bbjs='var bbjs={};
';

$membbid = unserialize($_pm['mem']->get('db_bbname'));
$mempropsid = unserialize($_pm['mem']->get('db_propsid'));

if (is_array($petsAll))
{   
	$kk=0;
	$flag = 0;
	foreach ($petsAll as $k => $rs)
	{
		if ($rs['muchang'] != 0) continue;
		
		//�ϳɴ����￪ʼ
		if($rs['level'] >= 40 && $comkk < 3){
			$compets[$comkk++] = "<img src=''.IMAGE_SRC_URL.'/bb/{$rs['cardimg']}' onclick='Display({$rs['id']});' style='cursor:pointer;display:none;' id='cp{$comkk}'>";
			$comapetslist .= "<option value='{$rs['id']}'>{$rs['name']}-{$rs['level']}</option>\n";
			$combblistid .= $combblistid?",'{$rs['id']}-{$rs['cardimg']}'":"'{$rs['id']}-{$rs['cardimg']}'";
		}
		//�ϳ����������
		//���ʹ����￪ʼ
		if($rs['level'] >= 60 && ($rs['name'] == "�����ޣ�����" || $rs['name'] == "�����ޣ��磩" || $rs['name'] == "�����ޣ�î��") && $rs['muchang'] == 0)
		{
			$zsoption .= "<option value='{$rs['id']}'>{$rs['name']}-{$rs['level']}</option>\n";
		}
		if ($rs['level']>=60 && $zskk < 3 && $rs['wx'] == 6 && ($rs['name'] != "�����ޣ�����" || $rs['name'] != "�����ޣ��磩" || $rs['name'] != "�����ޣ�î��")){
			$zspets[$zskk++] = "<img src=''.IMAGE_SRC_URL.'/bb/{$rs['cardimg']}' onclick='Display({$rs['id']});' style='cursor:pointer;display:none;' id='zscp{$zskk}'>";
			$zsapetslist .= "<option value='{$rs['id']}'>{$rs['name']}-{$rs['level']}</option>\n";
			$zsbblistid .= $bblistid?",'{$rs['id']}-{$rs['cardimg']}'":"'{$rs['id']}-{$rs['cardimg']}'";
		}
		//���ʹ��������
		
		if($pid == 0)
		{
			if ($kk == 0) 
			{
				$sel	= 100;
				$selid	= $rs['id'];
				$pd=$rs;
			}
			else $sel = 50;
		}
		else
		{
			if($rs['id'] == $pid)
			{
				$sel	= 100;
				$selid	= $rs['id'];
				$pd=$rs;
			}
			else
			{
				$sel = 50;
			}
		}
		if ($pid == $rs['id']) 
		{
			$pd		= $rs;
			$selid	= $rs['id'];
		}
		$sellv = $sel / 100;
		$pets[$kk++] = "<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display(this,{$rs['id']});copyWord(\"{$rs['name']}\")' style='opacity: ".$sellv."; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity={$sel},finishOpacity=100);cursor:pointer;' id='i{$kk}'>";// Added a new function of "copyWord" by DuHao
		$petsSS[]="<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display1(this,{$rs['id']},0,0,0);showJHInfo({$rs['id']});copyWord(\"{$rs['name']}\")' style='opacity: ".$sellv."; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity={$sel},finishOpacity=100);cursor:pointer;' id='s{$kk}'>";
		if($flag == 0){
			$bbsszs = '<table width="210" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td colspan="2">���ﵱǰ�ȼ���<span id="bblevel">'.($rs['wx']==7?$rs['level']:'').'</span></td>
				</tr>
			  <tr>
				<td colspan="2">���ﵱǰ�ɳ���<span id="bbczl">'.($rs['wx']==7?$rs['czl']:'').'</span></td>
				</tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
				</tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
			  <tr>
				<td width="125"><img src="../images/sd_cion08.jpg" style="cursor:pointer" onclick="displayInfo(3)" width="79" height="24" /></td>
				<td width="85"><img src="../images/sd_cion09.jpg" width="79" height="24" style="cursor:pointer" onclick="sszs()" /></td>
			  </tr>
			</table>';
			$js = 'setBBId='.$rs['id'].';';
			if($rs['wx'] == 7){
				$js .= "sszsshow(".$membbname[$rs['name']]['id'].");sszsstr(0,".$membbname[$rs['name']]['id'].",this);";
			}else{
				$js .= "sszsshow(0);sszsstr(0,0,this);";
			}
			
			$flag = 1;
		}
		if($rs['wx'] == 7){
			$petsZS[]="<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display1(this,{$rs['id']},".$rs['level'].",".$rs['czl'].",1);sszsshow(".$membbname[$rs['name']]['id'].");sszsstr(0,".$membbname[$rs['name']]['id'].",this);copyWord(\"{$rs['name']}\")' style='opacity: ".$sellv."; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity={$sel},finishOpacity=100);cursor:pointer;' id='z{$kk}'>";
		}else{
			$petsZS[]="<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display1(this,{$rs['id']},0,0,2);sszsshow(0);sszsstr(0,0,this);copyWord(\"{$rs['name']}\")' style='opacity: ".$sellv."; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity={$sel},finishOpacity=100);cursor:pointer;' id='z{$kk}'>";
		}
		
		//$petsZS[]="<img src='".IMAGE_SRC_URL."/bb/{$rs['cardimg']}' onclick='Display1(this,{$rs['id']});copyWord(\"{$rs['name']}\")' style='opacity: ".$sellv."; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity={$sel},finishOpacity=100);cursor:pointer;' id='z{$kk}'>";
		$bbjs.='bbjs["'.$rs['id'].'"]="'.getSSJh($rs).'";
';
		if ($kk == 3) break;
		
	}
}

function getSSJh($bb)
{
	global $_pm,$membbid,$mempropsid;	
	$bbO = $membbid[$bb['name']];
	
	if(!$bbO)
	{
		die('�ڴ����Ҳ���Ҫ�����ĳ���('.$bb['name'].')��ԭʼ���ݣ�');
	}
	
	$bbJhSetting = $_pm['mysql']->getOneRecord('select zs_progress,need_levels,need_props,max_czl from super_jh where pet_id='.$bbO['id']);
	if(!$bbJhSetting)
	{
		return 'N/A|'.$bb['level'].'|N/A|N/A|'.$bb['remaketimes'].'|'.$bb['czl'].'|'.$bb['wx'];
		//die('���ݿ���û�иó���('.$bb['name'].')��ʥ�������趨��');
	}
	
	$nlvls = explode(',',$bbJhSetting['need_levels']);
	if(count($nlvls)-1<$bb['remaketimes'])
	{
		$limitlvl=$nlvls[0];
	}else{
		$limitlvl=$nlvls[$bb['remaketimes']];
	}
	
	$nprops = explode(',',$bbJhSetting['need_props']);
	if(count($nprops)<$bb['remaketimes']) 
	{
		$npropsIds=explode('|',$nprops[0]);
	}else{
		$npropsIds=explode('|',$nprops[$bb['remaketimes']]);
	}

	$propsStr='';
	foreach($npropsIds as $str)
	{
		$items=explode(':',$str);
		if(count($items)==2){
			if(isset($mempropsid[$items[0]]))
			{
				$propsStr.=$mempropsid[$items[0]]['name'].' '.$items[1].'��,';
			}
			else
			{
				if($items[0]==0){
					$propsStr.='<font color=ff00000>�����ڵ���Ʒ</font> '.$items[1].'��,';
				}else{
					$propsStr.='<font color=ff00000>�趨����</font>,';
				}
			}
		}
	}
	if($propsStr) $propsStr = substr($propsStr,0,-1);
	$gold=($bbJhSetting['zs_progress']+$bb['remaketimes'])*10000;
	if($bb['remaketimes']>9)
	{
		$limitlvl='N/A';
		$gold    ='N/A';
	}
	return $limitlvl.'|'.$bb['level'].'|'.$propsStr.'|'.$gold.'|'.$bb['remaketimes'].'|'.$bb['czl'].'|'.$bb['wx'].'|'.$bbJhSetting['max_czl'];
}

$taskword= taskcheck($user['task'], 2);
$rs= $pd;
// Fix parameter.
if (is_array($petsAlls) && !empty($petsAlls)) { //only if statement is added by Zheng.Ping
    foreach($petsAlls as $x=>$y)
    {
        if ($y['name'] == $rs['name'])
        {
            $rs['remakelevel']	= $y['remakelevel'];
            $rs['remakeid']		= $y['remakeid'];
            $rs['remakepid']	= $y['remakepid'];
            break;
        }
    }
}
// ��ý������ϡ�Ĭ��Ϊ��һ��������
// Get plus level info. $pd.
if ($rs['remakelevel'] == '0,0' || $rs['remakelevel']==0)
{
$chga = $chgb = array();
}
else
{

	$props =unserialize( $_pm['mem']->get('db_propsid'));
	$bbs   = unserialize( $_pm['mem']->get('db_bbid')); // added by Zheng.Ping

	$arrlevel = split(',', $rs['remakelevel']);
	$arrpid   = split(',', $rs['remakepid']);
	$petgoals = split(',', $rs['remakeid']); //added by Zheng.Ping
	$no_pet_goal = '���ɽ���';
	$chga['level'] = $arrlevel[0];
	$chga['money'] = 1000;
	$chga['clevel']= $rs['level'];
	$chga['pid']   = getPropsName($arrpid[0],$props);
	$chga['gbname'] = (false !== $petgoals && isset($petgoals[0])) ? getBbName($petgoals[0], $bbs) : $no_pet_goal; // added by Zheng.Ping
	$chap['pids1'] = getPropsId($arrpid[0]);
	$chgb['level'] = $arrlevel[1];
	$chgb['money'] = 1000;
	$chgb['clevel']= $rs['level'];
	$chgb['pid']   = $arrpid[0]==$arrpid[1]?$chga['pid']:getPropsName($arrpid[1],$props);
	$chgb['gbname'] = (false !== $petgoals && isset($petgoals[1])) ? getBbName($petgoals[1], $bbs) : $no_pet_goal; // added by Zheng.Ping
	//$chgb['pids2']   = $arrpid[0]==$arrpid[1]?$chga['pid']:getPropsId($arrpid[1]);
	$chgb['pids2'] = getPropsId($arrpid[1]);
}
//@Load template.


//�ϳ���Ʒ
if (is_array($bag))
{
	$i = 0;
	foreach($bag as $k => $v)
	{
		if($v['varyname'] == 19){
			$zswptwo .= "<option value='{$v['id']}'>{$v['name']}-{$v['sums']}��</option>\n";
			continue;
		}
		if($v['varyname'] == 23){
			$sszswp .= "<option value='{$v['id']}'>{$v['name']}-{$v['sums']}��</option>\n";
			continue;
		}
		if ($v['varyname']!=8 || $v['effect']=='') continue;
		$money = 0;
		// Get money;
		// effect format: luck:B:10%:5000, shbb:5000
		$one = explode(',', $v['effect']);
		foreach ($one as $a => $b)
		{
			$arr = explode(':', $b);
			$money+=$arr[count($arr)-1];
		}
		//ת��
		$name = explode(":",$v['usages']);
		if(!empty($v['sums']) && $name[0] != '����')
		{
			$plist .= "<option value='{$v['id']}'>{$v['name']}-{$money}-{$v['sums']}��</option>\n";
		}
		$effarr = explode(":",$v['usages']);
		if($effarr[0] == '����' && !empty($v['sums'])){
			$zsplist .= "<option value='{$v['id']}'>{$v['name']}-{$v['sums']}��</option>\n";
		}
	}
}

$a=$_pm['mysql']->getOneRecord("select hecheng_nums,czl_ss from player_ext where uid='{$_SESSION['id']}'");

if($err=mysql_error())
{
	if(strpos($err,'czl_ss')!==false)
	{
		$_pm['mysql']->query('alter table player_ext add czl_ss int(11) null default 0;');
		$a=$_pm['mysql']->getOneRecord("select hecheng_nums,czl_ss from player_ext where uid='{$_SESSION['id']}'");
	}
}

$xingyunxin=$a['hecheng_nums'];


$tn = $_game['template'] . 'tpl_sd.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	$src = 
	array(
		'#money#',
		'#yb#',
		'#baglimit#',
		//right attrib.
		'#shoplist#',
		'#mybag#',
		'#word#',
		'#one#',
		'#two#',
		'#three#',
		'#alevel#',
		'#aclevel#',
		'#amoney#',
		'#aprops#',
	    '#agbname#', /* added by Zheng.Ping */
		'#blevel#',
		'#bclevel#',
		'#bmoney#',
		'#bprops#',
	    '#bgbname#', /* added by Zheng.Ping */
		'#id#',
		'#pids1#',
		'#pids2#',
		"#comone#",
		 "#comtwo#",
		 "#comapetslist#",
		 "#bpetslist#",
		 "#wupinone#",
		 "#wupintwo#",
		 "#bballid#",
		 "#xingyunxin#",
		  "#zsone#",
		 "#zstwo#",
		 "#zsapetslist#",
		 "#zsbpetslist#",
		 "#zswupinone#",
		 "#zsbballid#",
		 "#zsoptions#",
		 "#style#",
		 "#zswupintwo#",
		 '#keepCzl#',
		 '#bbjs#',
		 '#petsSS1#','#petsSS2#','#petsSS3#',
		 '#petsZS1#','#petsZS2#','#petsZS3#',
		 '#incZhl#','#yyczl#',
		 '#bbsszsinfo#','#js#','#sszswp#','#zjsxdj#'
	);

	$des = array($user['money'],
	$user['yb'],
	count($userBag).'/'.$user['maxbag'],
	//right part
	$shop,
	$bag,
	$taskword,
	$pets[0],
	$pets[1],
	$pets[2],
	$chga['level'],
	$chga['clevel'],
	$chga['money'],
	$chga['pid'],
	$chga['gbname'], /* added by Zheng.Ping */
	$chgb['level'],
	$chgb['clevel'],
	$chgb['money'],
	$chgb['pid'],
	$chgb['gbname'], /* added by Zheng.Ping */
	$selid,
	$chap['pids1'],
	$chgb['pids2'],
	$compets[0],
	$compets[1],
	$comapetslist,
	$comapetslist,
	$plist,
	$plist,
	$combblistid,
	$xingyunxin,
	$zspets[0],
	$zspets[1],
	$zsapetslist,
	$zsapetslist,
	$zsplist,
	$zsbblistid,
	$zsoption,
	$style,
	$zswptwo,
	$strKeepCzl,
	$bbjs,$petsSS[0],$petsSS[1],$petsSS[2]
	,$petsZS[0],$petsZS[1],$petsZS[2],
	$incZhl,$a['czl_ss'],$bbsszs,$js,$sszswp,$zjsxdj
	);
	$shop = str_replace($src, $des, $tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();
// Get props name for pid.
// @return: false or String.
function getPropsName($pid,$props)
{
	$pids = explode('|',$pid);
	$rtn = array();
	if(is_array($pids))
	{
		foreach($pids as $v)
		{
			if(empty($v))
			{
				continue;
			}
			$p = $props[$v];
			if(empty($str))
			{
				$str = "<span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>";
			}
			/*else
			{
				$str .= " �� <span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>";
			}*/
		}
	}

	/*foreach($props as $p)
	{
		if(in_array($p['id'],$pids))
		{
			$rtn[] = "<span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>"; //Add "<span>" by DuHao
		}
		if(count($rtn)==count($pids)) break;
	}
	
	$arr = array();
	foreach($rtn as $v)
	{
		if(in_array($v,$arr))
		{
			continue;
		}
		else
		{
			$arr[] = $v;
		}
	}
	if (is_array($arr)) return implode(" �� ",$arr);*/
	if(!empty($str)) return $str;
	else return false;
}
function getPropsId($pid)
{
	global $_pm;
	/*$rs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY,
	'v' => "if(\$rs['id'] == {$pid}) \$ret=\$rs;"
	));*/
	$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
	$arr = explode('|',$pid);
	if(is_array($arr)){
		foreach($arr as $v){
			$rs = $mempropsid[$v];
		}
	}
	if (is_array($rs)) return $rs['id'];
	else return false;
}

/**
 * get the name of the evolved BB
 * 
 * @param integer $pid
 * @param array   $bbs
 * @return string
 * @author Zheng.Ping
 */
function getBbName($pid, $bbs)
{
	$pids = explode('|',$pid);
   // $rtn  = array();
    $ret  = '���ɽ���';
	if(!is_array($pids))
	{
		return $ret;
	}
	foreach($pids as $v)
	{
		if(!empty($v))
		{
			$p = $bbs[$v];
			if(is_array($p))
			{
				if(empty($str))
				{
					$str = "<span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>";
				}
				else
				{
					$str .= "��<span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>";
				}
			}
		}
	}

    /*if (is_array($bbs) && !empty($bbs)) {
        foreach($bbs as $p)
        {
            if(in_array($p['id'], $pids))
            {
                $rtn[] = "<span onclick='copyWord(\"{$p['name']}\")'>".$p['name']."</span>"; //Add "<span>" by DuHao
            }
            if(count($rtn)==count($pids)) break;
        }
    }*/

    if (!empty($str)) return $str;
    else return $ret;
}
$_pm['mem']->memClose();
?>