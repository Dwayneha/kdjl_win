<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.06.06
*@Usage:Common function.
*@Note: none
*/

/**
*@Usage: ��ȡ�ڴ��и��������ID��Mem map table : memorder
*@Param: $m obj,
*@Table: $key of memory
*@Return: false or (int)id
*/
function mem_get_autoid($m, $key, $table)
{
	if (!is_object($m))	return false;
	global $db;
	if (!is_object($db)) $db = new mysql();
	$retid = false;
	$times=0;
########����ʼ#######

    //$db->query("SET autocommit=0");
	//$db->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
	$db->query("START TRANSACTION");
	$db->query("UPDATE memorder SET lastid=lastid+1 WHERE tname='{$table}'");
	$rs = $db->getOneRecord("SELECT lastid FROM memorder WHERE tname='{$table}'");

	if (!$db->query("COMMIT")){
		  $db->query("ROLLBACK");
	}
	else
	{
		$retid = $rs['lastid'];
		unset($rs);
	}
########����ʼ#######	
	return $retid;
}

/**
@Usage: ����û�SESSION�Ƿ���ڻ��Ƿ�࿪��
@Param: $m
@Return: false;
*/
function secStart($m)
{

	$ttmt_Exclusion_File = array(
				'Aoyun_Mod.php'=>0,
				'Aoyunti_Mod.php'=>0,
				'AoyuntiGate.php'=>0,
				'Base_Mod.php'=>0,
				'BattleComein_Mod.php'=>0,
				'BattleExp_Mod.php'=>0,
				'BattleFight_Mod.php'=>0,
				'battleFightGate.php'=>0,
				'BattleInfo_Mod.php'=>0,
				'BattleProps_Mod.php'=>0,
				'Challenge_Mod.php'=>0,
				'ChallengeGate.php'=>0,
				'City_Mod.php'=>0,
				'ext_Battle.php'=>0,
				'ext_Challenge.php'=>0,
				'ext_Muchang.php'=>0,
				'fb_Mod.php'=>0,
				'fbfight_Mod.php'=>0,
				'fbfightGate.php'=>0,
				'friendGate.php'=>0,
				'getmap.php'=>0,
				'GrowthRanking_Mod.php'=>0,
				'King_Mod.php'=>0,
				'KinPrestige_Mod.php'=>0,
				'KinPrestigeGate.php'=>0,
				'guild.php'=>0,
				'Family_Mod.php'=>0,
				'FamilyGate.php'=>0,
				'Muchang_Mod.php'=>0,
				'offprops.php'=>0,
				'openMap.php'=>0,
				'Pai_Mod.php'=>0,
				'paibuyGate.php'=>0,
				'paiProps_Mod.php'=>0,
				'paiPropsGate.php'=>0,
				'paisellGate.php'=>0,
				'Pets_Mod.php'=>0,
				'Pets_Mod_View.php'=>0,
				'Prestige_Mod.php'=>0,
				'PrestigeGate.php'=>0,
				'Public_Mod.php'=>0,
				'PuPrestige_Mod.php'=>0,
				'Sd_ComposeMod.php'=>0,
				'Sd_Mod.php'=>0,
				'Shopsm_Mod.php'=>0,
				'smbuyGate.php'=>0,
				'Tuoguan_Mod.php'=>0,
				'tuoGuanGate.php'=>0,
				'User_Mod.php'=>0,
				'Zb_Mod.php'=>0,
				'ZbGate.php'=>0,
				'zbPrestige_Mod.php'=>0,
				'zbPrestigeGate.php'=>0,
				'zbstrength_Mod.php'=>0,
				'zbstrengthGate.php'=>0,
				'zs_Mod.php'=>0,				
				'zsGate.php'=>0
				);

	if(isset($ttmt_Exclusion_File[basename($_SERVER['PHP_SELF'])]))
	{
		global $_pm;
		$ts=$_pm['mysql']->getOneRecord('select state from team_members  where uid='.$_SESSION['id']);
		$s=new socketmsg();
		$team=new team($_SESSION['team_id'],$s);
		$teamState=$team->getTeamState();
		
		if($ts&&$ts['state']==1){
			header('Content-Type:text/html;charset=GB2312');
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){					
				echo '���Ѿ�����ˣ����ܽ����������!';
			}else{
				echo '<body topmargin="100px">
				<table width="300" border="0" cellpadding="0" cellpadding="0" style="background-image:url(../new_images/ui/team_notice.gif);background-repeat:no-repeat" align="center">
				  <tr>
					<th scope="col" style="color:#FFFFFF" align="right"></th>
				  </tr>
				  <tr>
					<td style="font-size:12px; padding:5px; color:#EED193" height="120">
					<font color="#fffff">���Ѿ�����ˣ����ܽ����������!</font><br/><br/>
					�� <a href="/function/Team_Mod.php?tact=return" style="color:#EED193;font-weight:bold">���ض���</a>��<br /><br />
					���� <a href="/function/Team_Mod.php?tact=swap" style="color:#EED193;font-weight:bold">��ʱ�뿪����</a>��<br /><br />
					���� <a href="/function/Team_Mod.php?tact=quit" style="color:#EED193;font-weight:bold">�˳�����</a>��
					</td>
				  </tr>
				</table>
				</body>
				';
			}
			die();
		}
	}
	
	$OnlineUsers = $m->get('waitors');
	if(!is_array($OnlineUsers )&&strlen($OnlineUsers)>2) $OnlineUsers = unserialize($OnlineUsers);
	if(isset($OnlineUsers[$_SESSION['id']]))
	{
		unset($OnlineUsers[$_SESSION['id']]);
		$m->set(array('k'=>'waitors','v'=>$OnlineUsers));
	}
	
	//================
	if ( !isset($_SESSION['id']) || intval($_SESSION['id']) < 0 || $_SESSION['id'] == '') 
		die('<a href="/login/login.php?rd='.rand().'" target=_top>�����жϣ������µ�¼!</a>');
	$crc = $_COOKIE['PHPSESSID'];
	$truecrc = unserialize($m->get($_SESSION['id'] . 'chat'));
	if ($crc != $truecrc) {
		unset($_SESSION);
		die('<a href="/login/login.php" target=_top>�����жϣ������µ�¼!!</a>');
	}
	$m=null;
	unset($m);
}

/**
*@Usage: ���ݳɳ��ʷ�Χ�����ر����ĳ�ʼ�ɳ��ʡ�
*@Param: $czl: �ɳ��ʷ�Χ��
*@Return: false or (int)id
*/
function getCzl($czl)
{
	$ok = str_replace(".", "", $czl);
	$arr = split(",", $ok);
	if (count($arr) != 2) return false;
	$num = rand($arr[0], $arr[1]);
	unset($ok, $arr);
	return $num/10;
}

/**
* @Usage: �õ�����.
*/
function getWx($n)
{
	switch($n){
		case 1: $str = '��';break;
		case 2: $str = 'ľ';break;
		case 3: $str = 'ˮ';break;
		case 4: $str = '��';break;
		case 5: $str = '��';break;
		case 6: $str = '��';break;
		case 7: $str = '��ʥ';break;
	}
	return $str;
}

