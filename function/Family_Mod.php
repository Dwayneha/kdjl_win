<?php
require_once('../config/config.game.php');
$m = $_pm['user'];
secStart($_pm['mem']);

$u = $_pm['mem'];


$user		= $m->getUserById($_SESSION['id']);
$props		= unserialize($u->get(MEM_PROPS_KEY));
$userBag	= $m->getUserBagById($_SESSION['id']);
$type = $_REQUEST['type'];
$bagtype = $_REQUEST['bagtype'];
//����

$guild = $_pm['mysql'] -> getRecords('SELECT guild.id as gid,guild.name as gname,president_id,honor,level,number_of_member,player.nickname FROM guild,player WHERE player.id = guild.president_id ORDER BY honor DESC');

if(!is_array($guild)){
	$guildstr = '<tr>
              <td height="23" colspan="5" align="center">��ʱû�м���</td>
            </tr>';
}else{
	foreach($guild as $v){
		$guildstr .= ' 
					<tr>
              <td width="20%" height="24" style="cursor:pointer" onclick="guild_id='.$v['gid'].';show_guild_info('.$v['gid'].')" align="center" onmouseover="this.style.color=\'#ff0000\'" onmouseout="this.style.color=\'#600\'">'.$v['gname'].'</td>
              <td width="20%" height="24" align="center">'.$v['nickname'].'</td>
              <td width="20%" height="24" align="center">'.$v['honor'].'</td>
              <td width="20%" height="24" align="center">'.$v['level'].'</td>
              <td width="20%" height="24" align="center">'.$v['number_of_member'].'</td>
            </tr>';
	}
	
}

//�����̵�
$member = "SELECT guild_id,contribution,honor FROM guild_members where member_id={$_SESSION['id']}";
$member_eve = $_pm['mysql']->getOneRecord($member);
$guild_level = "SELECT shop_level FROM guild where id={$member_eve['guild_id']}";
$level_query = $_pm['mysql']->getOneRecord($guild_level);
$user['shop_level'] = $level_query['shop_level'];

if (!is_array($props)) $shop='��û���κ���Ʒ!';
else
{
	$sql = "SELECT * FROM props WHERE (contribution > 0 or honor > 0) and guild_level<={$user['shop_level']}";
	$props = $_pm['mysql']->getRecords($sql);
}

if (!is_array($props)) $shop='û����Ӧ�ļ�����Ʒ!';
else
{
	foreach ($props as $k => $rs)
	{
		
		###################����չʾ��9.18��̷�######################
		if(!empty($type))
		{
			$varyname = explode("|",$type); 
			if(!in_array($rs['varyname'],$varyname))
			{
				continue;
			}
		}
		###################����չʾ����######################
		if ($rs['id'] ==0 || intval($rs['buy'])>0) continue;//buy����0��ʾ�����̵��

		$shop .= '<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
                        <td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="window.parent.showTipEquip('.$rs['id'].',1,window.event);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="copyWord(\''.$rs[name].'\');sel(this);bid='.($rs['id']?$rs['id']:0).';price1='.$rs['honor'].';price2='.$rs['contribution'].';">'.$rs['name'].'</td>
                        <td width="60px" style="text-align:left">' . $rs['honor'] . '</td>
                        <td style="text-align:left">' . $rs['contribution'] .'</td>
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
		$bagoption .= "<option selected=selected value='./Family_Mod.php?bagtype=".$v."&type=".$type." '>".$arrinfo[$ks]."</option>";
	}
	else
	{
		$bagoption .= "<option value='./Family_Mod.php?bagtype=".$v."&type=".$type." '>".$arrinfo[$ks]."</option>";
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
		$bag .= ' <table class="tit01" id="mybag1">
<tr>
		<td width="35px" ><img style="width:25px;height:25px;" src="../images/ui/bag/'.$rs['varyname'].'.gif" /></td>
              		<td width="110px" id="t'.$rs['id'].'" style="cursor:pointer;text-align:left" onmouseover="showTip('.$rs['id'].',0,1,2);this.style.border=\'solid 1px #DFD496\';"   onmouseout="window.parent.UnTip();this.style.border=0;" onclick="sel(this);copyWord(\''.$rs['name'].'\');bid='.$rs['id'].';price='.$rs['sell'].';">'.$rs['name'].'</td>
              		<td width="60px" style="text-align:left">' . $rs['sell'] . '</td>
              		<td style="text-align:left" id="s'.$rs['id'].'" >' . $rs['sums'] .'</td>
            	 </tr>';
		$curBagNum++;
	}
}

$bag .= '</table>';


//@Load template.
if(empty($bag))
{
	$bag = "��û����Ӧ�ĵ��ߣ�";
}


$sql = "SELECT guild.id,guild_settings.level,need_honor,need_props,need_member_number FROM guild_settings,guild_members,guild WHERE guild_settings.level = guild.level AND guild_members.member_id = {$_SESSION['id']} AND guild_members.guild_id = guild.id";
$arr = $_pm['mysql'] -> getOneRecord($sql);
if(!is_array($arr)){
	$guild_level_info='��û�м����κμ��壡';
}else{
	$props	= unserialize($_pm['mem']->get('db_propsid'));
	$guild_level_info = '������<font color=red> '.($arr['level']+1).' </font>��<br />��Ҫ������'.$arr['need_honor'].'<br />��Ҫ��Ա����'.$arr['need_member_number'].'<br />��Ҫ��Ʒ��<br />';
	$new_arr = explode(',',$arr['need_props']);
	foreach($new_arr as $v){
		$a = explode('|',$v);
		$have_props = $_pm['mysql'] -> getOneRecord("SELECT sums FROM guild_bag WHERE pid = $a[0] AND guild_id = {$arr['id']}");
		if(!is_array($have_props)){
			$have_props['sums'] = 0;
		}
		$guild_level_info .= $props[$a[0]]['name'].'&nbsp;'.$have_props['sums'].'/'.$a[1].'��<br />';
	}
}
//echo $guildstr;exit;
$taskword= taskcheck($user['task'],6);
$tn = $_game['template'] . 'tpl_family.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#guild#',
				'#word#',
				'#honor#',
			    '#contribution#',
				 '#baglimit#',
				 '#shoplist#',
				 '#mybag#',
				 '#bagoption#',
				 '#guild_level_info#',
				 '#guild_shop_level#'
				);
	$des = array($guildstr,
				$taskword,
				$member_eve['honor'],
				 $member_eve['contribution'],
				 $curBagNum.'/'.$user['maxbag'],
				 $shop,
				 $bag,
				 $bagoption,
				 $guild_level_info,
				 $level_query['shop_level']
				);
	$shop = str_replace($src, $des, $tpl);
}

// gzip echo. if maybe.
ob_start('ob_gzip');
echo $shop;
ob_end_flush();
?>