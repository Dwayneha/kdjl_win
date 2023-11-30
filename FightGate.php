<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.08
*@Update Date: 2008.05.29
*@Usage:Fightting Function.getzbAttrib
*@Note: NO Add magic props.
  ��ģ����Ҫ���ܣ�
  1)���㹥����������BB�͹��
  2)ͬʱ��¼�û�ս���Ĺ������ݣ�����HP,MP,
  3)������Ʒ�����ݻ��ʣ�
*/
session_start();
error_reporting(E_ALL & ~E_NOTICE );
header('Content-Type:text/html;charset=GBK');
define(MEM_BOSS_KEY,	$_SESSION['id'] . 'boss');
define(MEM_FIGHT_KEY,	$_SESSION['id'] . 'fight');

require_once('../config/config.game.php');
if( $_SESSION['first_in'] == 2 || $_SESSION['first_in'] == 3 )
{
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");	
}
$_SESSION['first_in'] = 2;	//������
secStart($_pm['mem']);
$flagteam=false;
$isMyTurn=false;
if(isset($_SESSION['team_id'])){
	require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
	$s=new socketmsg();
	$team=new team($_SESSION['team_id'],&$s);
	$team->checkMyTeam();
	$teamInfo=$team->getTeamInfo();
	$ct=0;
	foreach($teamInfo['members'] as $amem)
	{
		if($amem['living']==1&&$amem['state']==1)
		{
			if($amem['uid']==$_SESSION['id'])
			{
				$isMyTurn=true;
			}
			//break;
		}
		if($amem['state']==1)
		{
			$ct++;
		}
	}
	if($ct<2)
	{
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		$_SESSION['first_in'] = 3;
		die('TEAMERROR');
	}
	if(!$isMyTurn){
		$teamState=$team->getTeamState();
		if(!empty($teamState['fightgate_html'])){
			echo '<!--fg '.__LINE__.'-->'.$teamState['fightgate_html'];
		}else{
			echo 'TEAMERROR';
		}
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		$_SESSION['first_in'] = 3;
		die();
	}else{
		$flagteam=true;
	}
}

//�������
$time = time();
$sql = "SELECT time FROM fight_log WHERE uid = {$_SESSION['id']} and vary = 1";
$timearr = $_pm['mysql'] -> getOneRecord($sql);
if(is_array($timearr)){
	$ctime = $time - $timearr['time'];
	if($ctime < 2){
		$_SESSION['id'] = '';
	}else{
		$_pm['mysql'] -> query("UPDATE fight_log SET time = ".time()." WHERE uid = {$_SESSION['id']} and vary = 1");
	}
}else{
	$_pm['mysql'] -> query("INSERT INTO fight_log (uid,time,vary) VALUES({$_SESSION['id']},".time().",1)");
}

//���������
$wgcheck = $_GET['checkwg']; 
if($wgcheck != 'checked'){
	$_SESSION['id'] = '';
}

$mmonsterContinueFlag = 'NOT';
/*###�Ƿ����ݼ�����###*/	
$user		= $_pm['user']->getUserById($_SESSION['id']);
if (!in_array($user['inmap'],$_game['map'])) stopUser(2);		// ��ͼ���


//����ս������ͳ��
/*$dh=date('YmdH');
$_pm['mysql'] -> query("insert into `logs` (id,sums) values ($dh,1) on duplicate key update sums=sums+1");*/
//$_pm['mysql'] -> query("update logs set sums = sums + 1 WHERE id = 1");



$id			= intval($_REQUEST['id']);		// 	����ID
$need_cold_skill_id_arr = array('319'=>'299','320'=>'299','321'=>'179','322'=>'179','323'=>'119');
/*��ȴ���ܣ�������fight.js,fbfight.js,fbfightGate.php,FightGate.php�м��ܴ��ݱ���һֱ����Ϊ����sission�ķ���������֤��Ϊ�ų��ӳ٣��������1��*/
if ( isset($need_cold_skill_id_arr[$id]) )
{
	$cold_time = $need_cold_skill_id_arr[$id];
	$key = $id."_".$_SESSION['id'];
	if( $_SESSION[$key] )
	{
		if( time()- $_SESSION[$key] > $cold_time  )
		{
			unset($_SESSION[$key]);
			$_SESSION[$key] = time();
		}
		else
		{
			echo "SKILLCOLD";
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
			$_SESSION['first_in'] = 3;
			die();
		}
	}
	else
	{
		$_SESSION[$key] = time();
	}
}

//$gid		= intval($_REQUEST['g']);	 	//  ����ID
$db_bb		= array();	//	���ݿ�2008-10-8�б�����ԭʼ���ԡ�

$fight		= $_SESSION['fight'.$_SESSION['id']];

//if ( $fight['gid']==0 ){exit;}
$memKey= "last_update_user_fight_time_".$_SESSION['id'];
$timeMem = unserialize($_pm['mem']->get($memKey));
if($timeMem){//��ֹ���٣����ȡ���˵�ǰս���Ĳ����Ϳ�����������������ս����
	if($timeMem+2>time()){
		echo '0,0,0#0,0#����̫�죡#-';
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		$_SESSION['first_in'] = 3;
		die();
		//sleep(intval($timeMem-time()));
	}
}
$_pm['mem']->set(array("k"=>$memKey,"v"=>time()));
/** �Ƿ����ݼ�⡣*/

//ʹ���Զ���Ǽ�Ѫ�����޸������
if(intval($_SESSION['GoToCity'])>0){
	stopUser2(50);//,true
	setcookie("PHPSESSID",'YOUAREBAD');
	unset($_SESSION['username']);
	unset($_SESSION['licenseid']);
	$drops='���� �����ˣ�����1';
	$word='';
	header('Content-Type:text/html;charset=GBK');
	echo '0,0,0#0,0#' . $drops . '#' . $word;
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	$_SESSION['first_in'] = 3;
	exit;
}
$doubleexp = unserialize($_pm['mem']->get(MEM_TIME_KEY));
//wuping��2013��1��11���޸ģ��ĳ�ֱ�Ӵ����ݿ��в�ѯ
//$memgpcid = unserialize($_pm['mem']->get('db_gpcid'));
$memskillsysid = unserialize($_pm['mem']->get('db_skillsysid'));