function ob_gzip($content)
{ 
	if( !headers_sent() &&
	extension_loaded("zlib") &&
	strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip"))
	{
		$content = gzencode($content." \n",9);

		header("Content-Encoding: gzip");
		header("Vary: Accept-Encoding"); 
		header("Content-Length: ".strlen($content));
	}
	return $content;
}

// Return task status.
// @Parm: task id, current npc code.
// $defaultWord = $_task['npc'][8].':<br/>
// @Add: ��ɫ�滻��
// [����]��ɫ���룺#008200 \����\��ɫ���룺#848EF7 {����}��ɫ���룺#FF0000 |����|��ɫ���룺#B528E7 /����/��ɫ���룺#FF7200

function taskWord($tid,$n)
{
	global $_pm,$_task,$user;
	$user = $_pm['user']->getUserById($_SESSION['id']);
	$ret = $_task['dlg'][$n];

	if (intval($tid) == 10000 || intval($tid)<0) 
	{
		
		return $ret; // No any task.
	}

	$taskinfo = $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
										  'v'	=> "if(\$rs['id']=={$tid}) \$ret=\$rs;"
									));
	if (is_array($taskinfo))
	{
		// compose task ui.title=>detail
		if ($taskinfo['fromnpc'] == $n )
		{
			$colortag = array('[', 
			                  ']',
							  '{',
							  '}',
							  '(',
							  ')'
							  );
			$colorlist = array('<font color=#0000FF>',
							   '</font>',
							   '<font color=#848EF7>',
				               '</font>',
				               '<font color=#FF0000>',
							   '</font>'							  
							   );
			$taskinfo['title'] = str_replace($colortag, $colorlist, $taskinfo['title']);
			$title = str_replace($colortag, $colorlist, $taskinfo['title']);
			/*if($taskinfo['id'] == 179 || $taskinfo['id'] == 182 || $taskinfo['id'] == 185 || $taskinfo['id'] == 186)
			{
				$ret = "";
			}else{*/
				$ret = '<span onclick="javascript:taskCatch('.$n.')" style="cursor:pointer;"><u>'.$taskinfo['title'].'</u></span>';
			//}
		}
		else if ($taskinfo['oknpc'] == $n && strlen($user['tasklog'])>2)
		{
		   $ret = "<font color=blue>{$taskinfo['title']}</font><br />".$taskinfo['okmsg']."<font color=green>[�ݷ����]</font>
		    <br/><span style='cursor:pointer;color:green;' onclick=\"task('n={$n}');\">[<u>��ȡ����</u>]</span>";
		}
	}
	
	// ��������ȼ���ѭ������
	
	$bbinfo = $_pm['mysql']->getOneRecord("SELECT level 
											 FROM userbb 
											 WHERE id={$user['mbid']}");
	//�ȼ���������  �ȼ��ֶ���ԭ�������֣ظ�Ϊ�أ���
	/*$lvlimit = $_pm['mysql'] -> getRecords("SELECT limitlv FROM task");
	foreach($limit as $kl => $vl)
	{
		$kl1 = $kl1 + 1;
		if($lvlimit[$kl]['name'] == $lvlimit[$kl1]['name'])
		{
			continue;
		}
		else
		{
			$newlimit[] = $vl;//�����еĵȼ��������һ��һά����
		}
	}
	foreach($newlimit as $ks => $vs)
	{
		$arlv = explode(",",$vs);
		$tl = $_pm['mysql'] -> getRecords("SELECT * FROM task
										WHERE id > 8 
										and fromnpc = {$n} 
										and id in 
										(SELECT id 
										FROM task 
										WHERE {$bbinfo['level']} between $arlv[0] and $arlv[1])");
	}*/
	
	$tl = $_pm['mysql']->getRecords("SELECT * FROM task
									  WHERE id>8 and limitlv<={$bbinfo['level']} and fromnpc={$n}
									");
	$arr = 	$_pm['mysql']->getRecords("SELECT taskid FROM tasklog,task
									  WHERE task.id = tasklog.taskid and uid = {$_SESSION['id']}
									");
	if(is_array($tl) && is_array($arr))
	{
		foreach($arr as $v)
		{
			$taskid[] = $v['taskid'];
		}
	}
	if(is_array($taskid))
	{
		foreach($tl as $vtl)
		{
			if(in_array($vtl['id'],$taskid))
			{
				continue;
			}
			else
			{
				$tls[] = $vtl;
			}
		}
	}
	else
	{
		$tls = $tl;
	}		
	if (is_array($tls))
	{
		foreach($tls as $k => $v)
		{
			$title = str_replace($colortag, $colorlist, $v['title']);
			$ret .= '<br/><span onclick="javascript:OpenLogin(1,'.$v['id'].','.$n.','.($user['task']?1:0).')" style="cursor:pointer;"><u>'.$title.'</u></span>';
		}
	}
	return $ret;
}

// function part.
// curent only support one attrib.
function zbAttrib($effect)
{
	global $_props;
	if(strlen($effect)<2) return '��';

	$arr = explode(',', $effect);
	foreach ($arr as $k => $v)
	{
		if($v== '' ) continue;
		$ar = explode(':', $v);
		switch($ar[0])
		{
			case "ac": $retstr .= '����: '.$ar[1].' ';break;
			case "mc": $retstr .= '����: '.$ar[1].' ';break;
			case "hp": $retstr .= '����: '.$ar[1].' ';break;
			case "mp": $retstr .= 'ħ��: '.$ar[1].' ';break;
			case "speed": $retstr .= '�ٶ�: '.$ar[1].' ';break;
			case "hits": $retstr .= '����: '.$ar[1].' ';break;
			case "miss": $retstr .= '����: '.$ar[1].' ';break;
		}
	}
	return $retstr;
}

/*
* @Usage: Get zb all attrib.
* @Param:
* $cpets Ч�����ַ���
* $ack �ǳ���Թ���Ĺ�����
* $ack1 �ǹ���Գ���Ĺ�����
* @Return:
*/
function getzbAttrib($bid,$ack=false,$ack1=''){
	global $_pm;
	formatMsgEffect($bid);
	$str = unserialize($_pm['mem'] -> get('format_user_zhuangbei_'.$bid));
	if (empty($str)) {
		formatMsgEffect($bid);
		$str = unserialize($_pm['mem'] -> get('format_user_zhuangbei_'.$bid));
	}
	if(empty($str)) return;
	$equipChangedFlag = unserialize($_pm['mem']->get("User_bb_equip_changed_".$bid.'_'.$_SESSION['id']));
	//$_SESSION['dbg_equip_attr3'] .= date("Y-m-d H:i:s")."= 1 ====".$cpets."<br>";
	if($ack===false&&$ack1===""){
		if(!$equipChangedFlag&&$bid!=0){
			$zbattrib = unserialize($_pm['mem']->get("User_bb_equip_info_a_".$bid.'_'.$_SESSION['id']));//û�а�����Ѫ�������ȣ�
			if($zbattrib){
				//$_SESSION['dbg_equip_attr'] .= "<br>".date("Y-m-d H:i:s").$_SERVER['PHP_SELF']."= return a.<br/>";
				return $zbattrib[0];
			}
		}
	}else{
		if(!$equipChangedFlag&&$bid!=0){
			$zbattrib = unserialize($_pm['mem']->get("User_bb_equip_info_b_".$bid.'_'.$_SESSION['id']));//��������Ѫ��������
			if($ack.'_'.$ack1==$zbattrib[1]){
				if($zbattrib){
					//$_SESSION['dbg_equip_attr'] .= date("Y-m-d H:i:s").$_SERVER['PHP_SELF']."= return b.<br/>";
					return $zbattrib[0];
				}
			}
		}
	}
	
	$zbattrib=array();
	
	$pets = $_pm['mysql'] -> getOneRecord("SELECT srchp,srcmp,ac,mc,hits,speed,miss 
								             FROM userbb 
								            WHERE id = $bid");
	$arr = explode(',',$str);
	foreach ($arr as $v){
		if (empty($v)) {
			continue;
		}
		$newarr = explode(':',$v);
		switch ($newarr[0]){
			case 'ac':
				$zbattrib['ac'] += $newarr[1];
				break;
			case 'mc':
				$zbattrib['mc'] += $newarr[1];
				break;
			case 'hp':
				$zbattrib['hp'] += $newarr[1];
				break;
			case 'mp':
				$zbattrib['mp'] += $newarr[1];
				break;
			case 'speed':
				$zbattrib['speed'] += $newarr[1];
				break;
			case 'hits':
				$zbattrib['hits'] += $newarr[1];
				break;
			case 'miss':
				$zbattrib['miss'] += $newarr[1];
				break;
			case 'hprate':
				$zbattrib['hp'] += round($newarr[1] * $pets['srchp'] * 0.01);
				break;
			case 'mprate':
				$zbattrib['mp'] += round($newarr[1] * $pets['srcmp'] * 0.01);
				break;
			case 'acrate':
				$zbattrib['ac'] += round($newarr[1] * $pets['ac'] * 0.01);
				break;
			case 'mcrate':
				$zbattrib['mc'] += round($newarr[1] * $pets['mc'] * 0.01);
				break;
			case 'hitsrate':
				$zbattrib['hits'] += round($newarr[1] * $pets['hits'] * 0.01);
				break;
			case 'missrate':
				$zbattrib['miss'] += round($newarr[1] * $pets['miss'] * 0.01);
				break;
			case 'speedrate':
				$zbattrib['speed'] += round($newarr[1] * $pets['speed'] * 0.01);
				break;
			case 'dxsh':
				$zbattrib['hpdx'] += round($newarr[1] * $ack * 0.01);
				break;
			case 'hitshp':
				$zbattrib['hp1'] += round($newarr[1] * $ack1 * 0.01);
				break;
			case 'hitsmp':
				$zbattrib['mp1'] += round($newarr[1] * $ack1 * 0.01);
				break;
			case 'shjs':
				$zbattrib['ack'] += round($newarr[1] * $ack1 * 0.01);
				break;
			case 'sdmp':
				$zbattrib['hpdx'] += round($newarr[1] * $ack * 0.01);//echo $zbattrib['hp2']."sdmp";
				$zbattrib['mp1'] = $zbattrib['mp1'] - round($newarr[1] * $ack * 0.01);
				break;
			case 'szmp':
				$zbattrib['mp1'] += round($newarr[1] * $ack * 0.01);
				break;
			case 'addmoney':
				$zbattrib['money'] += $newarr[1];
				break;
			case 'time':
				$zbattrib['time'] += $newarr[1];
				break;
			case 'crit':
			{
				$zbattrib['crit'] += $newarr[1];
			}
		}
	}
	$sql = " SELECT F_add_hp,F_add_mp,F_add_ac,F_add_mc,F_add_hits,F_add_miss,F_add_speed,F_add_hprate,F_add_mprate,F_add_acrate,F_add_mcrate,F_add_hitsrate,F_add_missrate,F_add_speedrate,F_dxsh,F_hitshp,F_hitsmp,F_shjs,F_sdmp,F_szmp,F_addmoney,F_time FROM player_ext,T_Card_to_Title WHERE player_ext.now_Achievement_title = T_Card_to_Title.F_title_name AND player_ext.uid = '".$_SESSION['id']."'";
	$buff_title = $_pm['mysql']->getOneRecord($sql);
	$zbattrib['hp'] += round($buff_title['F_add_hprate'] * $pets['srchp'] * 0.01);
	$zbattrib['mp'] += round($buff_title['F_add_mprate'] * $pets['srcmp'] * 0.01);
	$zbattrib['ac'] += round($buff_title['F_add_acrate'] * $pets['ac'] * 0.01);
	$zbattrib['mc'] += round($buff_title['F_add_mcrate'] * $pets['mc'] * 0.01);
	$zbattrib['hits'] += round($buff_title['F_add_hitsrate'] * $pets['hits'] * 0.01);
	$zbattrib['miss'] += round($buff_title['F_add_missrate'] * $pets['miss'] * 0.01);
	$zbattrib['speed'] += round($buff_title['F_add_speedrate'] * $pets['speed'] * 0.01);
	
	$zbattrib['hpdx'] += round($buff_title['F_dxsh'] * $ack * 0.01);
	$zbattrib['hp1'] += round($buff_title['F_hitshp'] * $ack1 * 0.01);
	$zbattrib['mp1'] += round($buff_title['F_hitsmp'] * $ack1 * 0.01);
	$zbattrib['ack'] += round($buff_title['F_shjs'] * $ack1 * 0.01);
	$zbattrib['hpdx'] += round($buff_title['F_sdmp'] * $ack * 0.01);
	$zbattrib['mp1'] = $zbattrib['mp1'] - round($buff_title['F_sdmp'] * $ack * 0.01);
	$zbattrib['mp1'] += round($buff_title['F_szmp'] * $ack * 0.01);
	$zbattrib['money'] += $buff_title['F_addmoney'];
	$zbattrib['time'] += $buff_title['F_time'];
	
	$zbattrib['hp'] += $buff_title['F_add_hp'];
	$zbattrib['mp'] += $buff_title['F_add_mp'];
	$zbattrib['ac'] += $buff_title['F_add_ac'];
	$zbattrib['mc'] += $buff_title['F_add_mc'];
	$zbattrib['hits'] += $buff_title['F_add_hits'];
	$zbattrib['miss'] += $buff_title['F_add_miss'];
	$zbattrib['speed'] += $buff_title['F_add_speed'];
	$key_arr = array('hp','mp','ac','mc','hits','miss','speed');
	/*foreach( $zbattrib as $key => $val )
	{
		if( in_array($key,$key_arr) && $val < 0 )
		{
			$zbattrib[$key] = 0;
		}
	}*/
	//ȷ�ϲ���Ϊ����
	savezbAtrribToMem($bid,$ack,$ack1,$zbattrib);
	return $zbattrib;
}

function savezbAtrribToMem($cpets,$ack,$ack1,$zbattrib){
	global $m;
	global $_pm;
	
	if($cpets!=0){
		//$_SESSION['dbg_equip_attr'] .= date("Y-m-d H:i:s")."==========================================";
		if($ack===false&&$ack1===""){
			//$_SESSION['dbg_equip_attr'] .= $_SERVER['PHP_SELF']."=:"."User_bb_equip_info_a_".$cpets.'_'.$_SESSION['id']." a changed.<br/>";
			$_pm['mem']->set(array("k"=>"User_bb_equip_info_a_".$cpets.'_'.$_SESSION['id'],"v"=>array($zbattrib,$ack.'_'.$ack1)));
		}else{			
			$_pm['mem']->del("User_bb_equip_changed_".$cpets.'_'.$_SESSION['id']);

			$_pm['mem']->set(array("k"=>"User_bb_equip_info_b_".$cpets.'_'.$_SESSION['id'],"v"=>array($zbattrib,$ack.'_'.$ack1)));
		}
	}
}
 

/**
*@Usage: ��ȡ���뼶���ʱ�䡣
*@Return: float;
*/
function utime($inms){
    $utime = preg_match("/^(.*?) (.*?)$/", microtime(), $match);
    $utime = $match[2] + $match[1];
    if($inms){
        $utime *=  1000000;
    }
    return $utime;
}

/*
	��������
	$num �Ӱ�NPC���
	$taskid �������ɵ�����
*/
function taskcheck($taskid,$num)
{//���������������ô��һ����ʾ�ľ������ӵ�����ı��⡣
	
	if(empty($num))
	{
		$ret = "���ݴ���";
		return $ret;
		exit;
	}
	global $_pm,$_task,$user;
	
	$m = $_pm['mem'];
	$nowtime = date("YmdHis");
	$timearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));
	$memtask = unserialize($_pm['mem']->get(MEM_TASK_KEY));
	foreach($timearr as $tv)
	{
		if($tv['titles'] == "task")
		{
			$taskcheckarr[] = $tv;
		}
	}
	if(!empty($taskid))
	{
		/*$taskinfo = $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
										  'v'	=> "if(\$rs['id']=={$taskid}) \$ret=\$rs;"
									));*/
		$taskinfo = $memtask[$taskid];

	}
	/*define(TASKSTARTTIME,			'2009-04-01 00:00:00');
define(TASKENDTIME,			'2009-04-20 00:00:00');*/
	
	
	if (is_array($taskinfo))
	{
		$checktaskflag = 1;
		if(!empty($flags))
		{
			foreach($taskcheckarr as $fv)
			{
				if($fv['days'] == $taskinfo['flags'] && $nowtime >= $fv['starttime'] && $nowtime <= $fv['endtime'])
				{
					$checktaskflag = 2;
				}
			}
		}
		
		// compose task ui.title=>detail
		if($checktaskflag == 1)
		{
			if ($taskinfo['oknpc'] == $num)
			{
				$colortag = array('[', 
								  ']',
								  '{',
								  '}',
								  '(',
								  ')'
								  );
				$colorlist = array('<font color=#0000FF>',
								   '</font>',
								   '<font color=#848EF7>',
								   '</font>',
								   '<font color=#FF0000>',
								   '</font>'							  
								   );
				$taskinfo['title'] = str_replace($colortag, $colorlist, $taskinfo['title']);
				$title = str_replace($colortag, $colorlist, $taskinfo['title']);
				/*if($taskinfo['id'] == 179 || $taskinfo['id'] == 182 || $taskinfo['id'] == 185 || $taskinfo['id'] == 186)
				{
					$ret = "";
				}else{*/
					$ret[0][] = $taskinfo['id'];
					$ret[0][] = '<li class="r0'.$taskinfo['color'].'"><a href="#"><span onclick="javascript:OpenLogin(2,'.$taskinfo['id'].','.$num.','.($taskid?1:0).')">'.$taskinfo['title'].'</span></a></li>';
					$ret[0][] = $taskinfo['cid'];
				//}
			}
		}
	}
	//$task = unserialize($m->get(MEM_TASK_KEY));
	$taskArr = array();
	$rwlidarr = array();
	foreach($memtask as $v)
	{
		$flagsarrcheck = 1;
		if(!empty($v['flags']))
		{
			foreach($taskcheckarr as $vx)
			{
				if($v['flags'] == $vx['days'] && $nowtime >= $vx['starttime'] && $nowtime <= $vx['endtime'])
				{
					$flagsarrcheck = 2;
				}
			}
		}
		if($flagsarrcheck != 1)
		{
			continue;
		}
		$fnpcarr = explode("|",$v['fromnpc']);
		if($fnpcarr[0] == $num)
		{
			if(empty($v['cid']))
			{
				$sql = "SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$v['id']}";
				$checkarr = $_pm['mysql'] -> getOneRecord($sql);
				if(is_array($checkarr))
				{
					continue;
				}
				else
				{
					$npcNum = explode("|",$v['fromnpc']);
					$colortag = array('[', 
													  ']',
													  '{',
													  '}',
													  '(',
													  ')'
													  );
					$colorlist = array('<font color=#0000FF>',
												   '</font>',
												   '<font color=#848EF7>',
												   '</font>',
												   '<font color=#FF0000>',
												   '</font>'							  
												   );
					$v['title'] = str_replace($colortag, $colorlist, $v['title']);
					$ret[$npcNum[1]][] = $v['id'];
					$ret[$npcNum[1]][] = '<li class="r0'.$v['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$v['id'].','.$num.','.($taskid?1:0).')">'.$v['title'].'</span></a></li>';
					$ret[$npcNum[1]][] = $v['cid'];
				}
			}
			else
			{
				$cidarr = explode(":",$v['cid']);
				if($cidarr[0] == "rwl")
				{
					$arr = explode("|",$cidarr[1]);
					if(is_array($rwlidarr[$v['xulie']]))
					{
						if(!in_array($arr[0],$rwlidarr[$v['xulie']]) && !empty($arr[1]))
						{
							$rwlidarr[$v['xulie']][] = $arr[0];
						}
						if(!in_array($arr[1],$rwlidarr[$v['xulie']]) && !empty($arr[1]))
						{
							$rwlidarr[$v['xulie']][] = $arr[1];
						}
					}
					else
					{
						if(!empty($arr[0]))
						{
							$rwlidarr[$v['xulie']][] = $arr[0];
						}
						if(!empty($arr[0]))
						{
							$rwlidarr[$v['xulie']][] = $arr[1];
						}
					}
				}
				else if ($cidarr[0] == "paihang")
				{
					if($cidarr[1] != $user['paihang'])
					{
						continue;
					}
					else
					{
						$npcNum = explode("|",$v['fromnpc']);
						$colortag = array('[', 
														  ']',
														  '{',
														  '}',
														  '(',
														  ')'
														  );
						$colorlist = array('<font color=#0000FF>',
														   '</font>',
														   '<font color=#848EF7>',
														   '</font>',
														   '<font color=#FF0000>',
														   '</font>'							  
														   );
						$v['title'] = str_replace($colortag, $colorlist, $v['title']);
						$ret[$npcNum[1]][] = $v['id'];
						$ret[$npcNum[1]][] = '<li class="r0'.$v['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$v['id'].','.$num.','.($taskid?1:0).')">'.$v['title'].'</span><a href="#"></li>';
						$ret[$npcNum[1]][] = $v['cid'];
					}
				}
				else
				{
					if($v['hide'] == 1 && $v['id'] != $taskid)
					{
						$npcNum = explode("|",$v['fromnpc']);
						$colortag = array('[', 
														  ']',
														  '{',
														  '}',
														  '(',
														  ')'
														  );
						$colorlist = array('<font color=#0000FF>',
														   '</font>',
														   '<font color=#848EF7>',
														   '</font>',
														   '<font color=#FF0000>',
														   '</font>'							  
														   );
						$v['title'] = str_replace($colortag, $colorlist, $v['title']);
						$ret[$npcNum[1]][] = $v['id'];
						$ret[$npcNum[1]][] = '<li class="r0'.$v['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$v['id'].','.$num.','.($taskid?1:0).')">'.$v['title'].'</span></a></li>';
						$ret[$npcNum[1]][] = $v['cid'];
					}
				}
			}
		}
	}
	//$arrcount = count($rwlidarr) - 1;echo $arrcount;exit;
	//print_r($rwlidarr);exit;
	if(is_array($rwlidarr)){
		foreach($rwlidarr as $k => $v){
			$result = '';
			$result = $_pm['mysql'] -> getOneRecord("SELECT * FROM tasklog WHERE uid = {$_SESSION['id']} and xulie = {$k} and fromnpc = {$num}");
			if(!is_array($result)){//û������������е�����
				$tarr = $memtask[$v[0]];
				if($tarr['hide'] == 2){
					continue;
				}else{
					$npcNum = explode("|",$tarr['fromnpc']);
					$colortag = array('[', 
													  ']',
													  '{',
													  '}',
													  '(',
													  ')'
													  );
					$colorlist = array('<font color=#0000FF>',
													   '</font>',
													   '<font color=#848EF7>',
													   '</font>',
													   '<font color=#FF0000>',
													   '</font>'							  
													   );
					$tarr['title'] = str_replace($colortag, $colorlist, $tarr['title']);
					$ret[$npcNum[1]][] = $tarr['id'];
					$ret[$npcNum[1]][] = '<li class="r0'.$tarr['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$tarr['id'].','.$num.','.($taskid?1:0).')">'.$tarr['title'].'</span></a></li>';
					$ret[$npcNum[1]][] = $tarr['cid'];
				}
			}
			else{
				//����������е�����
				foreach($v as $vv){
					if($vv == $result['taskid']){
						$narr = explode('rwl:'.$result['taskid'].'|',$memtaskid[$result['taskid']]['cid']);
						if(count($narr) != 1){
							continue;
						}
						if($narr[0] < 1){
							continue;
						}
						$tarr = $memtask[$narr[0]];
						
						$npcNum = explode("|",$tarr['fromnpc']);
						$colortag = array('[', 
														  ']',
														  '{',
														  '}',
														  '(',
														  ')'
														  );
						$colorlist = array('<font color=#0000FF>',
														   '</font>',
														   '<font color=#848EF7>',
														   '</font>',
														   '<font color=#FF0000>',
														   '</font>'							  
														   );
						$tarr['title'] = str_replace($colortag, $colorlist, $tarr['title']);
						$ret[$npcNum[1]][] = $tarr['id'];
						$ret[$npcNum[1]][] = '<li class="r0'.$tarr['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$tarr['id'].','.$num.','.($taskid?1:0).')">'.$tarr['title'].'</span></a></li>';
						$ret[$npcNum[1]][] = $tarr['cid'];
					}
				}
			}
		}
	}
	/*for($i = 1;$i < 100;$i++)
	{
		if(!is_array($rwlidarr[$i])) break;
		if(!in_array($taskid,$rwlidarr[$i]))
		{
			$sql = "SELECT * FROM tasklog WHERE uid = {$_SESSION['id']} and xulie = {$i} and fromnpc = {$num}";
			$result = $_pm['mysql'] -> getOneRecord($sql);
			if(is_array($result))
			{
				$taskinfo = $memtask[$result['taskid']];
				$a = explode("|",$taskinfo['cid']);
				if(empty($a[1]) || !is_numeric($a[1]))
				{
					continue;
				}
				$taskinfo = $memtask[$a[1]];
				
				if(is_array($taskinfo))
				{
					$checknum = 10;
					if(!empty($taskinfo['flags']))
					{
						foreach($taskcheckarr as $fv)
						{
							if($fv['days'] == $taskinfo['flags'] && $nowtime >= $fv['starttime'] && $nowtime <= $fv['endtime'])
							{
								$checknum = 11;
							}
						}
					}
					if($checknum != 10)
					{
						continue;
					}
					$npcNum = explode("|",$taskinfo['fromnpc']);
					if($npcNum[0] == $num)
					{
						$colortag = array('[', 
															']',
														  '{',
														  '}',
														  '(',
														  ')'
															  );
						$colorlist = array('<font color=#0000FF>',
															   '</font>',
															   '<font color=#848EF7>',
															   '</font>',
															   '<font color=#FF0000>',
															   '</font>'							  
															   );
						$taskinfo['title'] = str_replace($colortag, $colorlist, $taskinfo['title']);
						$ret[$npcNum[1]][] = $taskinfo['id'];
						$ret[$npcNum[1]][] = '<li class="r0'.$taskinfo['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$taskinfo['id'].','.$num.','.($taskid?1:0).')">'.$taskinfo['title'].'</span></a></li>';
						$ret[$npcNum[1]][] = $taskinfo['cid'];
					}
				}
			}
			else
			{
				foreach($memtask as $t)
				{
					if($t['xulie'] == $i && $t['hide'] == 1)
					{
						$npcNum = explode("|",$t['fromnpc']);
						if($npcNum[0] == $num)
						{
							$checknum = 10;
							if(!empty($t['flags']))
							{
								foreach($taskcheckarr as $fv)
								{
									if($fv['days'] == $t['flags'] && $nowtime >= $fv['starttime'] && $nowtime <= $fv['endtime'])
									{
										$checknum = 11;
									}
								}
							}
							if($checknum != 10)
							{
								continue;
							}
							$colortag = array('[', 
															']',
														  '{',
														  '}',
														  '(',
														  ')'
														  );
							$colorlist = array('<font color=#0000FF>',
															   '</font>',
															   '<font color=#848EF7>',
															   '</font>',
															   '<font color=#FF0000>',
															   '</font>'							  
															   );
							$t['title'] = str_replace($colortag, $colorlist, $t['title']);
							$ret[$npcNum[1]][] = $t['id'];
							$ret[$npcNum[1]][] = '<li class="r0'.$t['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$t['id'].','.$num.','.($taskid?1:0).')">'.$t['title'].'</span></a></li>';
							$ret[$npcNum[1]][] = $t['cid'];
						}
					}
				}
			}
		}
	}*///print_r($ret);exit;
	$array = BubbleSort($ret);
	$str = show($array);
	#########################���뿨Ƭ��ʾ########################
	if($num == 3){
		$str .= card();
		$msgs = zhaohui(); 
		$str .= newcard();
		if($msgs)
		{

				$msgs_str = substr($msgs,1);
				$array = explode(',', $msgs_str);//Array ( [0] => 35 [1] => 34 ) 
				foreach($array as $key => $value)
				{
					
					$a='<span onclick="javascript:showPrizeListDiv(\''.$value.'\');" style="cursor:pointer;"><b>��ȡ�ع齱Ʒ</b></span><br />';
					$str .= $a;
				}

			
		}	
	}
	if(empty($str))
	{
		$str = $_task['dlg'][$num];
	}
	return $str;
}



//������ʾ
function show($arr)
{
	if(is_array($arr))
	{
		foreach($arr as $k => $v)
		{
			$string .= $v[1];
		}
		return $string;
	}
}

//����ֵ�Ĵ�С��������
//$str Ҫ���������
function BubbleSort($str)
{	
	$newarr = array();
	if(!is_array($str))
	{
		$arrs = "";
		return $arrs;
		exit;
	}
	foreach($str as $k => $v)
	{
		if(in_array($k,$newarr))
		{
			continue;
		}
		$newarr[] = $k;
	}
    for ($i=0;$i<count($newarr);$i++)
    {
   		for($j=count($newarr)-2;$j>=$i;$j--)
        {
             if($newarr[$j+1]<$newarr[$j])
             {
             	$tmp = $newarr[$j+1];
                $newarr[$j+1]=$newarr[$j];
                $newarr[$j]=$tmp;
             }
        }
    }
	foreach($newarr as $v)
	{
		$arrs[$v] = $str[$v];
	}
        return $arrs;
}

//ȡ��
function getBaseTaskInfoById($id){
	global $_pm;
	$gpcInfo = $_pm['mem']-> get("base_task_info_".$id);
	if($gpcInfo){
		return $gpcInfo;
	}
	$sql = "SELECT * FROM task WHERE id='{$id}'";
	$rs = $_pm['mysql'] -> getOneRecord($sql);
	$arr = array();
	$arr['k'] = "base_task_info_".$id;
	$arr['v'] = $rs;
	$_pm['mem'] -> setArr($arr);
	return $rs;
}


//��ʾ����ľ�������
//$taskid Ҫ��ʾ���ݵ�����ID
function taskdiv($taskid,$npc,$op,$ifshow)
{
	global $_pm,$_task;
	$m = $_pm['mem'];
	//$task = unserialize($m->get(MEM_TASK_KEY));
	$user = $_pm['user']->getUserById($_SESSION['id']);
	//$_gpc	 = unserialize($m->get(MEM_GPC_KEY));
	//$_gpcid  = unserialize($m->get('db_gpcid'));
	//$props = unserialize($m->get("db_propsid"));
	//if(!is_array($task))
	//{
		//$div = $_task['dlg'][$npc];
	//}
	//$divv = $task[$taskid];
	$divv = getBaseTaskInfoById($taskid);
	//$div = '<h2 style="text-align:center;">'.$divv['title'].'</h2>';
	$div = '<h2 style="text-align:left;">����������</h2>';

	//foreach($task as $divv)
	//{
		//if($divv['id'] == $taskid)
		//{
			$colortag = array('[', 
			                  ']',
							  '{',
							  '}',
							  '(',
							  ')'
							  );
			$colorlist = array('<font color=#0000FF>',
							   '</font>',
							   '<font color=#848EF7>',
				               '</font>',
				               '<font color=#0000FF>',
							   '</font>'							  
							   );
			$frommsg = str_replace($colortag, $colorlist, $divv['frommsg']);
			$okmsg = str_replace($colortag, $colorlist, $divv['okmsg']);
			//$div .= $divv['title']."<br />";
			if($op == 1)
			{
				$duihua = $frommsg."<br />";
			}
			else if($op == 2)
			{
				$duihua = $okmsg."<br />";
			}
			//$oknpc = str_replace(array(10,11,12,13,1,2,3,4,5,6,7,8,9),$_task['npc'],$divv['oknpc']);
			$oknpc = $_task['oknpc'][$divv['oknpc']];
			$div .= "���ܵص�:����ʹ��<br />";
			$div .= "���ܶԻ�:<br />".$duihua."<br />";
			$des = neednpc($divv['okneed']);//see:3,killmon:3|4|5:2,killmon:20|21|22:2,killmon:37|38|39:2,killmon:54|55|56:2,killmon:71|72:2
			//Array ( [killmon] => Array ( [2] => Array ( [0] => 71 [1] => 72 ) ) ) 
			//����Ŀ��
			if(is_array($des))
			{
				foreach($des as $k => $v)
				{
					switch($k)
					{
						case "item":
							foreach($v as $item)
							{
								foreach($item as $ik => $iv)
								{
									foreach($iv as $ivv){
										$props[$ivv] = getBasePropsInfoById($ivv);
										$p['name'] = $props[$ivv]['name'];
										$str .= "�ռ�".$p['name']."&nbsp;".$ik."��<br />";
									}
									/*foreach($props as $p)
									{
										if($iv[0] == $p['id'])
										{
											$str .= "�ռ�".$p['name']."&nbsp;".$ik."��<br />";
										}
									}*/
								}
							}
							break;
						case "money":
							$str .= "��Ҫ��ң�".$v[0]."��<br />";
							break;
						case "ww":
							$str .= "��Ҫ������".$v[0]."��<br />";
							break;
						case "jifen":
							$str .= "��Ҫ���֣�".$v[0]."��<br />";
							break;
						case "dianjuan":
							$str .= "��Ҫ���ɵ��".$v[0]."��<br />";
							break;
						case "lv":
							$lvarr = explode("|",$v[0]);
							if($lvarr[1] == 0)
							{
								$str .= "��Ҫ�ȼ���".$lvarr[0]."������<br />";
							}
							else
							{
								$str .= "��Ҫ�ȼ���".$lvarr[0]."-".$lvarr[1]."��<br />";
							}
							break;
						case "killmon":
							foreach($v as $kss => $kill)
							{
								$str1 = "";
								foreach($kill as $vss)
								{	
									$_gpcid[$vss] = getBaseGpcInfoById($vss);
									$g = $_gpcid[$vss];
									if(strpos($str1,$g['name']) === false)
									{
										if(!empty($str1))
										{
											$str1 .= "��".$g['name'];
										}
										else
										{
											$str1 = $g['name'];
										}
									}
								}
								$gcpnum = explode(",",$kss);
								$str .= "ɱ������:".$str1."&nbsp;".$gcpnum[0]."��<br />";
							}
							break;
					}
				}
			//	$div .= "����Ŀ�꣺<br />".$str;
				$div .= "<h2 style='text-align:left;'>����Ŀ�꣺</h2><br />".$str;
			}
			
			#########
			//�������
			if($op == 2)//��ɲ���ʾ�������
			{
				if(is_array($des))
				{
				/*echo $divv['okneed']."<br />";
				echo "<pre>";
				print_r($des);
				echo "</pre>";*/
					foreach($des as $k => $v)
					{
						switch($k)
						{
							case "item"://see:5,giveitem:1577:1,giveitem:1277:1,givemoney:2000000 7��
								foreach($v as $item)
								{
									foreach($item as $ik => $iv)
									{
										foreach($iv as $ivv){
										$fff = "SELECT sum(sums) as cnt FROM userbag WHERE pid={$ivv} and uid={$_SESSION['id']}";
										//echo $fff;
											$rs = $_pm['mysql'] -> getOneRecord("SELECT sum(sums) as cnt FROM userbag WHERE pid={$ivv} and uid={$_SESSION['id']} and zbing!=1");
											if($rs['cnt']>=$ik)
											{
												$jindu = "�����";
											}
											else
											{
												$jindu = "δ���";
											}
											if($rs['cnt']<=0)
											{
												$rs['cnt'] = 0;
											}
											if(!isset($props[$ivv])){
												$props[$ivv] = getBasePropsInfoById($ivv);
											}
											$p['name'] = $props[$ivv]['name'];
											$str_jin .= "���ռ�  ".$p['name']."(".$rs['cnt']."/".$ik.") ".$jindu."<br />";
										}
									}
								}
								break;
							case "money":
								if($user['money']>=$v[0])
								{
									$jindu = "�����";
								}
								else
								{
									$jindu = "δ���";
								}
								$str_jin .= "��Ҫ��ң� ".$v[0]."�� ��ǰ��ң� ".$user['money']."��  ".$jindu."<br />";
								break;
							case "ww":
								if($user['prestige']>=$v[0])
								{
									$jindu = "�����";
								}
								else
								{
									$jindu = "δ���";
								}
								$str_jin .= "��Ҫ������ ".$v[0]."�� ��ǰ������ ".$user['prestige']."��  ".$jindu."<br />";
								break;
							case "jifen":
								if($user['score']>=$v[0])
								{
									$jindu = "�����";
								}
								else
								{
									$jindu = "δ���";
								}
								$str_jin .= "��Ҫ���֣� ".$v[0]."�� ��ǰ���֣� ".$user['score']."��  ".$jindu."<br />";
								break;
							case "dianjuan":
								if($user['active_score']>=$v[0])
								{
									$jindu = "�����";
								}
								else
								{
									$jindu = "δ���";
								}
								$str_jin .= "��Ҫ���ɵ�� ".$v[0]."�� ��ǰ��� ".$user['active_score']."��  ".$jindu."<br />";
								break;
							case "lv":
								$lvarr = explode("|",$v[0]);
								if(!empty($user['mbid']))
								{
									$petsAll = $_pm['user']->getUserPetById($_SESSION['id']);
									foreach($petsAll as $pet)
									{
										if($pet['id'] == $user['mbid'])
										{
											$bname = $pet['name'];
											$blevel = $pet['level'];
										}
									}
								}
								if($lvarr[1] == 0)
								{
									$str_jin .= "��Ҫ�ȼ��� ".$lvarr[0]."������ ��ǰ����ȼ��� ".$blevel."<br />";
								}
								else
								{
									$str_jin .= "��Ҫ�ȼ��� ".$lvarr[0]."-".$lvarr[1]."��  ��ǰ����ȼ��� ".$blevel."<br />";
								}
								break;     //see:8,killmon:92|93|94:5,killmon:95|96|97:5,killmon:98|99|100:5,killmon:101|102|103:5,killmon:104|105|106:5,no:1
							case "killmon"://see:2,giveitem:900:1,killmon:92|98|95|101|104|99|93|96|102|100|105|94|97|103|106|110:40,no:1
								foreach($v as $kss => $kill)
								{
									$str1 = "";
									foreach($kill as $vss)
									{	
										if(!isset($_gpcid[$vss])){
											$_gpcid[$vss] = getBaseGpcInfoById($vss);
										}
										$g = $_gpcid[$vss];
										if(strpos($str1,$g['name']) === false)
										{
											if(!empty($str1))
											{
												$str1 .= "��".$g['name'];
											}
											else
											{
												$str1 = $g['name'];
											}
										}
									}
									$gcpnum = explode(",",$kss);
									//$str .= "ɱ������:".$str1."&nbsp;".$gcpnum[0]."��<br />";
								}
								//��ǰɱ�ֽ���
								$usertask = $_pm['mysql'] -> getOneRecord("SELECT * FROM task_accept WHERE uid = {$_SESSION['id']} and taskid={$taskid}");
								if(!empty($usertask['state']))
								{
									$arr = neednpc($usertask['state']);
									foreach($arr as $k => $v)
									{
										switch($k)
										{
											case "killmon":
												foreach($v as $kss => $kill)
												{
													$str1 = "";
													foreach($kill as $vss)
													{
														if(!isset($_gpcid[$vss])){
															$_gpcid[$vss] = getBaseGpcInfoById($vss);
														}
														$g=$_gpcid[$vss];
														if(strpos($str1,$g['name']) === false)
														{
															if(!empty($str1))
															{
																$str1 .= "��".$g['name'];
															}
															else
															{
																$str1 = $g['name'];
															}
														}
														
														/*foreach($_gpc as $g)
														{
															if($vss == $g['id'])
															{
																if(strpos($str1,$g['name']) === false)
																{
																	if(!empty($str1))
																	{
																		$str1 .= "��".$g['name'];
																	}
																	else
																	{
																		$str1 = $g['name'];
																	}
																}
															}
														}*/
													}
													$gpcnum2 = explode(",",$kss);
													//$log .= "ɱ������:".$str1."&nbsp;".$gpcnum2[0]."��<br />";
													$log .= $str1."&nbsp;".$gpcnum2[0]."��<br />";
												}
												break;
										}
									}
									//$str .= "��ǰɱ�ֽ��ȣ�<br />".$log;
									if($gpcnum2[0] != $gcpnum[0])
									{
										$jindu = "δ���";
									}
									else
									{
										$jindu = "�����";
									} 
									$str_jin .= "��ɱ�� ".$str1."&nbsp;(".$gpcnum2[0]."/".$gcpnum[0].")&nbsp;".$jindu."<br />";
								}
								else{
									//$str .= "��ǰɱ�ֽ���Ϊ0<br />";
									$gpcnum2[0] = 0;
									$str_jin .= "��ɱ�� ".$str1."&nbsp;(".$gpcnum2[0]."/".$gcpnum[0].")&nbsp;δ���<br />";
								}
								break;
						}
					}
					$div .= "<h2 style='text-align:left;'>��ɽ��ȣ�</h2><br />".$str_jin;
				}
			}
			$resultarr = result($divv['result']);
			if(is_array($resultarr))
			{
				foreach($resultarr as $k => $v)
				{
					switch($k)
					{
						case "item":
							$resultstr .= "�����õ���<br />";
							break;
						case 'lvprops':
							foreach($v as $ks => $vs){
								foreach($vs as $kkk => $pr){
									$props[$kkk] = getBasePropsInfoById($kkk);
									$resultstr .= '�ȼ��� '.$ks.' ʱ���'.$props[$kkk]['name'].' '.$pr.' �� <br />';
								}
							}
							break;
						case "props":
							foreach($v as $ks => $vs)
							{
								foreach($vs as $pr)
								{
									$props[$pr] = getBasePropsInfoById($pr);
									$p['name'] = $props[$pr]['name'];
									$propsnum = explode(',',$ks);
									$resultstr .= "�����Ʒ��".$p['name']."&nbsp;".$propsnum[0]."��<br />";
								}
							}
							break;
						case "bprops":
							foreach($v as $ks => $vs)
							{
								foreach($vs as $pr)
								{
									$props[$pr] = getBasePropsInfoById($pr);
									$p['name'] = $props[$pr]['name'];
									$propsnum = explode(',',$ks);
									$resultstr .= "��ÿɽ�����Ʒ��".$p['name']."&nbsp;".$propsnum[0]."��<br />";
									/*foreach($props as $p)
									{
										if($p['id'] == $pr)
										{
											$propsnum = explode(",",$ks);
											$resultstr .= "��ÿɽ�����Ʒ��".$p['name']."&nbsp;".$propsnum[0]."��<br />";
										}
									}*/
								}
							}
							break;	
						case "exp":
							foreach($v as $ks => $vs)
							{
								if(is_numeric($ks))
								{
									$resultstr .= "��þ��飺".$vs."��<br />";
								}
								else
								{
									$arrs = explode("-",$ks);
									if(empty($arrs[1]))
									{
										$resultstr .= "�����ɵ���������".$arrs[0]."ʱ��þ��飺".$vs."��<br />";
									}
									else
									{
										$resultstr .= "�����ɵ�������".$ks."���þ��飺".$vs."��<br />";
									}
								}
							}
							break;
						case "mon":
							foreach($v as $ks => $vs)
							{
								if(is_numeric($ks))
								{
									$resultstr .= "��ý�ң�".$vs."��<br />";
								}
								else
								{
									$arrs = explode("-",$ks);
									if(empty($arrs[1]))
									{
										$resultstr .= "�����ɵ���������".$arrs[0]."ʱ��ý�ң�".$vs."��<br />";
									}
									else
									{
										$resultstr .= "�����ɵ�������".$ks."���ý�ң�".$vs."��<br />";
									}
								}
							}
							break;
					}
				}
			}
			$div .= "<h2 style='text-align:left;'>��������</h2><br />".$resultstr;
			$ifshow=$_GET['ifshow'];
			//$div.='$op='.$op.',$ifshow='.$ifshow.'<br/>';
			if($op == 2)//���
			{	
				if($ifshow==1)//���� ��� ����gettask  complatetask  offtask
				{
					//���ν��ܰ�ť
					$div1 = "<div class='btn' align='center'>
			   <input type='button' class='b' disabled='disabled'  id='taskbtn1' style='cursor:pointer;color:green;' onclick=\"javascript:gettask('taskid={$divv['id']}');window.parent.oPopup.hide();\" value='����'>
			  <input type='button' class='c' id='taskbtn2' onclick=\"javascript:complatetask('n={$npc}&taskid={$divv['id']}');window.parent.oPopup.hide();\" value='���'>
			  ";
				}
				else
				{
					//ֻ��һ��������ť
					$div1 = "<div class='btn' align='center'>
			   <input type='button' class='b' disabled='disabled'  id='taskbtn1' value='����'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn2' value='���'>
			  ";
				}
				$div1 .= "<input type='button' class='c' id='taskbtn3'  style='cursor:pointer;color:green;' onclick=\"javascript:offtask('taskid={$divv['id']}');window.parent.oPopup.hide();\" value='����'> 
		  </div>";
			}
			else if($op == 1)//����
			{
				if($ifshow==3)
				{
					//��ʾ������ť �ѽ�
					$div1 = "<div class='btn' align='center'>
			   <input type='button' class='b' disabled='disabled'  id='taskbtn1' value='����'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn2' value='���'>
			   <input type='button' class='c' id='taskbtn3'  style='cursor:pointer;color:green;' onclick=\"javascript:offtask('taskid={$divv['id']}');window.parent.oPopup.hide();\" value='����'>
		  </div>";
				}
				else if($ifshow==4)
				{
					//��ʾ���ܰ�ť �ɽ�
					$div1 = "<div class='btn' align='center'>
			   <input type='button' class='c' style='cursor:pointer;color:green;' onclick=\"javascript:gettask('taskid={$divv['id']}');window.parent.oPopup.hide();\" value='����'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn2' value='���'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn3' value='����'>
		  </div>";
				}
				else
				{
					//��ʾ�رհ�ť ���ɽ�
					$div1 = "<div class='btn' align='center'>
			   <input type='button' class='b' disabled='disabled'  id='taskbtn1' value='����'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn2' value='���'>
			  <input type='button' class='b' disabled='disabled'  id='taskbtn3' value='����'>
		  </div>";
					
				}
				
			}
	echo '
	<h2 style="text-align:center;">'.$divv['title'].'</h2>
	  <div class="task_info" style="overflow-x:hidden; overflow-y:auto; scrollbar-arrow-color:#ffffff;scrollbar-face-color:#e1d395; scrollbar-darkshadow-color:#e1d395; scrollbar-base-color:#f3edc9; scrollbar-highlight-color:#f3edc9; scrollbar-shadow-color:#f3edc9; scrollbar-track-color:#f3edc9; scrollbar-3dlight-color:#e1d395;height:280px;padding-left:10px;">

			  '.$div.'
			</div>'.$div1.'<div class="tip02" id="do_task" style="text-align:center;color:red;"></div>';
}


//���NPC����
//$str �ַ���
function neednpc($str)
{
	$arr = explode(",",$str);
	foreach($arr as $v)
	{
		$newarr = explode(":",$v);//see:3,killmon:3|4|5:2,killmon:20|21|22:2,killmon:37|38|39:2,killmon:54|55|56:2,killmon:71|72:2
		switch ($newarr[0])
		{
			case "giveitem":
				$itemarr = explode("|",$newarr[1]);
				$ret['item'][][$newarr[2]] = $itemarr;
				break;
			case "givemoney":
				$ret['money'][] =  $newarr[1];
				break;
			case "giveww":
				$ret['ww'][] =  $newarr[1];
				break;
			case "givejifen":
				$ret['jifen'][] =  $newarr[1];
				break;
			case "givedianjuan":
				$ret['dianjuan'][] =  $newarr[1];
				break;
			case "killmon":
				if (empty($i)) {
					$i = 0;
				}
				$killmonarr[$newarr[2].",".$i] = explode("|",$newarr[1]);//Array ( [killmon] => Array ( [2,0] => Array ( [0] => 3 [1] => 4 [2] => 5 ) [2,1] => Array ( [0] => 20 [1] => 21 [2] => 22 ) [2,2] => Array ( [0] => 37 [1] => 38 [2] => 39 ) [2,3] => Array ( [0] => 54 [1] => 55 [2] => 56 ) [2,4] => Array ( [0] => 71 [1] => 72 ) ) ) 
				foreach($killmonarr as $k => $v)
				{
					if(empty($v))
					{
						continue;
					}
					$ret['killmon'][$k] = $v;
				}
				$i++;
				break;
			case "monself":
				$ret['monself'][] =  $newarr[1];
				break;
			case "lv":
				$ret['lv'][] =  $newarr[1];
				break;
			case "givevip":
				$ret['givevip'][] =  $newarr[1];
				break;
		}
	}
	return $ret;
}

//��������ý���
//$str Ҫ������ַ���
function result($str)
{
	$arr = explode(",",$str);
	foreach($arr as $v)
	{
		$newarr = explode(":",$v);
		switch ($newarr[0])
		{
			case "lvprops":
				$lvarr = explode('|',$newarr[3]);
				$ret['lvprops'][$lvarr[0].'-'.$lvarr[1]][$newarr[1]] = $newarr[2];
				break;
			case "exp":
				$exparr = explode("|",$v);
				if(count($exparr) == 1)
				{
					$ret['exp'][] = $newarr['1'];
				}
				else if(count($exparr) == 2)
				{
					$ar = explode(":",$exparr[0]);
					$key = $ar[2]."-".$exparr[1];
					$ret['exp'][$key] = $ar[1];
				}
				break;
			case "money":
				$monarr = explode("|",$v);
				if(count($monarr) == 1)
				{
					$ret['mon'][] = $newarr['1'];
				}
				else if(count($monarr) == 2)
				{
					$monar = explode(":",$monarr[0]);
					$key = $monar[2]."-".$monarr[1];
					$ret['mon'][$key] = $monar[1];
				}
				break;
			case "props":
				if(empty($j))
				{
					$j = 0;
				}
				$propsarr[$newarr[2].",".$j] = explode("|",$newarr[1]);//exp:61,props:1387:1,props:1395:1,props:1396:1,props:1397:1
				foreach($propsarr as $k => $v)
				{
					if(empty($v))
					{
						continue;
					}
					$ret['props'][$k] = $v;
				}
				$j++;
				break;
			case "bprops":
				//$newarrArray ( [0] => bprops [1] => 1415 [2] => 1 ) 
				if(empty($mm))
				{
					$mm = 0;
				}
				$bpropsarr[$newarr[2].",".$mm] = explode("|",$newarr[1]);//exp:61,props:1387:1,bprops:1395:1,props:1396:1,props:1397:1
				foreach($bpropsarr as $k => $v)
				{
					if(empty($v))
					{
						continue;
					}
					$ret['bprops'][$k] = $v;
				}
				$mm++;
				break;
			case "itemrand":
				$itemarr = explode("|",$v);
				foreach($itemarr as $itemar)
				{
					$item = explode(":",$itemar);
					if(count($item) == 4)
					{
						$ret['item'][$item[1]]['jl'] = $item[2];
						$ret['item'][$item[1]]['num'] = $item[3];
					}
					if(count($item) == 3)
					{
						$ret['item'][$item[0]]['jl'] = $item[1];
						$ret['item'][$item[0]]['num'] = $item[2];
					}
				}
				break;
			
		}
	}
	return $ret;
}

function getTemplate($file)
{
	if(!isset($GLOBALS['_pm']['mem']))
	{
		$GLOBALS['_pm']['mem'] = new memery();
	}
	
	if(!file_exists($file))
	{
		return false;
	}
	
	$filetime = filemtime($file);
	$key = preg_replace("/[^\w]/","_",$file);
	
	$data = unserialize($GLOBALS['_pm']['mem']->get($key));
	if(!$data||$data[0]<$filetime)
	{
		$data[0] = $filetime;
		$data[1] = file_get_contents($file);
		$GLOBALS['_pm']['mem']->get(array('k'=>$key,'v'=>$data));
	}
	return $data[1];
}




//ս���õ��ĺ�������
//==============================================================
function getProps($str){
	if ($str == '') return false;
	if (strstr($str, ',') === false){ // Only one props.
		$arr = split(":", $str);
		seek();
		if (rand(1, $arr[1]) <= 1)
		{
			return $arr[0];
		}else return false;
	}
	else	// for more than one props.
	{
		$ret = '';
		$arr = split(",", $str);
		seek();
		foreach ($arr as $k => $v)
		{
			if ($v == '' or strstr($v, ":")===false ) continue;
			$temp = split(":", $v);
			if (rand(1, intval($temp[1])) <=1) $ret .= $temp[0].',';
		}
		// 
		$ret = substr($ret, 0, -1);
		return $ret?$ret:false;
	}
}

function seek(){
	  	list($usec, $sec) = explode(' ', microtime());
		$t = (float) $sec + ((float) $usec * 100000);
		srand($t);
}
/**
* @Usage: �洢�û��õ��ĵ��ߵ��û�����.
* @Param: String, format: 1,2,3
* @Logic: 
  ����û������д���Ʒ����������۵���ֱ���ۼӣ���������¼�¼��
  >>������Ʒ˵���ֶ�
*/
function saveGetPropsa($idlist,$_userid=0)
{
	if($_userid==0)
	{
		$_userid=$_SESSION["id"];
	}
	if ($idlist == '' or $idlist == 0) return false;
	global $_pm;//,$user;
	
	$user		= $_pm['user']->getUserById($_userid);

	$arrobj = new arrays();

	/*$l=0;
	if (is_array($_bag))
	{
		foreach ($_bag as $x => $y)
		{
			if ($y['sums']>0 && $y['zbing']==0) $l++;
		}
	}*/
	
	$l= $_pm['mysql'] -> getOneRecord("SELECT count(id) as sums FROM userbag WHERE sums > 0 and zbing = 0 and uid = ".$_userid);
	if ($l['sums'] >= $user['maxbag']) return false;
	
	$arr = split(',', $idlist);
	foreach ($arr as $k => $v)
	{
		if($v=='') continue;
		/*$rs = $arrobj->dataGet(array('k' => MEM_USERBAG_KEY, 
									 'v' => "if(\$rs['uid']=='{$_SESSION['id']}' && \$rs['pid']=='{$v}') \$ret=\$rs;"
									 ),
								   $_bag
							  ); */
		
		$rs = $_pm['mysql']->getOneRecord("SELECT id,vary,sell FROM userbag WHERE uid=".$_userid." and pid={$v}");
		if (is_array($rs))
		{
			if ($rs['vary'] == 1) // ���۵�����.
			{
				$_pm['mysql']->query("UPDATE userbag
							   SET sums=sums+1
							 WHERE id={$rs['id']}
						  ");
			}
			else
			{
				//$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   ".$_userid.",
								   {$v},
								   {$rs['sell']},
								   2,
								   1,
								   unix_timestamp()
								  );
						  ");
				 $l++;
			}
		}
		else{
			/*$rs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
								    'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
								  ));*/
			//$mempropsid = unserialize($_pm['mem'] -> get('db_propsid'));
			$mempropsid[$v] = getBasePropsInfoById($v);
			$rs = $mempropsid[$v];
			if (is_array($rs))
			{
				//$newid = mem_get_autoid($m, MEM_ORDER_KEY, 'userbag');
				$_pm['mysql']->query("INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
							VALUES(
								   ".$_userid.",
								   {$v},
								   {$rs['sell']},
								   {$rs['vary']},
								   1,
								   unix_timestamp()
								  );
						  ");
				 $l++;
			}	
		}		
		unset($rs);
		// ����Ƿ񳬳�������
		if ($l['sums'] >= $user['maxbag']) return false;
	}	
}
/**
@Save Other data.
@Param: $bb pets info.
        $exp: get exp data.
@ �������������ԡ�
@Return: false or ���������ʾ��Ϣ��true.
@Memo:
	BB������Ҫ���еĲ�����
	1��������ʽ��X=ȡ�������Գɳ�ֵ*�ɳ��ʣ�

*/
function saveGetOther($bb, $exp, $_userid=0)
{
	global $_pm;//, $db_bb;
	//if(!isset($db_bb)||!is_array($db_bb)) 
	//$db_bb=&$bb;
	$db_bb= $_pm['mysql']->getOneRecord("SELECT *
												  FROM userbb
												  WHERE id=".$bb['id']."
												  LIMIT 0,1
												");
	$bb=$db_bb;
	
	if($_userid==0)
	{
		$_userid=$_SESSION['id'];
	}
	if (!is_array($bb)) return false;
	if ($bb['level']>=130) return false;
	$willexp = $bb['nowexp']+$exp;
	if ($willexp >= $bb['lexp'])
	{
		//$now = 0;
		$now = $willexp-$bb['lexp'];
		
		//############### Update start ###############
		$czz = $_pm['mem']->dataGet(array('k' => MEM_WX_KEY, 
								 'v' => "if(\$rs['wx'] == '{$bb['wx']}') \$ret=\$rs;"
						   ));
		$init = $_pm['mem']->dataGet(array('k' => MEM_BB_KEY, 
						 		  'v' => "if(\$rs['name'] == '{$bb['name']}') \$ret=\$rs;"
							));
		// fix .
		if (!is_array($init))
		{
			$init = $_pm['mysql']->getOneRecord("SELECT * 
												   FROM bb
												  WHERE name='{$bb['name']}'
												  LIMIT 0,1
												");
			if (!is_array($init)) return false;
		}

		// fix .

		if (is_array($czz))
		{
			$kx = split(",", $bb['kx']);
			if($bb['wx']==7){
				$maxlvlRow=$_pm['mysql']->getOneRecord('select max_level from super_jh where pet_id='.$init['id']);
				
				if($maxlvlRow&&$bb['level']>=$maxlvlRow['max_level'])
				{
					return false;
				}
			}
			//Get all attrib.
			$lv = ++$bb['level'];
			$jk = intval($czz['j']*$bb['czl'])+$kx[0];
			$mk = intval($czz['m']*$bb['czl'])+$kx[1];
			$sk = intval($czz['s']*$bb['czl'])+$kx[2];
			$hk = intval($czz['h']*$bb['czl'])+$kx[3];
			$tk = intval($czz['t']*$bb['czl'])+$kx[4];
			//
			$hp = intval($czz['hp']*$bb['czl'])+$db_bb['srchp'];
			$mp = intval($czz['mp']*$bb['czl'])+$db_bb['srcmp'];
			$ac = intval($czz['ac']*$bb['czl'])+$db_bb['ac'];
			$mc = intval($czz['mc']*$bb['czl'])+$db_bb['mc'];
			$sp = intval($czz['speed']*$bb['czl'])+$db_bb['speed'];
			$hits=intval($czz['hits']*$bb['czl'])+$db_bb['hits'];
			$miss=intval($czz['miss']*$bb['czl'])+$db_bb['miss'];
			/*��ǰ�ĳ������ԡ�
			{
				$hp = intval($czz['hp']*$bb['czl']*$lv)+$init['hp'];
				$mp = intval($czz['mp']*$bb['czl']*$lv)+$init['mp'];
				$ac = intval($czz['ac']*$bb['czl']*$lv)+$init['ac'];
				$mc = intval($czz['mc']*$bb['czl']*$lv)+$init['mc'];
				$sp = intval($czz['speed']*$bb['czl']*$lv)+$init['sp'];
				$hits=intval($czz['hits']*$bb['czl']*$lv)+$init['hits'];
				$miss=intval($czz['miss']*$bb['czl']*$lv)+$init['miss'];
			}*/
            // �����޸��ر���������
			
			$srchp = $hp;
			$srcmp = $mp;

			// Get Next Level exp require.
			$lrs = $_pm['mem']->dataGet(array('k' => MEM_EXP_KEY, 
									 'v' => "if(\$rs['level'] == '{$lv}') \$ret=\$rs;"
								  ));
			
			//update user bb.
			$_pm['mysql']->query("UPDATE userbb
						   SET level=	{$lv},
							   ac	=	{$ac},
							   mc	=	{$mc},
							   srchp=	{$srchp},
							   hp	=	{$hp},
							   srcmp=	{$srcmp},
							   mp	=	{$mp},
							   nowexp=	{$now},
							   lexp	=	{$lrs['nxtlvexp']},
							   hits	=	{$hits},
							   miss	=	{$miss},
							   speed=	{$sp},
							   kx	=	'{$jk},{$mk},{$sk},{$hk},{$tk}'
						 WHERE id={$bb['id']} and uid={$bb['uid']}
					   ");
			return true;			
		}
		//############### Update end.#################
		else return false;
	}
	else
	{
		// Save exp
		$_pm['mysql']->query("UPDATE userbb
					   SET nowexp=nowexp+{$exp}
					 WHERE id={$bb['id']} and uid=".$_userid."
				  ");
		return false;
	}
}

/**
@Gaiwu Say word.
@Param: gaiwu data for a row of database.
@Param: $nowhp, current hp.
*/
function sayWord($rs,$nowhp)
{
	global $_gwword;
	$word = '';
	if ($rs['name'] == '����')
	{
		$now = ((1-round($nowhp/$rs['hp'],2))*100);

		if ($now>80 && $now<=100) $word = $_gwword[3];
		else if($now>50 && $now<70) $word =$_gwword[2];
		else if($now>10 && $now<30) $word = $_gwword[1];
		else if($now<2) $word = $_gwword[0];
	}
	return $word;
}

/**
*@Usage: Catch task.
*@Param: Record task gid.
*@Return: void.
*@Message format: killmon:18|19|20:5
		  giveitem:885:1,killmon:3|4|5:3,killmon:20|21|22:3,killmon:37|38|39:3,killmon:54|55|56:3,killmon:71|72:3
* Note: current only one vary gpc. 
*/
function catchTask($user, $gid)
{
	global $_pm;
	//$tid = $user['task'];
	if($tid == 10000) return; // no task.
	

	//$cmtnum = findme($user['tasklog'], "killmon");
	//$desnum = findme($taskinfo['okneed'], "killmon");

	//if($cmtnum >= $desnum) return; //had ok.
	$usertaskarr = $_pm['mysql'] -> getRecords("SELECT id,taskid,state FROM task_accept WHERE uid = ".$user['id']." ORDER BY id");
	if(is_array($usertaskarr))
	{
		foreach($usertaskarr as $v)
		{//print_r($v);echo '=============>';
			if($v['state'] != 1)
			{
				$taskinfo = '';
				/*
				$taskinfo = $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
									  'v'	=> "if(\$rs['id']=={$v['taskid']}) \$ret=\$rs;"
								));
				*/
				$taskinfo = getBaseTaskInfoById($v['taskid']);
				if (is_array($taskinfo))
				{
					if(strpos($taskinfo['okneed'],'killmon') !== false)
					{
						if($v['state'] == '0')
						{//echo __LINE__."<br>";
							//echo $taskinfo['okneed'].'<br />';
							if(strpos($taskinfo['okneed'],':'.$gid.'|') !== false || strpos($taskinfo['okneed'],'|'.$gid.'|') !== false || strpos($taskinfo['okneed'],'|'.$gid.':') !== false || strpos($taskinfo['okneed'],':'.$gid.':') !== false){
								$taskloginfo = $v['id'];//echo __LINE__."<br>";
								$flag = '1';
								break;
							}
						}
						$carr = explode(',',$v['state']);//echo __LINE__."<br/>";
						if(is_array($carr))
						{
							$flag = false;
							foreach($carr as $vv)
							{
								$find = split(':',$vv);
								if($find[0] == "killmon")
								{
									//$str = 'killmon:'.$find[1].':'.$find[2];
									preg_match_all("/killmon:".str_replace('|','\|',$find[1]).":(\d+)/",$taskinfo['okneed'],$preg);//print_r($preg);echo '===================>'.$find[2].'=======================>';
									if(is_array($preg) && $preg[1][0] > 0 && $preg[1][0] > $find[2])
									{
										if(strpos($preg[0][0],'|'.$gid.'|') === false && strpos($preg[0][0],':'.$gid.'|') === false && strpos($preg[0][0],'|'.$gid.':') === false  && strpos($preg[0][0],':'.$gid.':') === false){//echo __LINE__.'<br />';
											continue;
										}
										//echo __LINE__.'<br />';
										$taskloginfo = $v['id'];
										$flag = '1';//echo $taskloginfo.'<br />';
										break;
									}
								}
							}
							if($flag==='1'){//echo __LINE__.'<br />';
								break;
							}
						}
						else
						{
							$taskloginfo = $v['id'];
							$flag = '1';
							break;
						}
					}
				}//if (is_array($taskinfo))
			}
		}
	}else{
		return;
	}//exit;
	if($flag != 1){
		return;
	}//print_r($taskinfo);
	$arr = split(',', $taskinfo['okneed']);
	if (is_array($arr))
	{
		foreach ($arr as $k => $rs)
		{
			if ($rs =='') continue;
			$find = split(':',$rs);
			if ($find[0] == "killmon")
			{
				$ar = split('\|', $find[1]);
				if (in_array($gid, $ar)) // ok. update task log.
				{
					$str = 'killmon:'.$find[1].':'.$find[2];
					findsave($str,$taskloginfo);
					break;
				}
			}
		}
	}
}

// ����Ѿ�ɱ�ֵ���Ŀ.
function findme($str, $needle)
{
	if($str == '') return 0;
	$arr = split(',',$str);

	foreach ($arr as $k => $rs)
	{
		if ($rs =='') continue;
		$find = split(':',$rs);
		if ($find[0] == $needle)
		{
			return $find[2];
		}
	}
	return 0;
}

/**
* �������ݵ��û�����״̬�С�
* ����ֻ���µ�ǰ������
*/
function findsave($str,$tid)
{	
	//$need = $user['tasklog'];
	global $_pm;
	$sql = "SELECT taskid,state FROM task_accept WHERE id = $tid";
	$tarr = $_pm['mysql'] -> getOneRecord($sql);
	$need = $tarr['state'];
	if($str == '') {$need=$str;return;}


	$patter=explode(':',$str);
	$arr = split(',',$need);

	$tasklog = '';
	$ok = 0;
	foreach ($arr as $k => $rs)
	{
		if ($rs =='') continue;
		$find = split(':',$rs);

		if ($find[0] == 'killmon' && $find[1]==$patter[1])
		{
			if ($find[2]<$patter[2])
			{
				$tasklog .= ','.$find[0].':'.$find[1].':'.($find[2]+1);
			}
			else 
			{
				$tasklog .= ','.$rs;
			}
			$ok=1;
		}
		else $tasklog .= ','.$rs;
	}

	if ($ok == 0) 
	{
		$tmp = explode(':', $str);
		
		$tasklog .= ','.$tmp[0].':'.$tmp[1].':1';
	}
	//$user['tasklog'] = $tasklog;
	//echo "UPDATE task_accept SET state = '$tasklog' WHERE id = $tid";
	$_pm['mysql'] -> query("UPDATE task_accept SET state = '$tasklog' WHERE id = $tid");
}

function stopUser($type=1,$flag = false)
{
	global $_pm;
	if($flag){
		/*$_pm['mysql']->query("UPDATE player 
				   SET secid={$type} 
				 where id={$_SESSION['id']}");*/
	}
	unset($_SESSION['id']);
	$_pm['mem']->memClose();
	exit();
}
function stopUser2($type=1,$flag = true)
{
	global $_pm;
	if($flag){
		$_pm['mysql']->query("UPDATE player 
				   SET wg='{$type}' 
				 where id={$_SESSION['id']}");
	}
	unset($_SESSION['id']);
	$_pm['mem']->memClose();
	exit();
}
/*
@Param: array=> $user
@Return false or array.
*/
function usedProps($user)
{
	global $_pm;
	$doubleexp = unserialize($_pm['mem']->get(MEM_TIME_KEY));
	foreach($doubleexp as $v)
	{
		if($v['titles'] == "exp")
		{
			$newdoubleexparr[$v['starttime'].'-'.$v['endtime']] = $v['days'];
		}else if($v['titles'] == "exp1"){
			$newdoubleexparr1[$v['starttime'].'-'.$v['endtime']] = $v['days'];
		}
	}
	$nowtime = date("YmdHis");	// double exp 12         3600
	if ($user['dblexpflag']>1 && ($user['dblstime']+$user['maxdblexptime']) > time()) // ��Ч˫������ʱ��
	{
		switch($user['dblexpflag'])
		{
			case 2: $dbl = 1.5;break;
			case 3: $dbl = 2;break;
			case 4: $dbl = 2.5;break;
			case 5: $dbl = 3;break;
			default:$dbl = 1;break;
			/*case 2: $dbl = 1;break;
			case 3: $dbl = 1;break;
			case 4: $dbl = 1;break;
			case 5: $dbl = 1;break;
			default:$dbl = 1;break;*/
		}
		$ret['double'] = $dbl;
	}
	else if($user['dblexpflag']>0 && ($user['dblstime']+$user['maxdblexptime']) <= time()) // ˫��ʱ��������Զ��رա�
	{
		$user['dblexpflag']=0;
		$user['maxdblexptime']=0;
		$ret['double']=1;
	}
	else $ret['double']=1;
	// auto fitting.
	
	############################# ̷� 9.24###############################
	//��Ǯ��
	if(empty($_SESSION['way'.$_SESSION['id']]) || $_SESSION['way'.$_SESSION['id']] == "money")
	{
		if ($user['autofitflag']>0 && $user['sysautosum']>0) // �Զ�ս��!
		{	
			$user['sysautosum']-=1;
			$ret['doubleexp'] =1.2;
			$ret['auto'] = 1;
		}
		else if($user['autofitflag']>1 && $user['sysautosum']<=0) 
		{
			$user['sysautosum']=0;
			$ret['auto'] = 2; //money auto complete.
			$ret['doubleexp'] =1;
		}
	}
	//Ԫ����
	else if($_SESSION['way'.$_SESSION['id']] == "yb")
	{
		if ($user['autofitflag']>0 && $user['maxautofitsum']>0) // �Զ�ս��!
		{	
			$user['maxautofitsum']-=1;
			$ret['doubleexp']=1.5;
			$ret['auto'] = 1;
		}
		else if($user['autofitflag']>1 && $user['maxautofitsum']<=0) 
		{
			$user['maxautofitsum']=0;
			$ret['auto'] =2; // auto complete.
			$ret['doubleexp']=1;
		}
	}
	if(is_array($newdoubleexparr))
	{
		$k = "";
		$v = "";
		foreach($newdoubleexparr as $k => $v)
		{
			if(!empty($k))
			{
				$arr = "";
				$arr = explode("-",$k);
				if($nowtime >= $arr[0] && $nowtime <= $arr[1])
				{
					$ret['double'] = $v;
				}
			}
		}
	}
	if(is_array($newdoubleexparr1))
	{
		$week = date('w');
		$time = date('Hi');
		$k = "";
		$v = "";
		foreach($newdoubleexparr1 as $k => $v)
		{
			if(!empty($k))
			{
				$arr = "";
				$arr = explode("-",$k);
				$narr = explode('|',$arr[0]);
				if($narr[0] == $week && $time >= $narr[1] && $time <= $arr[1])
				{
					$ret['double'] = $v;
				}
			}
		}
	}
	return $ret;
}

// =========================

class Ack{
	private $ap	=	array();//����
	
	private $bp =	array();//����
	
	private $hitsp	=	0;
	
	private $commack=	0;
	
	private $maxack	=	0;
	
	public $skillack	=	0;
	
	public $hit = 0;
	
	public $Crit;
	
	// save array
	public function __construct($a, $b)
	{
		global $Crit;
		$this->Crit = $Crit;
		$this->ap	=	$a;
		$this->bp	=	$b;
		$this->getHitsp();
		$this->His_and_miss();
	}
	
	/**
	@Get hits%
	@patter: ������������ֵ������������ֵ��*100%
	*/ 
	public function getHitsP()
	{
		$this->hitsp	=	($this->ap['hits']-$this->bp['miss'])/100;
		if ($this->hitsp<0.1) $this->hitsp= 0.1;
		else if ($this->hitsp>1.5) $this->hitsp= 1.5;
	}
	public function His_and_miss()
	{
		if( $this->ap['hits'] > 3*$this->bp['miss'] )	//���������ǹ��������3������
		{
			$num = 9;
		}
		if( $this->ap['hits'] <= 3*$this->bp['miss'] && $this->ap['hits'] > 2*$this->bp['miss'] )//���������ǹ��������2-3��
		{
			$num = 8;
		}
		if( $this->ap['hits'] <= 2*$this->bp['miss'] && $this->ap['hits'] > $this->bp['miss'] )//���������ǹ��������1-2��
		{
			$num = 6;
		}
		if( $this->ap['hits'] <= $this->bp['miss'] && 2*$this->ap['hits'] > $this->bp['miss'] )//��������ǳ������е�1-2��
		{
			$num = 4;
		}
		if( 2*$this->ap['hits'] <= $this->bp['miss'] && 3*$this->ap['hits'] > $this->bp['miss'] )//��������ǳ������е�2-3��
		{
			$num = 3;
		}
		if( 3*$this->ap['hits'] <= $this->bp['miss'] )//��������ǳ������е�2-3��) 
		{
			$num = 3;
		}
		$hit_or_miss = rand(1,10);
		$hit_or_miss = 1;
		if( $num  >= $hit_or_miss )	//����
		{
			$this->hit = 1;
		}
		else
		{
			$this->hit = 0;
			global $Crit;
			$Crit = 0;
		}
	}
	public function getSkillAck()
	{
		if( $this->hit == '1' )
		{
			$this->hit = 0;	//��ʼ��
			if ($this->ap['skill']!='')
			{
				$patter = $this->ap['s_id'] . ":";
				$arr = explode( $patter, $this->ap['skill']);	
				$cid = intval(substr($arr[1],0,1))-1; // ��Ӧ����key��
				if($cid<0) $cid=0;
			
				$ar = explode(",", $this->ap['s_value']);
				$ackvalue = $ar[$cid];
				$ar1= explode(",", $this->ap['s_plus']);
				$plus	=	$ar1[$cid]/100+1;
			}
			else
			{
				$ackvalue = $this->ap['s_value'];
				$plus	  = $this->ap['s_plus'];
				$plusname = explode(":",$plus);
				if($plusname[0] == 'super')
				{
					if(isset($this -> bp['srchp']))
					{
						$one = intval(($plusname[1] * $this -> bp['srchp']) / 100);
						if(isset($this -> bp['addhp']))
						{
							$one .= intval(($plusname[1] * $this -> bp['addhp']) / 100);
						}
					}
					else
					{
						$one = intval($plusname[1] * $this -> bp['hp'] / 100);
					}
				}
				else
				{
	
					if($plus == '' || $plus == 0) $plus = 1;
					else
					{
						$plus = str_replace("%","",$plus)/100+1;
					}
				}
			}
			if($plusname[0] != 'super')
			{
				$ackvalue=explode(',',$ackvalue);
				$ackvalue=$ackvalue[0];
				$ackvalue=preg_replace("/[^\d]/",'',$ackvalue);
				$base = ($this->ap['ac']+$ackvalue)*$plus-$this->bp['mc'];
				$base = $base<=0?1:$base;
		
				$one = round($base*$this->hitsp)-0; // kx=0 current.
				$this->seedInit();
				$one = $one + 1;
	
				if ($one<0) $one=0;
			}
			if($this->bp['id'] != 262)	//�����޲�����������
			{
				$foalt_ac_number = rand(-10,5);
				if($this->Crit == '1')
				{
					$one *= 2;
				}
				else
				{
					$one = intval($one*(100+$foalt_ac_number)/100);
				}
			}
			if($one < 1)
			{
				$one = 1;
			}
			$this->skillack	=	$one;
		}
	}
	
	public function seedInit()
	{
	  	list($usec, $sec) = explode(' ', microtime());
		$t = (float) $sec + ((float) $usec * 100000);
		srand($t);
	}
}


class Ack1{
	private $ap	=	array();
	
	private $bp =	array();
	
	private $hitsp	=	0;
	
	private $commack=	0;
	
	private $maxack	=	0;
	
	public $skillack	=	0;
	
	public $hit = 0;
	
	// save array
	public function __construct($a, $b)
	{
		$this->ap	=	$a;
		$this->bp	=	$b;
		$this->getHitsp();
		$this->His_and_miss();
	}

	/**
	@Get hits%
	@patter: ������������ֵ������������ֵ��*100%
	*/ 
	public function getHitsP()
	{
		$this->hitsp	=	($this->ap['hits']-$this->bp['miss'])/100;	
		if ($this->hitsp<0.1) $this->hitsp= 0.1;
		else if ($this->hitsp>1.5) $this->hitsp= 1.5;
	}
	public function His_and_miss()
	{
		if( $this->bp['hits'] > 3*$this->ap['miss'] )	//���������ǹ��������3������
		{
			$num = 9;
		}
		if( $this->bp['hits'] <= 3*$this->ap['miss'] && $this->bp['hits'] > 2*$this->ap['miss'] )//���������ǹ��������2-3��
		{
			$num = 8;
		}
		if( $this->bp['hits'] <= 2*$this->ap['miss'] && $this->bp['hits'] > $this->ap['miss'] )//���������ǹ��������1-2��
		{
			$num = 6;
		}
		if( $this->bp['hits'] <= $this->ap['miss'] && 2*$this->bp['hits'] > $this->ap['miss'] )//��������ǳ������е�1-2��
		{
			$num = 4;
		}
		if( 2*$this->bp['hits'] <= $this->ap['miss'] && 3*$this->bp['hits'] > $this->ap['miss'] )//��������ǳ������е�2-3��
		{
			$num = 3;
		}
		if( 3*$this->bp['hits'] <= $this->ap['miss'] )//��������ǳ������е�2-3��) 
		{
			$num = 3;
		}
		$hit_or_miss = rand(1,10);
		$hit_or_miss = 1;
		//echo "->".$hit_or_miss."|".$num."<-";
		if( $num  >= $hit_or_miss )	//��������
		{
			$this->hit = 1;
		}
		else
		{
			$this->hit = 0;
		}
	}	
	/**
	@Get common actvalue
	@Patter: ��ͨ�˺�=������������ֵ������������ֵ��*������+rand����1*�������ȼ���+1*�������ȼ���
	*/
	public function getCommonAct()
	{
		$this->seedInit();
		//$rand = rand(0, $this->ap['level']);
		$this->commack	=	($this->ap['ac']-$this->bp['mc'])*$this->hitsp + 1;
		if ($this->commack <= 0) $this->commack = 0;
	}
	
	/**
	@Get max ack.
	@Patter: ������������ֵ������������ֵ��*������*2+rand����2*�������ȼ���+2*�������ȼ���
	*/
	public function getMaxAct()
	{
		$this->maxack=($this->ap['ac']-$this->bp['mc'])*$this->hitsp;
		$this->seedInit();
		//$rand = rand(0, 2*$this->ap['level']);
		$this->maxack = $this->maxack + 1;
		if($this->maxack<1) $this->maxack=0;	
	}
	
	/**
	@Get skill ack
	@Patter: ȡ��{[������������ֵ+�����˺�ֵ��*��100%+���������˺��ӳɣ�������������ֵ]*������}-���������Եֿ�ֵ+rand����1*�������ȼ���+1*�������ȼ���
	������˫���ܣ��˺�=�����˺��ٷֱ� * ���Ѫ��;
	*/ 
	public function getSkillAck()
	{
		if( $this->hit == '1' )
		{
			$patter = $this->ap['s_id'] . ":";
			$arr = explode( $patter, $this->ap['skill']);	
			$cid = intval(substr($arr[1],0,1))-1; // ��Ӧ����key��
			if($cid<0) $cid=0;
		
			$ar = explode(",", $this->ap['s_value']);
			$ackvalue = $ar[$cid];
			$ackvalue=preg_replace("/[^\d]/",'',$ackvalue);
			$ar1= explode(",", $this->ap['s_plus']);
			$plus	=	$ar1[$cid]/100+1;
			/*$ackvalue = $this->ap['s_value'];
			$plus	  = $this->ap['s_plus'];*/
			//$plusname = explode(":",$plus);
			if($this -> ap['s_id'] == 50)
			{
				$plus = $plus - 1;
				if(isset($this -> bp['srchp']))
				{
					$one = intval($plus * $this -> bp['srchp']);
					if(isset($this -> bp['addhp']))
					{
						$one += intval($plus * $this -> bp['addhp']);
					}
				}
				else
				{
					$one = intval($plus * $this -> bp['hp']);
				}
			}
			if($this -> ap['s_id'] != 50)
			{
				$base = ($this->ap['ac']+$ackvalue)*$plus-$this->bp['mc'];
				$base = $base<=0?1:$base;
	
				$one = round($base*$this->hitsp)-0; // kx=0 current.
				$this->seedInit();
				$one = $one + 1;
				if ($one<0) $one=0;
			}
			$foalt_ac_number = rand(-5,10);
			$one = intval($one*(100+$foalt_ac_number)/100);
			$this->skillack	=	$one;
		}
	}
	
	public function seedInit()
	{
	  	list($usec, $sec) = explode(' ', microtime());
		$t = (float) $sec + ((float) $usec * 100000);
		srand($t);
	}
}

/**
* @Usage: ˢ��BOSS��顣
*/


// ����BOSS�������ʱ�䡣
function updateBoss($gid)
{
	global $_pm,$gs;
	$fight		= $_SESSION['fight'.$_SESSION['id']];
	if ($fight['boss']!=3) return false;
	$newtime = time()+rand(60,900);
	$_pm['mysql']->query("UPDATE boss_refresh
							 SET dtime=".$newtime.",glock=0,rtime=".time()."
						   WHERE gid={$gid}
						");
	$log = "UPDATE boss_refresh
							 SET dtime=".$newtime.",glock=0,rtime=".time()."
						   WHERE gid={$gid}
						";
	$task = new task();
	$task->saveGword("������BOSS[".$gs['name']."]������˴������");
	$log = addslashes($log);
	$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary) VALUES(unix_timestamp(),{$_SESSION['id']},{$_SESSION['id']},'{$log}',3)");	
}

function memContent2Arr($memKey,$arrKey)
{
	if(isset($GLOBALS['sysmem_'.$memKey]))
	{
		return $GLOBALS['sysmem_'.$memKey];
	}	
	global $_pm;
	$data = unserialize($_pm['mem']->get($memKey));
	$arr=array();
	foreach($data as $d)
	{
		$arr[$d[$arrKey]] = $d;
	}
	$GLOBALS['sysmem_'.$memKey] = $arr;
	return $arr;
}
function iconvall($des){
	foreach($des as $k=>$v)
	{
		$des[$k]=iconv('gbk','utf-8',$v);
	}
	return $des;
}




//$id����id
function lockItem($id){
	if(strpos($_SESSION['lockedItems'],','.$id.',')!==false)
	{
		return false;
	}else{
		$_SESSION['lockedItems'] .= ','.$id.',';
		return true;
	}
}

function unLockItem($id){
	$_SESSION['lockedItems']=str_replace(array(','.$id.',',',,'),array('',','),$_SESSION['lockedItems']);
}


//װ��Ч�����Ӵ����ڴ�
/*
$id װ��id(props)
*/
function formatMsgEffect($bid){
	global $_pm;
	$_pm['mem'] -> del('format_user_zhuangbei_'.$bid);
	//�ҳ��˳�һ�������ļ���װ��
	$sql = 'SELECT id,pid,plus_tms_eft,F_item_hole_info FROM userbag WHERE sums > 0 AND zbpets = '.$bid;
	$parr = $_pm['mysql'] -> getRecords($sql);
	if (!is_array($parr)){
		$str = 'ac:0';
		$_pm['mem'] -> set(array('k'=>'format_user_zhuangbei_'.$bid,'v'=>$str));
		return;
	}
	//$props = unserialize($_pm['mem']->get('db_propsid'));
	foreach ($parr as $pv){
		//$rs = $props[$pv['pid']];
		$rs = getBasePropsInfoById($pv['pid']);
		$addplus = explode(',',$pv['plus_tms_eft']);//print_r($addplus);exit;//ǿ��Ч��
		$rs['addplus'] = $addplus[1];
		
		if(!is_array($rs)) continue;
		//����Ч��
		
		
		if (!empty($rs['effect'])) {
			$effectarr = explode(':',$rs['effect']);
			if (is_array($effectarr)) {
				$arr[$effectarr[0]] += $effectarr[1];
				if (!empty($rs['addplus'])){
					$arr[$effectarr[0]] += $rs['addplus'];
				}
			}
			unset($effectarr);
		}
		//����Ч��
		if (!empty($rs['pluseffect'])) {
			$plusar = explode(',',$rs['pluseffect']);
			foreach ($plusar as $v){
				$plusarr = explode(':',$v);
				switch ($plusarr[0]){
					case 'ac':
						$arr['ac'] += $plusarr[1];
						break;
					case 'mc':
						$arr['mc'] += $plusarr[1];
						break;
					case 'hp':
						$arr['hp'] += $plusarr[1];
						break;
					case 'mp':
						$arr['mp'] += $plusarr[1];
						break;
					case 'speed':
						$arr['speed'] += $plusarr[1];
						break;
					case 'hits':
						$arr['hits'] += $plusarr[1];
						break;
					case 'miss':
						$arr['miss'] += $plusarr[1];
						break;
					case 'hprate':
						$num = intval($plusarr[1]);
						$arr['hprate'] += $num;
						unset($num);
						break;
					case 'mprate':
						$num = intval($plusarr[1]);
						$arr['mprate'] += $num;
						unset($num);
						break;
					case 'acrate':
						$num = intval($plusarr[1]);
						$arr['acrate'] += $num;
						unset($num);
						break;
					case 'mcrate':
						$num = intval($plusarr[1]);
						$arr['mcrate'] += $num;
						unset($num);
						break;
					case 'hitsrate':
						$num = intval($plusarr[1]);
						$arr['hitsrate'] += $num;
						unset($num);
						break;
					case 'missrate':
						$num = intval($plusarr[1]);
						$arr['missrate'] += $num;
						unset($num);
						break;
					case 'speedrate':
						$num = intval($plusarr[1]);
						$arr['speedrate'] += $num;
						unset($num);
						break;
					case 'sdmp'://���ܵ��˺���X%��MP����
						$num = intval($plusarr[1]);
						$arr['sdmp'] += $num;
						unset($num);
						break;
					case 'szmp'://���ܵ��˺���X%ת��ΪMP
						$num = intval($plusarr[1]);
						$arr['szmp'] += $num;
						unset($num);
						break;
					case 'dxsh'://�˺�����
						$num = intval($plusarr[1]);
						$arr['dxsh'] += $num;
						unset($num);
						break;
					case 'hitshp'://������ȡ�˺���X%ת��Ϊ����HP
						$num = intval($plusarr[1]);
						$arr['hitshp'] += $num;
						unset($num);
						break;
					case 'hitsmp':
						$num = intval($plusarr[1]);
						$arr['hitsmp'] += $num;
						unset($num);
						break;
					case 'shjs'://�Ե�����ɵ��˺�����X%
						$num = intval($plusarr[1]);
						$arr['shjs'] += $num;
						unset($num);
						break;
					case 'addmoney'://ս��ʤ����ý�Ǯ����X��
						$arr['addmoney'] += $plusarr[1];
						break;
				}
			}
		}
		//��Ƕ(��ʯ����)
		$sql = " SELECT F_item_hole_info FROM userbag WHERE  id = '".$pv['id']."'";
		$item_hole_info = $_pm['mysql'] -> getOneRecord($sql);
		if( isset($item_hole_info) && !empty($item_hole_info) )
		{
			$mid_arr = explode(',',$item_hole_info['F_item_hole_info']);
			foreach( $mid_arr as $info )
			{
				$hole_info = explode(':',$info);
				switch ($hole_info[0])
				{
					case 'ac' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['acrate'] += $num;
						unset($num);
						break;
					}
					case 'mc' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['mcrate'] += $num;
						unset($num);
						break;
					}
					case 'hits' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['hitsrate'] += $num;
						unset($num);
						break;
					}
					case 'miss' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['missrate'] += $num;
						unset($num);
						break;
					}
					case 'hp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['hprate'] += $num;
						unset($num);
						break;
					}
					case 'mp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['mprate'] += $num;
						unset($num);
						break;
					}
					case 'speed' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['speedrate'] += $num;
						unset($num);
						break;
					}
					case 'sdmp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['sdmp'] += $num;
						unset($num);
						break;
					}
					case 'szmp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['szmp'] += $num;
						unset($num);
						break;
					}
					case 'dxsh' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['dxsh'] += $num;
						unset($num);
						break;
					}
					case 'shjs':
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['shjs'] += $num;
						unset($num);
						break;
					}
					case 'crit' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['crit'] += $num;
						unset($num);
						break;
					}
					case 'hitshp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['hitshp'] += $num;
						unset($num);
						break;
					}
					case 'hitsmp' :
					{
						$num = intval(substr($hole_info[1],0,-1));
						$arr['hitsmp'] += $num;
						unset($num);
						break;
					}
				}
			}
		}
		unset($plusar,$plusarr,$v);
		//��װЧ��
		
		if (!empty($rs['serieseffect']) && !empty($bid)) {
			$series = explode(':',$rs['series']);
			$seriesid = explode('|',$series[1]);//����װ������id
			$check = 0;
			if (is_array($_SESSION[$bid.'series'])) {
				foreach ($_SESSION[$bid.'series'] as $bv){
					if (in_array($bv,$seriesid)){
						$check = 1;//�Ѿ�����
						break;
					}
				}
			}
			if ($check != 1) {
				$ids = implode(',',$seriesid);
				$sql = "SELECT count(id) as countid FROM userbag WHERE pid in ($ids) and uid = {$_SESSION['id']} and zbpets = $bid";
				$idarr = $_pm['mysql'] -> getOneRecord($sql);
				$readynum = $idarr['countid'];//�˳�װ���м���
				if (!is_array($idarr)) {
					return false;
				}
				$seriesar = explode(',',$rs['serieseffect']);
				foreach ($seriesar as $k => $v){
					$j = $k + 1;
					if ($j <= $readynum) {
							$seriesarr = explode(':',$v);
							switch ($seriesarr[0]){
								case 'ac':
									$arr['ac'] += $seriesarr[1];
									break;
								case 'mc':
									$arr['mc'] += $seriesarr[1];
									break;
								case 'hp':
									$arr['hp'] += $seriesarr[1];
									break;
								case 'mp':
									$arr['mp'] += $seriesarr[1];
									break;
								case 'speed':
									$arr['speed'] += $seriesarr[1];
									break;
								case 'hits':
									$arr['hits'] += $seriesarr[1];
									break;
								case 'miss':
									$arr['miss'] += $seriesarr[0];
									break;
								case 'hprate':
									$num = intval($seriesarr[1]);
									$arr['hprate'] += $num;
									unset($num);
									break;
								case 'mprate':
									$num = intval($seriesarr[1]);
									$arr['mprate'] += $num;
									unset($num);
									break;
								case 'acrate':
									$num = intval($seriesarr[1]);
									$arr['acrate'] += $num;
									unset($num);
									break;
								case 'mcrate':
									$num = intval($seriesarr[1]);
									$arr['mcrate'] += $num;
									unset($num);
									break;
								case 'hitsrate':
									$num = intval($seriesarr[1]);
									$arr['hitsrate'] += $num;
									unset($num);
									break;
								case 'speedrate':
									$num = intval($seriesarr[1]);
									$arr['speedrate'] += $num;
									unset($num);
									break;
								case 'missrate':
									$num = intval($seriesarr[1]);
									$arr['missrate'] += $num;
									unset($num);
									break;
								case 'dxsh':
									$num = intval($seriesarr[1]);
									$arr['dxsh'] += $num;
									unset($num);
									break;
								case 'hitshp':
									$num = intval($seriesarr[1]);
									$arr['hitshp'] += $num;
									unset($num);
									break;
								case 'hitsmp':
									$num = intval($seriesarr[1]);
									$arr['hitsmp'] += $num;
									unset($num);
									break;
								case 'time':
									$num = intval($seriesarr[1]);
									$arr['time'] += $num;
									unset($num);
									break;
								case 'shjs':
									$num = intval($seriesarr[1]);
									$arr['shjs'] += $num;
									unset($num);
									break;
								case 'addmoney':
									$arr['addmoney'] += $seriesarr[1];
									break;
							}
					}
				}
				unset($k,$v);
			}
			$_SESSION[$bid.'series'][] = $rs['id'];
		}
		unset($rs);
	}
	if( $arr['dxsh'] >= 70 )
	{
		$arr['dxsh'] = 70;
	}
	
	if(is_array($arr)){
		$memstr = "";
		foreach($arr as $k => $v){
			if(empty($v)) continue;
			if(empty($memstr)) $memstr = $k.':'.$v;
			else $memstr .= ','.$k.':'.$v;
		}
		$_pm['mem'] -> set(array('k'=>'format_user_zhuangbei_'.$bid,'v'=>$memstr));
	}
	unset($_SESSION[$bid.'series']);
}