if($flagteam&&empty($fight['gid'])){
	$teamState=$team->getTeamState();
	/*echo '
	$fight='.print_r($fight,1).'
	$teamState[cur_monster]='.print_r($teamState['cur_monster'],1).'
	$teamState[monsters]='.print_r($teamState['monsters'],1).'
	';*/
	$_SESSION["fight".$_SESSION['id']]=$teamState['cur_monster'];
	$_SESSION["fight".$_SESSION['id']]['uid']=$_SESSION['id'];
	$fight=$_SESSION["fight".$_SESSION['id']];
	$_SESSION['gwcdie'.$_SESSION['id']] = $_SESSION["fight".$_SESSION['id']]['gid'];
}
if(empty($fight['gid'])){
	//header("refresh:1;url=Fight_Mod.php?p={$_SESSION['mbid']}");
	//echo 'fg $flagteam='.$flagteam.','.print_r($fight,1).','.__LINE__.'-'.$_SESSION['mbid'];
	$__gw=false;
	if(count($teamState['monsters'])>0)
	{
		foreach($teamState['monsters'] as $k=>$v)
		{
			if(!empty($v))
			{
				$__gw=$v;
				break;
			}else{
				unset($teamState['monsters'][$k]);
			}
		}
	}

	if($__gw)
	{
		$_SESSION['fight'.$_SESSION['id']]	= array(
					'uid'=>$_SESSION['id'],
					'bid'=>$_SESSION['mbid'],
					'gid'=>$__gw['id'],
					'hp' =>$__gw['hp'],
					'mp' =>$__gw['mp'],
					'fuzu'=>0,
					'fatting'=>1,
					'boss'=>$__gw['boss'],
					'ftime'=>time()-11
					);
		$_SESSION['gwcdie'.$_SESSION['id']]=$__gw['id'];
	}
	else {	
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
			$_SESSION['first_in'] = 3;
		exit("1,1,1,��ͨ����,#0,1,��ͨ����#��þ��飺0<br/>��ý�ң�0+0 ��
					  <br/>������0<br/>�����Ʒ���ޣ�<br/>���⽱������<br/>##1,0,0#NOT");
	}
}

if ($fight['gid']==0){
	$dh=date('YmdH');
	$_pm['mysql'] -> query("insert into `logs1` (id,sums) values ($dh,1) on duplicate key update sums=sums+1");
	
	$drops='���� �����ˣ�����2';
	$word='';
	header('Content-Type:text/html;charset=GBK'); 
	echo '0,0,0#0,0#' . $drops . '#' . $word;	
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	$_SESSION['first_in'] = 3;
//stopUser(10);
	exit;
} // ˢ��
if(empty($_SESSION['gwcdie'.$_SESSION['id']]) || $_SESSION['gwcdie'.$_SESSION['id']] != $_SESSION["fight".$_SESSION['id']]['gid'])
{
	$_SESSION['id'] = "";
}
if (bossCheck($_SESSION["fight".$_SESSION['id']]['gid']) === false) {
	if($flagteam)
	{
		$team->clearTeamState();
		$mems=array();
		if(!empty($teaminfo['members'])){
			foreach($teaminfo['members'] as $row)
			{
				$mems[]=$row['uid'];				
			}
		}
		$team->snotice('getTeamFightMod',NULL,$mems);
		$_SESSION['fight'.$_SESSION['id']]['ftime']=0;
	}
	$drops='BOSS���� �����ˣ�����3';
	$word='';
	header('Content-Type:text/html;charset=GBK'); 
	echo '0,0,0#0,0#' . $drops . '#' . $word;
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	$_SESSION['first_in'] = 3;
	exit;
	//stopUser(3);  // �Ƿ�ˢBOSS��
}



$gwdeadFlag = false;



// auto fit check
if($user['autofitflag']==1 && $user['maxautofitsum']<=0 && $user['sysautosum']<=0)
{
	$user['maxautofitsum']=0;
	$user['sysautosum']=0;
	header('Content-Type:text/html;charset=GBK'); 
	echo 'autoend';	
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	$_SESSION['first_in'] = 3;
	exit();
}

foreach($doubleexp as $v){
	if($v['titles'] == "exp"){
		$newdoubleexparr = $v;	
	}
}
$nowtime = date("YmdHis");
/* Fix read database fail!*/
if(intval($_SESSION['id'])<1||intval($user['fightbb'])<1)
{
	$recover=$_pm['mysql']->getOneRecord('select fightbb,mbid from player where id='.$_SESSION['id']);
	if($recover)
	{
		if($recover['fightbb']>0)
		{
			$user['fightbb']=$recover['fightbb'];
		}else if($recover['mbid']>0){
			$user['fightbb']=$recover['mbid'];
		}else
		{	
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
			$_SESSION['first_in'] = 3;
			exit('1,1,1,��ͨ����,#0,2,��ͨ����#��þ��飺0<br/>��ý�ң�0+0 ��
					  <br/>������0<br/>�����Ʒ���ޣ�<br/>���⽱������<br/>##1,0,0#NOT');
		}
	}
	else
	{	
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		$_SESSION['first_in'] = 3;
		exit('1,1,1,��ͨ����,#0,3,��ͨ����#��þ��飺0<br/>��ý�ң�0+0 ��
					  <br/>������0<br/>�����Ʒ���ޣ�<br/>���⽱������<br/>##1,0,0#NOT');
	}
}
$_bb = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['fightbb']);//ս�����
if (!is_array($_bb))
{   
	$loop=true;
	$ct=0;
	while($loop)
	{
		$ct++;
		$_bb		 = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['fightbb']);		
		if (is_array($_bb)) break;
		
		if($ct>10)
		{
			$_SESSION['first_in'] = 3;
			 exit("Get BB Failed!");
		}
		
	}
}
if($id == 112){
	$_SESSION['id'] = '';
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	$_SESSION['first_in'] = 3;
	die('�Ƿ�������������ǿ�ƶ���(1)��');
}
$_sk		 = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],$id);/*SELECT s.*,
											  b.uid as uid
										 FROM userbb as b, skill as s
										WHERE b.uid={$uid} and b.id=s.bid and s.bid={$id} and s.sid={$sid}
										ORDER BY s.level  $uid,$id,$sid ��ȡ����*/
if(intval($_SESSION['id'])<1||intval($user['fightbb'])<1) 
{
	$_SESSION['first_in'] = 3;
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
	exit('$_bb[\'id\']='.$_bb['id'].',$_SESSION[\'id\']='.$_SESSION['id']);
}

/**��������*����鵽��ҵļ��ܲ���ȷʱ.������������Ϊԭʼ����  2009.06.24 kevin**/
if(!is_array($_sk))
$_sk = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],"1");
/*��������*/

if (!is_array($_sk))
{
	$loop=true;
	$ct=0;
	while($loop)
	{
		$ct++;
		$_sk		 = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],$id);
		if (is_array($_sk)) break;
		if($ct>10)
		{
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
			$_SESSION['first_in'] = 3;
			 exit("Get SKILL Failed!");
		}
		
	}
}
/*** fix end ***/
// Get bb info for fightting.
if (is_array($_bb) && is_array($_sk))
{	
	// Componse array .
	$rs = array_merge($_bb, array('s_name'  => $_sk['name'],
								's_level' => $_sk['level'],
								's_vary'  => $_sk['vary'],
								's_wx'	  => $_sk['wx'],
								's_value' => $_sk['value'],
								's_plus'  => $_sk['plus'],
								's_uhp'   => $_sk['uhp'],
								's_ump'   => $_sk['ump'],
								's_imgeft'   => $_sk['img']//����Ч����
							   )
					 );
}
else $rs = '';
//print_r($rs);
//get gwinfo.
//wuping��2013��1��11���޸ģ��ĳ�ֱ�Ӵ����ݿ��в�ѯ
//$grs =  $_pm['mysql']->getOneRecord('select id,name,level,hp,mp,ac,mc,speed,hits,miss,catchv,catchid,skill,imgstand,imgack,imgdie,droplist,exps,money,boss,wx,kx,activedroplist from gpc where id='.$_SESSION["fight".$_SESSION['id']]['gid'].' limit 1');
$grs = getGpcByGid($_SESSION["fight".$_SESSION['id']]['gid']);
//$grs = $memgpcid[$_SESSION["fight".$_SESSION['id']]['gid']];
/*$grs = $_pm['mem']->dataGet(array('k' => MEM_GPC_KEY, 
						 'v' => "if(\$rs['id'] == '{$gid}') \$ret=\$rs;"
					));*/
if(
	$grs['boss'] == 4	
)
{	
	$teamState=$team->getTeamState();
	if(
		!isset($teamState) || 
		!isset($teamState['team_fuben_flag']) || 
		!$teamState['team_fuben_flag']
	){
		unset($_SESSION['id']);
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		$_SESSION['first_in'] = 3;
		die("�Ƿ�������������ǿ�ƶ���(2)��");
	}
}

if (!is_array($grs)) $skid=1;
else
{
	$ar = split(",", $grs['skill']);
	foreach($ar as $k => $v)
	{
		$arr = split(":", $v);
		$alljn[$k] = $arr[0];
	}
	$skid = $alljn[rand(0, count($alljn)-1)];
	//$_SESSION['multi_monsters'.$_SESSION['id']] = 3;
	//$_SESSION['multi_monsters_boss_tgt_'.$_SESSION['id']] = 32;
	//echo $_SESSION['multi_monsters'.$_SESSION['id']].'<br />'.$_SESSION['multi_monsters_boss_tgt_'.$_SESSION['id']].'<br />';
	if($_SESSION['multi_monsters'.$_SESSION['id']] == 3 && $rs['wx'] == 6 && $_SESSION['multi_monsters_boss_tgt_'.$_SESSION['id']] > 30){//ͨ����30�����ϳ�����м��������⼼�ܵĹ�
		$grsjv = substr($grs['level'],-2);//echo $grs['level'].'<br />';
		//$grsjv = substr($grs['level'],-1);
		$randjv = rand(1,$grsjv);
		if($randjv == 1){
			$skid = 302;
		}//echo $grsjv.'<br />'.$randjv.'<br />'.$skid;
	}
}

// Gpc data	
//wuping��2013��1��11���޸ģ��ĳ�ֱ�Ӵ����ݿ��в�ѯ
//$v = $_pm['mysql']->getOneRecord('select id,name,level,hp,mp,ac,mc,speed,hits,miss,catchv,catchid,skill,imgstand,imgack,imgdie,droplist,exps,money,boss,wx,kx,activedroplist from gpc where id='.$_SESSION["fight".$_SESSION['id']]['gid'].' limit 1');
$v = getGpcByGid($_SESSION["fight".$_SESSION['id']]['gid']);
//$v = $memgpcid[$_SESSION["fight".$_SESSION['id']]['gid']];
$y = $memskillsysid[$skid];
if (is_array($v) && is_array($y))
{
	// Componse array .
	$gs = array_merge($v, array('s_name'  => $y['name'],
								's_wx'	  => $y['wx'],
								's_value' => $y['ackvalue'],
								's_plus'  => $y['plus'],
								's_uhp'   => $y['uhp'],
								's_ump'   => $y['ump'],
								's_id'	  => $y['id']
							   )
					 );
}
else $gs = '';