function curl($url,$port=80){
	$post = 1;
	$returntransfer = 1;
	$header = 0;
	$nobody = 0;
	$followlocation = 1;
	
	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
						CURLOPT_HEADER => $header,
						CURLOPT_NOBODY => $nobody,
						CURLOPT_PORT => $port,
						CURLOPT_POST => $post,
						CURLOPT_POSTFIELDS => $request,
						CURLOPT_RETURNTRANSFER => $returntransfer,
						CURLOPT_FOLLOWLOCATION => $followlocation,
						CURLOPT_COOKIEJAR => $cookie_jar,
						CURLOPT_COOKIEFILE => $cookie_jar,
						CURLOPT_REFERER => $url
						);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

//��Ƭ
function card(){
	global $_pm;
	$time = date('YmdHi');
	$sql = "SELECT id,npcmsg,flag FROM card WHERE starttime <= $time and endtime >= $time";
	$arr = $_pm['mysql'] -> getRecords($sql);
	if(!is_array($arr)){
		return false;
	}
	
	
	$str = false;
	foreach($arr as $v){
		if(!empty($v)){
			if($v['flag'] == 1){
				$sql = "SELECT uid FROM card_info WHERE cardtype = {$v['id']} and uid = {$_SESSION['id']}";
				$newarr = $_pm['mysql'] -> getOneRecord($sql);
				if(is_array($newarr)){
					continue;
				}
			}
			//<li class="r0'.$t['color'].'"><a href="#"><span onclick="javascript:OpenLogin(1,'.$t['id'].','.$num.','.($taskid?1:0).')">'.$t['title'].'</span></a></li>
			$str .= '<li class="r04"><a href="#"><span onclick="javascript:card('.$v['id'].');">'.$v['npcmsg'].'</span></a></li>';
			//$str .= '<font color="red"><span onclick="javascript:card('.$v['id'].');" style="cursor:pointer;">'.$v['npcmsg'].'</span></font><br />';
		}
	}
	return $str;
}