if(is_array($rs) && is_array($gs))  
{
	//=================== װ��Ч����ʼ =========================
	
	$db_bb = $rs;
	//########################
	// ����װ�����Ե�ս���С�
	#############################
	if($user['inmap']<=10){
		$att = unserialize($_pm['mem']->get('game_user1_zb_attrib_'.$_SESSION['id']));
		
		if(!is_array($att) || empty($att)){//���û��ȡ��
			$att = getzbAttrib($rs['id']);
			$_pm['mem']->setexpire(array('k'=>'game_user1_zb_attrib_'.$_SESSION['id'],'v'=>$att),3600);
			
		}
		
	}else{
		$att = getzbAttrib($rs['id']);	
	}
	
	
	$rs['ac']	+= $att['ac'];
	$rs['mc']	+= $att['mc'];
	$rs['hits'] += $att['hits'];
	$rs['speed']+= $att['speed'];
	$rs['miss']	+= $att['miss'];
	//ս��ҩƷ����Ч��
	$medicine_buff = $_pm['mysql'] -> getOneRecord(" SELECT F_Medicine_Buff FROM player_ext WHERE uid = '".$_SESSION['id']."'");
	if( isset($medicine_buff['F_Medicine_Buff']) && !empty($medicine_buff['F_Medicine_Buff']) )	//�������ս����buffҩ
	{
		$med_buff_arr_all = explode(',',$medicine_buff['F_Medicine_Buff']);
		foreach( $med_buff_arr_all as $info )
		{
			$med_buff_arr = explode(':',$info);	//addac:10
			switch($med_buff_arr[0])
			{
				case "addac" :
				{
					if( strstr($med_buff_arr[1],'%') )	//�аٷֺ�
					{
						$med_buff_arr[1] = substr($med_buff_arr[1],0,-1);	//ȥ�ٷֺ�
						$rs['ac'] = (1+$med_buff_arr[1]/100)*$rs['ac'];
					}
					else
					{
						$rs['ac'] += $med_buff_arr[1];
					}
					break;
				}
				case "addmc" :
				{
					if( strstr($med_buff_arr[1],'%') )	//�аٷֺ�
					{
						$med_buff_arr[1] = substr($med_buff_arr[1],0,-1);	//ȥ�ٷֺ�
						$rs['mc'] = (1+$med_buff_arr[1]/100)*$rs['mc'];
					}
					else
					{
						$rs['mc'] += $med_buff_arr[1];
					}
					break;
				}
			}
		}
		
	}
	$zhuangbei_crit = unserialize($_pm['mem'] -> get('format_user_zhuangbei_'.$bid));
	$mem_welcome = unserialize($_pm['mem']->get('db_welcome'));	
	foreach($mem_welcome as $info)
	{
		if( $info['code'] == 'crit_rate' )
		{
			$mem_system_crit = $info['contents'];
		}
	}
	if( empty($mem_system_crit) )
	{
		$sql = " SELECT contents FROM welcome WHERE code = 'crit_rate'";
		$Crit_rate_db = $_pm['mysql']->getOneRecord($sql);
		$Crit_rate = $Crit_rate_db['contents'];	//�����ݿⱩ����
	}
	else
	{
		$Crit_rate = $mem_system_crit;
	}
	//������װ������
	if( isset($att['crit']) )
	{
		$Crit_rate += intval($att['crit']);
	}
	$Crit_number = rand(0,100);	//���100������Ŷ
	if( $Crit_number <= $Crit_rate )	//����
	{
		$Crit = 1;
	}
	else
	{
		$Crit = 0;	//û�б�
	}
	//----------------------------------------
	//----------------------------------------
	$aobj = new Ack($rs, $gs);
	$aobj -> getSkillAck();
	//print_r($rs);
	$bbskillAddHP=0;
	$bbskillAddMP=0;
	//����Թ��﹥����
	if($rs['s_uhp']<0||$rs['s_ump']<0){	//ʹ��ħ����������Ϊ�������Ǹ��Լ���	
		$bback = 0;
		$bb = '0,' . $rs['s_name'];
		$aobj->skillack = 0;
		//��������hp or mp
		if($rs['s_uhp']<0) $bbskillAddHP-=$rs['s_uhp'];
		if($rs['s_ump']<0) $bbskillAddMP-=$rs['s_ump'];
	}else{
		$bback = $aobj -> skillack;
		$bb = $aobj->skillack . ',' . $rs['s_name'];
		
	}
	
	
	

	$aobj1 = new Ack1($gs, $rs);
	$aobj1 -> getSkillAck();
	//����Գ��﹥����
	$gwac = $aobj1 -> skillack;
		
	$gw = number_format($gwac,'','','') . ',' . $gs['s_name'];
	
	
	//������Ѫ����ħ
	//wuping��2013011�޸�
	if($user['inmap']<=10){
		$att = unserialize($_pm['mem']->get('game_user1_hp_mp_'.$_SESSION['id']));
		if(!is_array($att) || empty($att)){//���û��ȡ��
			$att = getzbAttrib($rs['id'],$gwac,$bback);
			$_pm['mem']->setexpire(array('k'=>'game_user1_hp_mp_'.$_SESSION['id'],'v'=>$att),3600);
		}
		//echo "mphp";
	}else{
		$att = getzbAttrib($rs['id'],$gwac,$bback);
	}
	
	
	

	//dxsh ת��Ϊ��������
	$jnnewarr = $_pm['mysql'] -> getOneRecord("SELECT img FROM skill WHERE sid = 112 AND bid = {$_SESSION['mbid']}");
	if(count($jnnewarr) >= 1){
		$add_s_imgeft_arr1 = explode(':',$jnnewarr['img']);
		$add_s_imgeft_arr = str_replace('%','',$add_s_imgeft_arr1[1]);
		$att['hpdx'] += round($add_s_imgeft_arr * $gwac *0.01);
	}
	//���Ӽ���Ч��:hitshp:������ȡ�˺��İٷֱ�ת��Ϊ����:
	if(!empty($rs['s_imgeft']))
	{
		$jnar = explode(":",$rs['s_imgeft']);
		$sp = explode("%",$jnar[1]);
		$num = $sp[0] / 100;
		switch($jnar[0])
		{
			case "hitshp":
				$att['hp1'] += round($num * $bback);
				//echo "bback:".$bback."num:".$num."hp1:".$att['hp1'];
				break;
			case "dxsh":
				$att['hpdx'] += round($num * $gwac);
				break;
			case "shjs":
				$att['ack'] += round($num * $bback);
				break;
		}
	}
	
	//$rs['hp'] += $att['hp1'] + $att['hp2'];
	//$rs['mp'] += $att['mp1'];
	$gwac1 = $gwac - $att['hpdx'];
	$gw = number_format($gwac1,'','','') . ',' . $gs['s_name'];
	$gw1 = number_format($gwac,'','','') . ',' . $gs['s_name'];
	//$aobj -> skillack += $att['hp1'];
	
	$sql = "SELECT * FROM userbb 
			WHERE id = {$rs['id']}";
	$row = $_pm['mysql'] -> getOneRecord($sql);
	
	//�����Ѫ����Ϊ����������ȼ�Ѫ�������ٴ���ң�����Ӧ���ȼ�Ѫ�������ٴ�
	//�����ǰ� ���ʣ���Ѫ+�ӵ�Ѫ-����Ĺ��� ��������ҵ�����Ѫ
	//������� ��Ѫ�� 10000��ʣ��9000,���﹥����11000����Ҽ�Ѫ10000�����ʱ�����Ӧ�ñ�������
	if($row['addhp']+$rs['hp']+$bbskillAddHP>$row['srchp']+$att['hp'])//��ȫ����
	{
		$row['addhp'] = $att['hp'];
		$rs['hp'] = $row['srchp'];
	}else if($rs['hp']+$bbskillAddHP>$row['srchp']){//����hp���Ӳ���addhp
		$row['addhp'] = min($row['addhp'] +$rs['hp']+$bbskillAddHP-$row['srchp'],$att['hp']);
		$rs['hp'] = $row['srchp'];
	}else{
		$rs['hp'] += $bbskillAddHP;		
	}
	
	//��ħҲһ��
	if($row['addmp']+$rs['mp']+$bbskillAddmp>$row['srcmp']+$att['mp'])//��ȫ����
	{
		$row['addmp'] = $att['mp'];
		$rs['mp'] = $row['srcmp'];
	}else if($rs['mp']+$bbskillAddmp>$row['srcmp']){//����mp���Ӳ���addmp
		$row['addmp'] = min($row['addmp'] +$rs['mp']+$bbskillAddmp-$row['srcmp'],$att['mp']);
		$rs['mp'] = $row['srcmp'];
	}else{
		$rs['mp'] += $bbskillAddmp;		
	}
	
	
	$srchp1 = $row['srchp'] + $row['addhp'];
	$srcmp1 = $row['srcmp'] + $row['addmp'];
		
	$ftgw = $_SESSION['fight'.$_SESSION['id']];
	//======================== װ��Ч������ ===============================
	$aobj->skillack += $att['ack'];

	if (!is_array($ftgw))	// �����û���ս����¼����ս�Ĺ������ݡ�
	{   
		$newhp_gw = $gs['hp']-$aobj->skillack;
		$newmp = $gs['mp'];
		
		//��Ѫʱ�����ﲻ��Ѫ
		
/*
if($rs['s_uhp']<0||$rs['s_ump']<0){	
			$newhp_gw = $gs['hp'];					}*/
		$_SESSION['fight'.$_SESSION['id']]=array('uid' => $_SESSION['id'],
												 'bid' => $rs['id'],
												 'gid' => $gid,
												 'hp'  => $newhp_gw,
												 'mp'  => $newmp,
												 'fuzu'=> 0,
												 'ftime'=> time(),
												 'fatting'=> 1);
											
												 
	}
	else if ($ftgw['fuzu']==0)	// ���¹������HP,MP��
	{
		if ($ftgw['bid'] == $rs['id'] && $ftgw['fatting']==1)
		{
			$newhp_gw = $ftgw['hp']-$aobj->skillack;
			$newmp = $ftgw['mp']-$gs['s_ump']; // in here add mp part..<<<<<<<<<<<
		}
		else
		{
			$newhp_gw = $gs['hp']-$aobj->skillack;
			$newmp = $gs['mp']-$gs['s_ump'];
		}
		//��Ѫʱ�����ﲻ��Ѫ
		if($rs['s_uhp']<0||$rs['s_ump']<0){	
			//$newhp_gw = $gs['hp'];
		}

		if ($newhp_gw<0) $newhp_gw = 0;
		if ($newmp<0) $newmp=0;
		$r = $fight;
		$r['hp']			=$newhp_gw;
		$r['mp']			=$newmp;
		$r['fatting']		=1;
		$r['ftime']		=time();
		
		$r['fuzu']		=0;
		
		$_SESSION['fight'.$_SESSION['id']] = $r;
		
	}
	else if($ftgw['fuzu'] == 1) //����û�����һ��������
	{
		$r = $_SESSION['fight'.$_SESSION['id']];
		$r['fuzu']= 0;
		
		$_SESSION['fight'.$_SESSION['id']] = $r;
		
		
		$aobj->skillack = 0;
		$newhp_gw = $ftgw['hp'];
		$newmp = $ftgw['mp'];
	}
	if($flagteam)
	{
		$team->setTeamState(array('cur_monster'=>$_SESSION["fight".$_SESSION['id']]));
	}
	// �����û�BB��Ϣ��
	// ���BB��ɱ����򲻼��Լ�������.
	######################�����ʣ��Ѫ����ħ��###########################

	if ($aobj->skillack < $ftgw['hp']) 
	{
		//����װ���������е�hp
		$sumhp = $att['hp1'] + $att['hp2'] + $row['addhp'];// + $att['hpdx'];

		//
		if($sumhp > $gwac1)//��Ѫ��Щ�������﹥��
		{
			if( $sumhp - $gwac1 > $att['hp'])
			{
				$addhp = $att['hp'];
				//$sumhp - $gwac1
				$nhp_bb   = $sumhp - $gwac1 - $att['hp'] + $row['hp'];
				//		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.',<br />��Ѫ '.$att['hp1'].'#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;	
				//$str .= '<dx>������'.$att['hpdx'];
				//echo '<!--';
				//echo '$nhp_bb('.$nhp_bb   .')=$sumhp('. $sumhp .')-$gwac1('. $gwac1 .')-$att[\'hp\']('.$att['hp'] .')+$row[\'hp\']('. $row['hp'].")-->\r\n";
				if($nhp_bb > $row['srchp'])
				{
					$nhp_bb = $row['srchp'];
				}
			}else{
				$addhp = $sumhp - $gwac1;
				$nhp_bb   = $row['hp'];
			}
		}
		else
		{
			//$nhp_bb = $sumhp + $rs['hp'] - $gwac1;
			$nhp_bb = $sumhp + $rs['hp'] - $gwac1 ;
			
			if($row['srchp'] < $nhp_bb + $bbskillAddHP)
			{
				$nhp_bb = $sumhp + $row['srchp'] - $gwac1;
			}else{
				$nhp_bb += $bbskillAddHP;
			}
			$addhp = 0;//��Ϊ�����,������������Ϊ0, ���Ծͻᵽfight_mod.php�ͻ��Զ����
		}
		//$nhp_bb = $rs['hp']-$aobj1->skillack;
		
	}
	//����װ�����ӵ�mp
	else
	{
		
		$nhp_bb = $rs['hp'];
		if($att['hp1']>0)
		{
			if( $att['hp1'] + $row['addhp'] > $att['hp'])
			{
				$addhp = $att['hp'];
				$nhp_bb   = $att['hp1'] + $row['addhp'] - $att['hp'] + $row['hp'];
				if($nhp_bb > $row['srchp'])
				{
					$nhp_bb = $row['srchp'];
				}
			}else{
				$addhp = $sumhp - $gwac1 + $row['addhp'];
				$nhp_bb   = $row['hp'];
			}
		}
	}
	
	
	$summp = $att['mp1'] + $row['addmp'];
	if($summp > $rs['s_ump'])
	{
		$nmp = $rs['mp'];
		$addmp = $summp - $rs['s_ump'];
		//�жϳ����mp�Ƿ񳬹����ֵ
		$summp1 = $addmp + $nmp;
		if($summp1 > $srcmp1)
		{
			$addmp = $srcmp1 - $nmp;
		}
	}
	else
	{
		$nmp = $summp + $rs['mp'] - $rs['s_ump'];
		if($nmp+$bbskillAddMP>$rs['srcmp'])
		{
			$nmp = $rs['srcmp'];
		}else{
			$nmp += $bbskillAddMP;
		}
		$addmp = 0;
	}
	//$nmp = $rs['mp']-$rs['s_ump']; 
	//$nhp_bb�ǳ����,
	//if($att['hp1']>0) $nhp_bb+=$att['hp1'];

	if ($nhp_bb<0) $nhp_bb=0;
	if ($nmp<0) $nmp=0;

	$_pm['mysql']->query("UPDATE userbb
				   SET hp={$nhp_bb}, 
				       mp={$nmp},
					   addmp={$addmp},
					   addhp={$addhp}
				 WHERE id={$rs['id']} and uid={$_SESSION['id']}
			  ");
	
	if ($newhp_gw <= 0) // gaiwu die
    {	
		unset($_SESSION['catch_gw_info']);//��׽�Ĺ���id
		updateBoss($_SESSION["fight".$_SESSION['id']]['gid']);
		################################################################################
		//������Ʒ��ȡ����ʽ������ID�����ʷ�Χ��
		$prpid = getProps($gs['droplist']);

		//�涨ʱ���ڣ�������Ʒ����
		$date = date("w");
		$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));
		foreach($battletimearr as $v)
		{
			if($v['titles'] == 'gpc')
			{
				$weekarr[$v['days']]['start'] = $v['starttime'];
				$weekarr[$v['days']]['end'] = $v['endtime'];
				$weeks[] = $v['days'];
			}
		}
		if(in_array($date,$weeks))
		{
			$hm = date("H:i");
			if($hm >= $weekarr[$date]['start'] && $hm <= $weekarr[$date]['end'])
			{
				if(!empty($gs['activedroplist']))
				{
					$prpid = getProps($gs['activedroplist']);
				}
				if(!empty($activeprpid))
				{
					$prpid .= ",".$activeprpid;
				}
			}
		}
		$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
		$okidlist = '';
		if($_SESSION['multi_monsters'.$_SESSION['id']] == 3){
			$_pm['mysql'] -> query("DELETE FROM tgt WHERE id = {$_SESSION['multi_monsters_id_tgt_'.$_SESSION['id']]}");
		}
		if($_SESSION['multi_monsters'.$_SESSION['id']] == 3 && empty($_SESSION['multi_monsters_next_tgt_'.$_SESSION['id']])){//ͨ��������
			$tga = $_pm['mysql'] -> getOneRecord("SELECT drops FROM c_gpc WHERE id = {$_SESSION['multi_monsters_tgid_tgt_'.$_SESSION['id']]}");
			$_SESSION['multi_monsters_tgid_tgt_'.$_SESSION['id']] = '';
			$tgplist = getProps($tga['drops']);
			$tgrarr = split(',', $tgplist);
			foreach ($tgrarr as $k => $v)
			{
				if(empty($v)){
					continue;
				}
				$tgprs = $mempropsid[$v];
				/*$prs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
										 'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
							  ));*/
				if( is_array($tgprs) )
				{
					$tgdrop .= $tgprs['name'].'��';
					$drop .= $tgprs['name'].'��';
					$okidlist .= $v.',';
				} 
			}	// end foreach.			
			if(substr($tgdrop,-2,2)=='��') $tgdrop = substr($tgdrop,0,-2);
			
			if($_SESSION['multi_monsters_boss_tgt_'.$_SESSION['id']] % 5 == 0){
				$task = new task();
				$task->saveGword("�����ͨ����{$_SESSION['multi_monsters_boss_tgt_'.$_SESSION['id']]}�ؿ�������� $tgdrop ��ͨ�ؽ�����");
			}
			
			
		
			$_pm['mysql'] -> query("UPDATE player_ext SET tgt = tgt + 1 WHERE uid = {$_SESSION['id']}");
			//echo "UPDATE player_ext SET tgt = tgt + 1 WHERE uid = {$_SESSION['id']}";
			$tch = tgtgw();
		}
		
		
		if ($prpid === false || $prpid == 0 || $prpid == ''){			
			$drop = '�ޣ�';			
		}
		else
		{
		    $rarr = split(',', $prpid);
			foreach ($rarr as $k => $v)
			{
				if(empty($v)){
					continue;
				}
				
				$prs = $mempropsid[$v];
				/*$prs = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
										 'v' => "if(\$rs['id'] == '{$v}') \$ret=\$rs;"
							  ));*/

				if( is_array($prs) )
				{
					$drop .= $prs['name'].'��';
					$okidlist .= $v.',';
				} 
			}	// end foreach.			
			if(substr($drop,-2,2)=='��') $drop = substr($drop,0,-2);
			if(substr($okidlist,-1,1)==',') $okidlist = substr($okidlist,0,-1);
		}		
		
		/** ������߼�� */
		$uProps = usedProps($user);
//$gs['exps'] = $gs['exps']*100;
		if ($uProps !== false)
		{
			if($_SESSION['exptype'.$_SESSION['id']] != 1)
			{
				$gs['exps'] = intval($gs['exps']*$uProps['double']);
			}
			else
			{
				if(!empty($uProps['doubleexp']))
				{
					$gs['exps'] = intval($gs['exps']*$uProps['double']*$uProps['doubleexp']);
				}
				else
				{
					$gs['exps'] = intval($gs['exps']*$uProps['double']);
				}
			}
		}		
		//echo '$flagteam=['.$flagteam.']';
		/*������߲������*/
		if(!$flagteam){
			
			$boss_judge = $_pm['mysql']->getOneRecord(" SELECT  boss FROM gpc WHERE id = '".$_SESSION['gwcdie'.$_SESSION['id']]."'" );
			if( $boss_judge['boss']  == 3 )
			{
				$T_fight_count = $_pm['mysql']->getOneRecord(" SELECT count(*) zl FROM T_fight_log ");
				if( $T_fight_count['zl'] > 3000 )	//����3000������ʼɾ��
				{
					$delete_log_id = $_pm['mysql']->getOneRecord(" SELECT Id  FROM T_fight_log LIMIT 1 ");
					$_pm['mysql']->query(" DELETE FROM T_fight_log WHERE id = {$delete_log_id['Id']} ");
				} 
				$_pm['mysql']->query( "INSERT INTO T_fight_log(F_uid,F_gpc,F_p_info,F_time) VALUES ({$_SESSION[id]},'".$_SESSION['gwcdie'.$_SESSION['id']]."','".$okidlist."','".time()."')" );
			}
			saveGetPropsa($okidlist);
			
			$sj = saveGetOther($rs, $gs['exps']); // Save exps and money.
			if ($sj === true) 
			{
				$sj="<font color=yellow size=4 style='font-family:������κ;font-weight:bold;'>{$rs['name']} �ĵȼ�����!</font>";
				//$_pm['user']->updateMemUsersk($_SESSION['id']);
				$_pm['mem']->set(array('k'=>MEM_SYSWORD_KEY, 'v'=>'��ϲ��� '.$_SESSION['nickname'].'�ı��� '.$rs['name'].' ͨ���������У����뵽���ߵȼ���'));
			}
			else $sj = "";
			
			$user['money'] += $gs['money'];
			$user['money'] += $att['money'];
			if ($user['money'] >= 1000000000) $user['money']=1000000000;
			
	
			unset($prs, $rarr);
			if(empty($att['money']))
			{
				$att['money'] = 0;
			}
		
			if($_SESSION['multi_monsters'.$_SESSION['id']] == 1 && $_SESSION['multi_monsters_boss'.$_SESSION['id']] >= 4 && empty($_SESSION['multi_monsters_next'.$_SESSION['id']])){//��ս��ͼ
				//$_SESSION['multi_monsters_next'.$_SESSION['id']]
				//����������������Ѷȵ���ս�������XXXXXXX��ϡ�е��ߡ�
				if(empty($_SESSION['multi_monsters_drops'.$_SESSION['id']])){
					$_SESSION['multi_monsters_drops'.$_SESSION['id']] = $drop;
				}else{
					$_SESSION['multi_monsters_drops'.$_SESSION['id']] .= ','.$drop;
				}
				$task = new task();
				$task->saveGword("�����������{$_SESSION['multi_monsters_boss'.$_SESSION['id']]}���Ѷȵ���ս������� {$_SESSION['multi_monsters_drops'.$_SESSION['id']]} ��ϡ�е��ߡ�");
			}else if($_SESSION['multi_monsters'.$_SESSION['id']] == 1 && $_SESSION['multi_monsters_boss'.$_SESSION['id']] >= 4 && !empty($_SESSION['multi_monsters_next'.$_SESSION['id']])){
				if(empty($_SESSION['multi_monsters_drops'.$_SESSION['id']])){
					$_SESSION['multi_monsters_drops'.$_SESSION['id']] = $drop;
				}else{
					$_SESSION['multi_monsters_drops'.$_SESSION['id']] .= ','.$drop;
				}
			}
			
			if($_SESSION['multi_monsters'.$_SESSION['id']] == 1){
				
				$_pm['mysql'] -> query("UPDATE challenge SET gid = {$_SESSION['multi_monsters_next'.$_SESSION['id']]},lastvtime = ".time()." WHERE uid = {$_SESSION['id']}");
				$_pm['mysql'] -> query("DELETE FROM challenge_log WHERE id = {$_SESSION['multi_monsters_id'.$_SESSION['id']]}");
			}
			
			$drops = "��þ��飺" . ($gs['exps']) . "<br/>��ý�ң�" . ($gs['money']."+".$att['money']) . " ��
					  <br/>������0<br/>�����Ʒ��{$drop}<br/>���⽱������<br/>{$sj}";
			$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
			$a = catchTask($user, $_SESSION["fight".$_SESSION['id']]['gid']);
			// �����û����ݡ�dblexpflag,maxdblexptime,sysautosum,maxautofitsum
			$sql = "UPDATE player
						   SET money={$user['money']},
							   tasklog='{$user['tasklog']}',
							   dblexpflag={$user['dblexpflag']},
							   maxdblexptime={$user['maxdblexptime']},
							   sysautosum={$user['sysautosum']},
							   maxautofitsum={$user['maxautofitsum']}
						 WHERE id={$_SESSION['id']}
					  ";
			$_pm['mysql']->query($sql);
		}else{
			foreach($teamInfo['members'] as $m)
			{
				if($m['state']>0) {
					//catchTask($user, $_SESSION["fight".$_SESSION['id']]['gid']);
					$_user = $_pm['user']->getUserById($m['uid']);
					catchTask($_user, $_SESSION["fight".$_SESSION['id']]['gid']);
					//echo $_user['id'].',';
				}
			}
			
			$teamState=$team->getTeamState();

			if(
				empty($teamState['monsters'])
				||
				(
					count($teamState['monsters_tf_3'])==1 &&
					$teamState['team_fuben_card_step_num']==3
				)
			)//��Ӵ��ʤ��
			{
				$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
				$team->setTeamState(
						array(	
							'fighting'=>0,
							'props_get'=>$okidlist,
							'exp_get'=>$gs['exps'],
							'money_get'=>$gs['money']+$att['money']
							)
				);
				$teamState=$team->getTeamState();
				$memberNum=0;
				$tmpMem=array();
				foreach($teamInfo['members'] as $k=>$m)
				{
					if($m['state']>0) {
						$memberNum++;
						$tmpMem[]=$k;
					}
				}
				
				//$memberNum=count($teamInfo['members']);
				$multiple=$memberNum*0.2+1;
				$moneyAvg=intval($teamState['money_get']*$multiple/$memberNum);
				$expAvg  =intval($teamState['exp_get']*$multiple/$memberNum);
				$props   =explode(',',$teamState['props_get']);
				
				$cur=0;				
				for($i=0;$i<count($props);$i++)
				{
					if(empty($props[$i])) continue;
					$teamInfo['members'][$tmpMem[$cur]]['props_get'][]=$props[$i];
					$cur++;
					if($cur==$memberNum) $cur=0;
				}

				//$hasAuto=$_pm['mysql']->getOneRecord('select pid from userbag where pid=2418 and uid='.intval($teamInfo['team']['creator']).' limit 1');

				$auto=$_pm['mysql']->getOneRecord('select b.uid,b.team_auto_times from player_ext b,team t where t.creator=b.uid and t.id='.$_SESSION['team_id'].' limit 1');
				$hasAuto=false;
				if($auto&&$auto['team_auto_times']>0&&$teamState['autofighting'])
				{
					$hasAuto=true;
					$auto['team_auto_times']-=1;
				}else{
					$auto['team_auto_times']=0;
				}
				if(!isset($teamState))
				{
					$teamState=$team->getTeamState();
				}
				if($teamState['autofighting']==1)
				{
					$_pm['mysql']->query('update player_ext set team_auto_times='.$auto['team_auto_times'].' where team_auto_times>0 and uid='.$auto['uid']);
				}
				$rsStr='';
				$_db_bb=$db_bb;
				for($i=0;$i<count($teamInfo['members']);$i++)
				{
					if($teamInfo['members'][$i]['state']>0){
						if($hasAuto)
						{
							$_pm['mysql']->query("UPDATE userbb,player
													 SET hp=srchp,mp = srcmp,addmp = 0,addhp = 0
												   WHERE fightbb=userbb.id and player.id=".$teamInfo['members'][$i]['uid']);
						}
						$rsStr.='<strong>'.$teamInfo['members'][$i]['nickname'].'</strong>�����Ʒ��<br/>';
						$_rs=$_pm['mysql']->getOneRecord('select userbb.level,userbb.nowexp,userbb.lexp,userbb.wx,userbb.kx,userbb.czl,userbb.hits,userbb.speed,userbb.name,userbb.username,userbb.uid,userbb.id from userbb,player where userbb.uid=player.id and userbb.id=player.mbid and player.id='.$teamInfo['members'][$i]['uid']);
						if(!empty($teamInfo['members'][$i]['props_get'])){
							foreach($teamInfo['members'][$i]['props_get'] as $__id)
							{
								if(isset($mempropsid[$__id]))
								{
									$rsStr.=$mempropsid[$__id]['name'].'��';
								}
							}
						}
						
						$_bb = $_pm['user']->getUserPetByIdS($teamInfo['members'][$i]['uid'],$_rs['id']);//ս�����
						if (!is_array($_bb))
						{   
							$loop=true;
							$ct=0;
							while($loop)
							{
								$ct++;
								$_bb		 = $_pm['user']->getUserPetByIdS($teamInfo['members'][$i]['uid'],$_rs['id']);		
								if (is_array($_bb)) break;
								if($ct>10) 
								{
									$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
									$_SESSION['first_in'] = 3;
									exit("Get BB Failed!");
								}
								
							}
						}
						$rs = array_merge($_bb,array());

						if(!empty($teamInfo['members'][$i]['props_get']))
						{
							saveGetPropsa(implode(',',$teamInfo['members'][$i]['props_get']),$teamInfo['members'][$i]['uid']);
						}
						$db_bb = $rs;
						$sj = saveGetOther($rs, $expAvg*($auto['uid']==$teamInfo['members'][$i]['uid']?1.2:1),$teamInfo['members'][$i]['uid']);
						$sql= 'update player set money=if(money+'.$moneyAvg.'>1000000000,1000000000,money+'.$moneyAvg.') where id='.$teamInfo['members'][$i]['uid'];
						$_pm['mysql']->query($sql);
						$rsStr.="<br/> ��þ��飺".intval($expAvg*($auto['uid']==$teamInfo['members'][$i]['uid']?1.2:1))."��".($sj ===true?"<strong>�ȼ�����!</strong>":"")."<br/>��ý�ң�".$moneyAvg."��<hr>";
					}
				}
				$db_bb=$_db_bb;
				$drops=$rsStr;
				
				if(isset($teamState['team_fuben_step'])){
					$isBoss = isset($teamState['team_fuben_boss'])&&$teamState['team_fuben_boss']=='1'?true:false;
					
					$teamfbFlag=$team->setTeam_fuben_step($teamState);//������Ӹ�������
					//$teamState=$team->getTeamState();
					
					if($teamfbFlag!==false)
					{
						$drops.='<!--teamfbFlag-'.$teamfbFlag.'-->'.($isBoss?'<!--boss-->':'');
					}else{
						$drops='��Ӹ�������:<strong><font style="color: rgb(153, 0, 102);">'.$team->fbjindu.'��</font></strong><hr/>'.$drops;
					}
				}
				$team->clearTeamState($auto['team_auto_times']);
				//$teamState=$team->getTeamState();
			}else{
				//array_shift($teamState['monsters']);

				if(count($teamState['monsters'])>0){
					foreach($teamState['monsters'] as $k=>$v)
					{
						if(!empty($v))
						{
							unset($teamState['monsters'][$k]);
							break;
						}else{
							unset($teamState['monsters'][$k]);
						}
					}
				}
				if(count($teamState['monsters'])>0)
				{
					foreach($teamState['monsters'] as $k=>$v)
					{
						if(!empty($v))
						{
							$__gw=$v;
							break;
						}else{
							unset($teamState['monsters'][$k]);
						}
					}
				}
				if(empty($__gw)||empty($__gw['id']))
				{
					$_SESSION['first_in'] = 3;
					die('line:'.__LINE__.';$teamState[\'monsters\']='.print_r($teamState['monsters'],1));
				}
				//$__gw=$teamState['monsters'][0];
				
				//ֻ�����һ�������ʱ��,�������������һ��,
				//��$__gw��ͬһ��,���������������ʤ����,����Ҫ��$teamState['monsters']����Ϊ��,��������!
				if(count($teamState['monsters'])==1){
					//���������������ǰ�˱����һ�����������һ����Ҫͨ�����ȡ���������ݣ�
					//��Ϊ����ѹ��$teamState['monsters']������ˣ���fight_mod����ͨ��$teamState['monsters']���ҹ����
					$teamState['monsters_last']=$teamState['monsters'][0];
					$teamState['monsters']=array();
				}
				$teamMoreMonster=true;
				$_SESSION['fight'.$_SESSION['id']]	= array(
							'uid'=>$_SESSION['id'],
							'bid'=>$_SESSION['mbid'],
							'gid'=>$__gw['id'],
							'hp' =>$__gw['hp'],
							'mp' =>$__gw['mp'],
							'fuzu'=>0,
							'fatting'=>1,
							'boss'=>$__gw['boss'],
							'ftime'=>time()-11
							);
				$_SESSION['gwcdie'.$_SESSION['id']]=$__gw['id'];
				//echo '$okidlist='.$okidlist.',$gs='.print_r($gs,1).',$teamState='.print_r($teamState,1)."\r\n\r\n\r\n";
				$team->setTeamState(
						array(
							'monsters'=>$teamState['monsters'],
							'cur_monster'=>$_SESSION["fight".$_SESSION['id']],
							'props_get'=>$okidlist,
							'exp_get'=>$gs['exps'],
							'money_get'=>$gs['money']+$att['money']
							)
				);
				
				//
				$drops='TeamFightNextMonster';
			}
			$sql = "UPDATE player
						   SET tasklog='{$user['tasklog']}',
							   maxdblexptime={$user['maxdblexptime']},
							   sysautosum={$user['sysautosum']},
							   maxautofitsum={$user['maxautofitsum']}
						 WHERE id={$_SESSION['id']}
					  ";
			$_pm['mysql']->query($sql);
		}
		//$_pm['user']->updateMemUser($_SESSION['id']);
		//$_pm['user']->updateMemUserbb($_SESSION['id']);
		//$_pm['user']->updateMemUserbag($_SESSION['id']);
		
	}
	else if ($nhp_bb + $addhp <= 0)
	{
		$mmonsterContinueFlag ="DIE";
		$drops='���� ' . $rs['name'].' �ܵ��������˺����Ѿ�����ս��������'; // bb die.
		$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
		if($_SESSION['multi_monsters'.$_SESSION['id']] == 3){
			$_pm['mysql'] -> query("UPDATE player_ext SET tgttime = ".time().",tglasttime = ".time()." WHERE uid = {$_SESSION['id']}");
		}
		
		if($_SESSION['multi_monsters'.$_SESSION['id']] == 1){
			$_SESSION['multi_monsters_drops'.$_SESSION['id']] = '';
			$_pm['mysql'] -> query("UPDATE challenge SET flag = 0 WHERE uid = {$_SESSION['id']}");
		}
		
		if($flagteam)
		{
			$anyAlive=$team->setTeamMemberSate($_SESSION['id'],0);
			if($anyAlive)
			{
				$_SESSION["fight".$_SESSION['id']]['ftime']=time()-10;	
				$_SESSION["fight".$_SESSION['id']]['hp']=$newhp_gw;
				$data['cur_monster']=$_SESSION["fight".$_SESSION['id']];
				$drops='TeamStillAlive';
				$data['cur_monster']['hp']= $newhp_gw;
				$team->setTeamState(array('fight_html'=>'','cur_monster'=>$data['cur_monster']));
				//$_SESSION['teamState']=$team->getTeamState();

				//$team->snotice('getTeamFightMod');
			}else{
				$auto=$_pm['mysql']->getOneRecord('select b.uid,b.team_auto_times from player_ext b,team t where t.creator=b.uid and t.id='.$_SESSION['team_id'].' limit 1');
				if($auto&&$auto['team_auto_times']>0)
				{
					$auto['team_auto_times']-=1;					
				}else{
					$auto['team_auto_times']=0;
				}
				if(!isset($teamState))
				{
					$teamState=$team->getTeamState();
				}
				if($teamState['autofighting']==1)
				{
					$_pm['mysql']->query('update player_ext set team_auto_times='.$auto['team_auto_times'].' where team_auto_times>0 and uid='.$auto['uid']);
				}
				$drops='���Ķ����Ѿ�ȫ������'; // bb die.
				$team->clearTeamState($auto['team_auto_times']);
				$team->clearTeamFubenData();
				$team->setTeamState(array('fubensjoj'=>0));
			}
		}		
		unset($_SESSION['catch_gw_info']);//��׽�Ĺ���id
	}
	else if ($newhp_gw > 0) 
    {
		if($flagteam){
			$mmonsterContinueFlag='MULTI_MONSTRTER_CONTINUE';
		}	
	}
	else $drops='';
/*-----------------------------------------------------------------------------------------------------------------------------------------------------------*/

		$sqlUpdate = "UPDATE userbb
				   SET hp={$nhp_bb},
					   mp={$nmp},
					   addmp={$addmp},
					   addhp={$addhp}
				 WHERE id={$rs['id']} and uid={$_SESSION['id']}
		  ";
/*
$sql = "SELECT addmp,addhp FROM userbb WHERE uid = {$_SESSION['id']} and id = {$rs['id']}";
		$add = $_pm['mysql'] -> getOneRecord($sql);	
		$addhp = $add['addhp'];
		$addmp = $add['addmp'];
*/

/*-----------------------------------------------------------------------------------------------------------------------------------------------------------*/

	$_pm['mysql']->query($sqlUpdate);

	if ($newhp_gw == 0&&!isset($teamMoreMonster)) {
		$r =$_SESSION['fight' . $_SESSION['id']];
		$r['hp']		= $newhp_gw;
		$r['mp']		= $newmp;
		$r['fatting']	= 0;
		$r['fuzu']		= 0;
		$r['gid']		= 0;		
		$_SESSION['fight'.$_SESSION['id']]= $r;
	}
	
	// Free resource.
	// set fight info to memory.
	//$_pm['mem']->set(array('k'=>MEM_FIGHT_KEY, 'v'=>$fight));
	//$_SESSION['fight'.$_SESSION['id']]=$fight;
	
	// Add gaiwu word.
	$word = sayWord($grs, $newhp_gw);

	header('Content-Type:text/html;charset=GBK'); 
	// =============================== ս�������ʼ =========================


	$nhp_bb += $addhp;
	if($nhp_bb > $srchp1)
	{
		$nhp_bb = $srchp1;
	}
	$nmp += $addmp;
	if($nmp > $srcmp1)
	{
		$nmp = $srcmp1;
	}
	
	//echo $echo;
	
	//$_SESSION['echo'] .=  $echo."\n".$sqlUpdate.'<br/>'."\n\n";
	//echo $echo."\n".$sqlUpdate.'<br/>'.print_r($add,1)."\n\n";;
	//��������,������ħ������
	//$bb .= ','.$rs['s_vary'].','.$rs['s_uhp'].','.$rs['s_ump'];
	
	if(!empty($att['hp1']) && empty($att['mp1']))
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.',<br />��Ѫ '.$att['hp1'].'#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;	
	}
	
	else if(!empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] > 0)
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.',<br />��Ѫ '.$att['hp1'].'&nbsp;==<br />��ħ'.$att['mp1'].'&nbsp;#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;	
	}
	else if(!empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] < 0)
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.',<br />��Ѫ '.$att['hp1'].'&nbsp;==<br /ʧħ'.$att['mp1'].'&nbsp;#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;
	}
	else if(empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] < 0)
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.'<br /> ʧħ'.$att['mp1'].'&nbsp;#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;	
	}
	else if(empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] > 0)
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.',<br />��ħ'.$att['mp1'].'&nbsp;#'. $newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;	
	}
	else
	{
		$str .= $nhp_bb . ',' . $nmp. ',' . $bb.'#'.$newhp_gw . ',' . $gw1.'#' . $drops . '#' . $word;
	}
	if(!empty($att['hpdx']))
	{
		$str .= '<dx>������'.$att['hpdx'];
	}
	
	if( ($rs['s_uhp']<0||$rs['s_ump']<0) && ($rs['mp']< $rs['s_ump']) ){
		$str.='#'.$rs['s_vary'].',0,0#'.$mmonsterContinueFlag;
	}else{
		 $str.='#'.$rs['s_vary'].','.$rs['s_uhp'].','.$rs['s_ump'].'#'.$mmonsterContinueFlag;
	}
	if(!empty($att['ack']))
	{
		$str .= '#<ack>�˺����'.$att['ack'];
	}
	if($_SESSION['multi_monsters'.$_SESSION['id']] == 1 && empty($_SESSION['multi_monsters_next'.$_SESSION['id']])&&$newhp_gw <= 0){
		$_SESSION['multi_monsters_drops'.$_SESSION['id']] = '';
		$str .= '#challengeend';
		$_pm['mysql'] -> query("UPDATE challenge SET flag = 0 WHERE uid = {$_SESSION['id']}");
		//$_pm['mysql'] -> query("DELETE FROM challenge_log WHERE uid = {$_SESSION['id']}");
	}
	if($tch == 'a'){
		//ͨ����
		//$_pm['mysql'] -> query("UPDATE player_ext SET tgttime = ".time().",tglasttime = ".time()." WHERE uid = {$_SESSION['id']}");
		//��¼ͨ��ʱ��
		$_pm['mem']->set(array("k"=>'tgtimeflag'.$_SESSION['id'],"v"=>time()));
		$str .= '#<tgtend>';
	}
	
	if($_SESSION['multi_monsters'.$_SESSION['id']] == 3){
		$_pm['mysql'] -> query("UPDATE player_ext SET tglasttime = ".time()." WHERE uid = {$_SESSION['id']}");
	}
	$str .= "*".$Crit;	//�Ƿ񱩻�
	$ack_type = 0;
	$str .= "*".$ack_type;	//���й���
	echo $str;
	if($flagteam){		
		//wr(date("Y-m-d H:i:s").':'.__FILE__.'>'.__LINE__."<br/>\r\n");
		$team->setTeamState(array('fightgate_html'=>$str));
		$rs=$team->snotice('getTeamFightGate'.iconv('gbk','utf-8',$str),NULL,array($_SESSION['id']));
		//wr(date("Y-m-d H:i:s").':'.__FILE__.'>'.__LINE__.' - '.$rs."<br/>\r\n");
	}
	// ========================== ս��������� =======================================
}
else
{	$drops='���� ' . $grs['name'].' �����ˣ�����4';
	header('Content-Type:text/html;charset=GBK'); 
	echo '0,0,0#0,0#' . $drops . '#' . $word;
	$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");
}