function newcard(){
	global $_pm;
	$time = date('YmdHi');
	$sql = "SELECT id,days FROM timeconfig WHERE starttime <= $time and endtime >= $time AND titles='newcard'";
	$arr = $_pm['mysql'] -> getRecords($sql);
	if(!is_array($arr)){
		return false;
	}
	
	
	$str = false;
	foreach($arr as $v){
		if(empty($v)){
			continue;
			}
			$cname = explode('|',$v['days']);
			if(!empty($cname[1])){
				$cid = $cname[1];
			}else{
				$cid = 0;
			}
			$str .= '<li class="r04"><a href="#"><span onclick="javascript:card(\''.$cid.'\');">'.$cname[0].'</span></a></li>';
	}
	return $str;
}
/*
������timeconfig =>id
return:string
*/
function getprizelist($msgs)
{
	global $_pm,$user;
	$user = $_pm['user']->getUserById($_SESSION['id']);
	$memtimeconfig = unserialize($_pm['mem']->get('db_timeconfignew'));
	$arr = $memtimeconfig['recallPlayer'];

		$nums = count($arr);
		for($i=0;$i<$nums;$i++)
		{
			if($arr[$i]['Id']==$msgs)
			{
				$one = explode(',', $arr[$i]['days']);//array(0->111:2,1->112:4)
			}
		}
		$idlist = '';
		foreach($one as $a => $b)
		{
			$b_value = explode(':', $b);
			//$memtimeconfig = unserialize($_pm['mem']->get('db_propsid'));
			//$arr_name = $memtimeconfig[$b_value[0]];
			$arr_name = getBasePropsInfoById($b_value[0]);
			$prize .=  $arr_name['name'].":".$b_value[1]."��<br />";
		}
		
					$string = "<b>�������������Ʒ��������Ԥ����Ӧ������λ����</b><br/><br/>".$prize."<br /><a href='javascript:getprize(".$msgs.");'><span style='text-decoration:none;'>ȷ����ȡ</span></a>&nbsp;&nbsp;&nbsp;<a href='javascript:close_window();'><span style='text-decoration:none;'>�ر�</span></a>";
				return $string;
}
//�ٻ��������  
/*
return:array(id) =>(timeconfig->id)
*/
function zhaohui()//return ,35,34  (timeconfig->id)
{
		$num = 0;
		global $_pm,$user;
		$user = $_pm['user']->getUserById($_SESSION['id']);
		$memtimeconfig = unserialize($_pm['mem']->get('db_timeconfignew'));
		$arr = $memtimeconfig['recallPlayer'];
		/*
		Array ( [0] => Array ( [Id] => 35 [titles] => recallPlayer [days] => 1:2,2:4 [starttime] => 1 [endtime] => 94,5,10 ) [1] => Array ( [Id] => 34 [titles] => recallPlayer [days] => 111:2,12:4 [starttime] => 1 [endtime] => 5,2,0 ) ) 
		*/
		$ct = count($arr);//�м�����ѭ������
		$msg = false;
		for($i=0;$i<$ct;$i++)// 0 1
		{
			if($arr[$i]['starttime']==0)
			{
				continue;
			}
			else
			{
				$panduan = explode(',', $arr[$i]['endtime']);
				$lasttime = $panduan[1] * 24 * 60 * 60;
				$time = time()-$lasttime;			
				$sql_level = "select level from userbb where id={$user['mbid']}";
				$arr_level = $_pm['mysql'] -> getOneRecord($sql_level);//�ȼ�
				if($arr_level['level']<$panduan[0])
				{
					continue;
				}
				if($_SESSION['lastvtime']>$time)
				{

					continue;
				}
				if($panduan[2]!=0)
				{
					$sql_yb = "select sum(yb) tl from yblog where nickname='{$user['name']}'";
					$arr_yb = $_pm['mysql'] -> getOneRecord($sql_yb);//Ԫ��	
					if($arr_yb['tl']<$panduan[2])
					{
						continue;
					}
				}
				//��� �Ƿ� �Ѿ� ��ȡ����
				$sql_log = "select taskid from tasklog where uid={$user['id']} and taskid>9999";
				$arr_log = $_pm['mysql'] -> getRecords($sql_log);	//Array ( [0] => Array ( [taskid] => 999922 ) [1] => Array ( [taskid] => 999921 ) ) 		
				if(is_array($arr_log))
				{
					foreach($arr_log as $key => $value)
					{
						$shu = 999900;
						$flag[] = $value['taskid']-$shu;//34 35 36 37 0 =>
					}
					if(is_array($flag))
					{
						if(in_array($arr[$i]['Id'],$flag))
						{
							continue;
						}
					}
				}
				

			}//else ����
			$msg .= ','.$arr[$i]['Id'];
		}//forѭ������
		return $msg;	
		
}

function del_bag_expire(){
	global $_pm;
	$arr = array();
	$sql = "SELECT userbag.id as ubid,userbag.pid as pid,userbag.sums as sums,userbag.stime as stime,userbag.zbpets as zbpets FROM userbag,props WHERE props.expire > 0 AND uid = {$_SESSION['id']} AND userbag.sums > 0 AND userbag.pid = props.id AND  userbag.stime<=(".time()." - props.expire)";
	$arr = $_pm['mysql'] -> getRecords($sql);
	if(is_array($arr) && count($arr) > 0){
		foreach($arr as $vv){
			$str = 'pid:'.$vv['pid'].'sums:'.$vv['sums'].'stime:'.date('Y-m-d H:i',$vv['stime']);
			$_pm['mysql'] -> query("INSERT INTO gamelog (seller,buyer,ptime,pnote,vary) VALUES({$_SESSION['id']},{$_SESSION['id']},".time().",'$str',222)");
			$sql = "UPDATE userbag SET sums = 0 WHERE id = {$vv['ubid']}";
			$_pm['mysql'] -> query($sql);
			$_pm['mem'] -> del('format_user_zhuangbei_'.$vv['zbpets']);
			$_pm['mem']->set(array("k"=>"User_bb_equip_changed_".$vv['zbpets'].'_'.$_SESSION['id'],"v"=>1));
		}
	}
}