function bossCheck($gid)
{
	global $_pm;
	//wuping��2013��1��11���޸ģ��ĳ�ֱ�Ӵ����ݿ��в�ѯ
	//$memgpcid = unserialize($_pm['mem']->get('db_gpcid'));
	//$grs = $memgpcid[$gid];
	//$grs = $_pm['mysql']->getOneRecord('select id,name,level,hp,mp,ac,mc,speed,hits,miss,catchv,catchid,skill,imgstand,imgack,imgdie,droplist,exps,money,boss,wx,kx,activedroplist from gpc where id='.$gid.' limit 1');
	$grs = getGpcByGid($gid);
	/*$grs = $_pm['mem']->dataGet(array('k' => MEM_GPC_KEY, 
									  'v' => "if(\$rs['id'] == '{$gid}') \$ret=\$rs;"
								));*/
	if (!is_array($grs)) return true;
    if ($grs['boss'] == 3)
	{
		$exists = $_pm['mysql']->getOneRecord("SELECT fightuid,gid
						                         FROM boss_refresh
									 		    WHERE fightuid={$_SESSION['id']} and gid={$gid}
												LIMIT 0,1
											 ");
		if (!is_array($exists)) return false;
	}
	return true;
}
//���ݹ���id��ȡ�ù�������
function getGpcByGid($gid){
	global $_pm;
	$grs = $_pm['mysql']->getOneRecord('select id,name,level,hp,mp,ac,mc,speed,hits,miss,catchv,catchid,skill,imgstand,imgack,imgdie,droplist,exps,money,boss,wx,kx,activedroplist from gpc where id='.$gid.' limit 1');
	return $grs;
	$grs = $_pm['mem']->get('game_base_gpc_'.$gid);
	
	if($grs){
		
		return unserialize($grs);
	}else{
		$grs = $_pm['mysql']->getOneRecord('select id,name,level,hp,mp,ac,mc,speed,hits,miss,catchv,catchid,skill,imgstand,imgack,imgdie,droplist,exps,money,boss,wx,kx,activedroplist from gpc where id='.$gid.' limit 1');
		$_pm['mem']->set(array('k'=>'game_base_gpc_'.$gid,'v'=>$grs));
		return $grs;
	}
}

$_pm['mem']->memClose();
$_SESSION['first_in'] = 4;	//������
?>