function tgtgw(){
	global $_pm;
	$carr = $_pm['mysql'] -> getOneRecord("SELECT gid FROM tgt WHERE uid = {$_SESSION['id']}");
	if(is_array($carr)){
		return false;
	}
	$tgcheck = $_pm['mysql'] -> getOneRecord("SELECT tgt,tgttime FROM player_ext WHERE uid = {$_SESSION['id']}");
	$tgnum = $tgcheck['tgt'] + 1;
	
	if($tgnum > 55){  //TT�ܲ�������
		$_pm['mysql'] -> query("UPDATE player_ext SET tgt=0,tgttime = ".time()." WHERE uid = {$_SESSION['id']}");
		return 'a';
	}
	$tggwarr = $_pm['mysql'] -> getRecords("SELECT id,gpc,drops FROM c_gpc WHERE boss = $tgnum");
	$tgtgwmax = count($tggwarr) - 1;
	$tgtgwnum = rand(0,$tgtgwmax);
	$tggw = $tggwarr[$tgtgwnum];
	$arr = explode(',',$tggw['gpc']);
	foreach($arr as $v){
		$_pm['mysql'] -> query("INSERT INTO tgt (uid,gid,boss) VALUES ({$_SESSION['id']},$v,{$tggw['id']})");
	}
}

function getTasks($task,$user,$petsAll,$bbs)
{
	//echo '$taskid='.$taskid.'$user='.count($user).',$petsAll='.count($petsAll).',$bbs='.count($bbs).',$memtask='.count($memtask)."\n";
	global $_pm;
	$taskid = $task['id'];
	if(empty($taskid))
	{
		//die("���ݴ���".__LINE__);
		return false;
	}
	//$a = count($memtask);
	
	
	$taskinfo = $task;

	//print_r($taskinfo);
	if(!is_array($taskinfo)) //die("���ݴ���".__LINE__);
		return false;
	
	//ֻ�����һ��,�ȼ����Ƶ�����
	if(!empty($taskinfo['limitlv']))
	{
		$limitarr = explode(",",$taskinfo['limitlv']);//lv:1|0,jifen:1,cishu:1:1
		if(is_array($limitarr))
		{
			foreach($limitarr as $v)
			{
				$limitarrs = explode(":",$v);
				switch($limitarrs[0])
				{
					case "lv":
						foreach($petsAll as $bb)
						{
							if($bb['id'] == $user['mbid'])
							{
								$blv = $bb['level'];
							}
						}
						if(empty($blv))
						{
							//die("���ȵ�����������ս��");
							return false;
						}
						$lvarr = explode("|",$limitarrs[1]);
						if($lvarr[1] == "0")
						{
							if($blv < $lvarr[0])
							{
								//die("���ĵȼ��������ܴ�����");
								return false;
							}
						}
						else
						{
							if($blv < $lvarr[0] || $blv > $lvarr[1])
							{
								//die("���ĵȼ����ڿɽӴ�����Χ֮�ڣ�");
								return false;
							}
						}
						break;
					
					
					case "xfyb"://�ֶθ�ʽxfyb��xxxxxxxx|xxxxxxxx��xxxx|yyyy����ĳ��ĳ��ĳ�յ�ĳ��ĳ��ĳ����Ҫ��������xxxx��yyyy�����Ҳ��ܽ�������
						$sql="select yb,buytime from yblog where nickname='{$user['name']}'";
						$t=$_pm['mysql'] -> getRecords($sql);
						if(!is_array($t))
						{
							//die("��δ����Ԫ�����ѣ��޷���ȡ����!");
							return false;
						}else{
							
							$xfyb=explode(";",$limitarrs[1]);
							$xfyb1=explode("|",$xfyb[0]);//ʱ���
							$xfyb2=explode("|",$xfyb[1]);//Ԫ�����Ѷ�
							$sum_yb=0;
							if(is_array($xfyb2) && is_array($xfyb1)){
								foreach($t as $k=>$v){
									if(date(Ymd,$v['buytime'])>=$xfyb1[0] && date(Ymd,$v['buytime'])<=$xfyb1[1]){
										$sum_yb+=$v['yb'];
									}
								}
								
								$taskidxfyb=$_pm['mysql']->getRecords('select id,limitlv from task where  left(limitlv,4)="xfyb"');
								$taskidxfybs=array();
								$taskidxfybinfos=array();
								foreach($taskidxfyb as $row)
								{
									$taskidxfybs[$row['id']]=$row['id'];
									$taskidxfybinfos[$row['id']]=$row['limitlv'];
								}
								
								$mytasklogssql='select taskid from tasklog where uid='.$_SESSION['id'].' and taskid in ('.implode(',',array_values($taskidxfybs)).');';
								$mytasklogs=$_pm['mysql']->getRecords($mytasklogssql);
								$myusedtaskyblog=0;
								if(!empty($mytasklogs)){
									foreach($mytasklogs as $tlog)
									{
										$strtlog=explode(';',$taskidxfybinfos[$tlog['taskid']]);
										$yblogstr=explode('|',$strtlog[1]);
										$strtlog=explode(':',$strtlog[0]);
										$strtlog=explode('|',$strtlog[1]);
										if(intval($strtlog[1])>$xfyb1[0])//�Ѿ��������Ľ���ʱ����ڵ�ǰ����Ŀ�ʼʱ��
										{
											$myusedtaskyblog+=intval($yblogstr[0]);
										}
									}
								}								
								$sum_yb-=$myusedtaskyblog;
								
								if($xfyb2[1]==0){
									if($sum_yb<=$xfyb2[0]){
										//die("����Ԫ��������{$xfyb2[0]}���ϲſ���ȡ������");
										return false;
									}
								}elseif($xfyb2[0]>0 && $xfyb2[1]>=0){
									if($sum_yb<=$xfyb2[0] || $sum_yb>=$xfyb2[1] ){
										//die("����Ԫ��������������ȡ������ķ�Χ�ڣ�");
										return false;
									}
								}else{
									//die("��ȡ�������");
									return false;
								}
							}else{
								//die("��ȡ�������");
								return false;
							}
						}
						break;
					
					case "xfsj":// task����limitlv�ֶ������xfsj������ʽxfsj��xxxxxxxx|xxxxxxxx����ĳ��ĳ��ĳ�յ�ĳ��ĳ��ĳ����Ҫ���Ѳ��ܽ�������
						$jc=0;
						$xfsj=explode("|",$limitarrs[1]);
						$sql="select id,yb,buytime from yblog where nickname='{$user['name']}'";
						$t=$_pm['mysql'] -> getRecords($sql);
						$check = $_pm['mysql'] -> getOneRecord("select time from tasklog where uid = {$_SESSION['id']} and taskid = 88888 order by id desc limit 1");
						$count = count($t) - 1;
						if(is_array($check) && $t[$count]['id'] <= $check['time']){
							//die('���ʱ����û���µ����Ѽ�¼�����ܽ��ܴ�����');
							return false;
						}
						
						if(!is_array($t))
						{
							//die("��δ�������ѣ��޷���ȡ����!");
							return false;
						}else{
							foreach($t as $k=>$v){
							 //�����⵽������������������ֱ���������������
							
								if(date(Ymd,$v['buytime'])>=$xfsj[0] && date(Ymd,$v['buytime'])<=$xfsj[1]){
									$jc=1;
									break;
								}
							}
						}
						if($jc==0){
							//die("��δ�������ѣ��޷���ȡ����!{$tt} |{$xfsj[0]} | {$xfsj[1]}");
							return false;
						}
						break;	
						
					case "cishu":
						//cishu:X:Y ���������жϣ������������Y���Ѿ������X�Σ����޷��������
						$time = time() - $limitarrs[2] * 24 * 3600;
						$today = strtotime(date('Ymd',time()));
						$sql = "SELECT count(*) sl FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$taskid} and time > {$today}";
						$arr = $_pm['mysql'] -> getOneRecord($sql);
						if(is_array($arr))
						{
							if($arr['sl'] >= $limitarrs[1])
							{
								//die("��{$limitarrs[2]}������ֻ�����$limitarrs[1]������");
								return false;
							}
						}
						break;
						
					case "czl"://�ж���ս����ĳɳ�ֵ�Ƿ񹻸���ܴ�����
						$lvarr = explode("|",$limitarrs[1]);
						$sql = "SELECT czl FROM userbb WHERE id=".$user['mbid'];
						//$petsmain=$_pm['mysql'] -> getOneRecord($sql);
						foreach($petsAll as $pv){
							$petsmain = $pv;
						}
					
						if($lvarr[1]==0){
							if($lvarr[0]>$petsmain['czl']){
								//die("�ó���ɳ�ֵΪ".$petsmain['czl']."���޷���ȡ������!");
								return false;
							}
						}
						if($lvarr[1]>0){
							if(!($lvarr[0]<=$petsmain['czl'] && $lvarr[1]>=$petsmain['czl'])){
								//die("�ó���ɳ�ֵ���ڴ�����Χ�ڣ��޷���ȡ����");
								return false;
							}
						}
						break;	
					case "comself"://comself:X ����������ս����IDΪX������Խ�������
						$abcarr = explode("|",$limitarrs[1]);
						$bbarr = "";
						foreach($petsAll as $pv)
						{
							if($pv['id'] == $user['mbid'])
							{
								$bname = $pv['name'];
							}
						}
						$bnamearr = array();
						foreach($abcarr as $av)
						{
							foreach($bbs as $bbav)
							{
								if($bbav['id'] == $av)
								{
									$bnamearr[] = $bbav['name'];
									break;
								}
							}
						}
						if(!in_array($bname,$bnamearr))
						{
							//die("���ĵ�ǰ���費�ܽ��ܴ�����");
							return false;
						}
						break;
					case "jifen"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['score'] < $limitarrs[1])
						{
							//die("���ĵ�ǰ���ֲ����Ӵ�����");
							return false;
						}
						break;
					case "vip"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['vip'] < $limitarrs[1])
						{
							//die("����vip���ֲ����Ӵ�����");
							return false;
						}
						break;
					case 'merge':
						$merge = $_pm['mysql'] -> getOneRecord("SELECT merge FROM player_ext WHERE uid = {$_SESSION['id']}");
						if($merge['merge'] < 1){
							//die('��Ŀǰδ�飬���ܽ��ܴ�����');
							return false;
						}
				}
			}
		}
	}
	$arr = "";
	if(empty($taskinfo['cid']))
	{
		$sql = "SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$taskid}";
		$arr = $_pm['mysql'] -> getOneRecord($sql);
	}
	if(is_array($arr))
	{
		//die("������ֻ�ܽ���һ��~��");
		return false;
	}
	//ֻ�����һ�ε��������������
	
	$usertaskarr = $_pm['mysql'] -> getRecords("SELECT taskid FROM task_accept WHERE uid = {$_SESSION['id']}");
	if(is_array($usertaskarr)){
		foreach($usertaskarr as $v){
			$accept[] = $v['taskid'];
		}
		if(in_array($taskid,$accept)){
			//die("���Ѿ����ܴ�����");
			return false;
		}
		if(count($usertaskarr) >= 15){
			//die("���Ѿ�������15�����񣬳�����������ƣ�");
			return false;
		}
	}
	
	
	//echo "��ϲ�����ɹ����ܴ�����";
	return true;
}

//�ͻ�����ʵ��ʾ��Ϣ$msg,ִ�нű�$script�����$back=true���Զ�����ǰһ��ҳ��
function alert($msg,$script='',$back=false)
{
	if($back&&!empty($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']===false))
	{
		header('location:'.$_SERVER['HTTP_REFERER']);
		echo $msg;
	}
	echo '<script language="javascript">parent.Alert("'.$msg.'");'.$script.'</script>';
}

//��memcache�е�ĳ��һ�����������������ֶ�ά����ת�����ַ�����������
function memArr2Str($data,$key,$spFiled="`_`",$spLine="$+$",$suffix='str')
{
	global $_pm;
	//$data=$_pm['mem']->get($key);
	//if(!is_array($data)&&strlen($data)>3) $data=unserialize($data);
	$str='';
	$con='';
	
	if(count($data)>0){
		foreach($data as $v)
		{
			if(count($v)>0)
			{
				$str.=$con.implode($spFiled,$v);
				$con=$spLine;
			}
		}
	}
	$key.=$suffix;
	$_pm['mem']->setnsnc($key,$str);
	
}
?>
