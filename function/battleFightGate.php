<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.08
*@Update Date: 2008.08.15
*@Usage:Fightting Function.
*@Note: NO Add magic props.
  ��ģ����Ҫ���ܣ�
  	 ս��ս���������ؽű���
  ��Ҫ��
  ###############################################################
     �ɹ������ҷ�������Է�����ĳɳ�ֵ֮��=x
	       ս���ȼ����ṩ����������Ů������
		   ����ֵ=ȡ��{ս��ʤ����������*[1��(X��20��/100)]}
	       ͬʱ���ٶԷ�Ů��X������  
     ���ʧ�ܣ����Լ���ӪŮ������ 1 �㡣

	 >> ����ս���ʱ�����ơ�
	 >> ����û��Ƿ��ر���������⡣
  ###############################################################
*/
session_start();
require_once('../config/config.game.php');
/*if (!defined('BATTLE_TIME_START'))
	define(BATTLE_TIME_START, "20:00");
if (!defined('BATTLE_TIME_END'))
	define(BATTLE_TIME_END, "22:00");
if (!defined('BATTLE_TIME_WEEK'))
	define(BATTLE_TIME_WEEK, 5);*/

secStart($_pm['mem']);
//�������
$time = time();
$sql = "SELECT time FROM fight_log WHERE uid = {$_SESSION['id']} and vary = 1";
$timearr = $_pm['mysql'] -> getOneRecord($sql);
if(is_array($timearr)){
	$ctime = $time - $timearr['time'];
	if($ctime < 1.5){
		$_SESSION['id'] = '';
		die('�������죡');
	}else{
		$_pm['mysql'] -> query("UPDATE fight_log SET time = ".time()." WHERE uid = {$_SESSION['id']} and vary = 1");
	}
}else{
	$_pm['mysql'] -> query("INSERT INTO fight_log (uid,time,vary) VALUES({$_SESSION['id']},".time().",1)");
}
//���������

$id			= intval($_REQUEST['id']);		// 	����ID
if($id>1)
{
	$_SESSION['id'] = '';
	$drops='�Ƿ�ʹ�ü��ܣ����߳ͷ�������';
	$word='';
	header('Content-Type:text/html;charset=GBK'); 
	echo '0,0,0#0,0#' . $drops . '#' . $word;	
//stopUser(10);
	exit;
}
$id			= 1;
$gid		= intval($_REQUEST['g']);	 	//  ����ս��ҵĳ���ID
$db_bb		= array();	//	���ݿ��б�����ԭʼ���ԡ�


$wgcheck = $_GET['checkwg'];
if($wgcheck != 'checked'){
	$_SESSION['id'] = '';
}

// ս������ʱ�俪�ء�
$week=date("N", time());
$hourM=date("H:i", time());

$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));

foreach($battletimearr as $bv)
{
	if($bv['titles'] != "battle")
	{
		continue;
	}
	if($week == $bv['days'] && $hourM >= $bv['starttime'] && $hourM <= $bv['endtime'])
	{
		$checkstr = 1;
		break;
	}
}
if(empty($checkstr))
{
	die('<center><span style="font-size:12px;">ս����δ������</span></center>');
}

/*if ($week != BATTLE_TIME_WEEK || ($hourM < BATTLE_TIME_START || $hourM > BATTLE_TIME_END) )
{
	die('<center><span style="font-size:12px;">ս����δ������</span></center>'); // record log in here.
}*/

$user		= $_pm['user']->getUserById($_SESSION['id']);
$fight		= $_SESSION['fight'.$_SESSION['id']];
$cUser = $_pm['mysql']->getOneRecord("SELECT bid 
										FROM battlefield_user 
									   WHERE uid={$_SESSION['id']}");
if ( $fight['gid']==0 ){exit();}

/** �Ƿ����ݼ�⡣*/
if ($fight['gid'] != $gid) stopUser();
/*###�Ƿ����ݼ�����###*/	


// GET INFO FROM ARRAY.
//$_bb	 = $_pm['user']->getUserPetById($_SESSION['id']);

//$_sk	 = $_pm['user']->getUserPetSkillById($_SESSION['id']);

//$_sksys	 = unserialize($_pm['mem']->get(MEM_SKILLSYS_KEY));
//$_gpc	 = unserialize($_pm['mem']->get(MEM_GPC_KEY));


/* Fix read database fail!*/
if(intval($_SESSION['id'])<1||intval($user['mbid'])<1) exit('$user[\'mbid\']='.$user['mbid'].',$_SESSION[\'id\']='.$_SESSION['id']);
$_bb = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['mbid']);
if (!is_array($_bb))
{   
	$loop=true;
	$ct=0;
	while($loop)
	{
		$ct++;
		$_bb		 = $_pm['user']->getUserPetByIdS($_SESSION['id'],$user['mbid']);		
		if (is_array($_bb)) break;
		if($ct>10) exit("Get BB Failed!");
		sleep(1);
	}
}
$_sk		 = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],$id);

if(intval($_SESSION['id'])<1||intval($user['mbid'])<1) exit('$_bb[\'id\']='.$_bb['id'].',$_SESSION[\'id\']='.$_SESSION['id']);
if (!is_array($_sk))
{   
	$loop=true;
	$ct=0;
	while($loop)
	{
		$ct++;
		$_sk		 = $_pm['user']->getUserPetSkillByIdS($_SESSION['id'],$_bb['id'],$id);
		if (is_array($_sk)) break;
		if($ct>10) exit("Get SKILL Failed!");
		sleep(1);
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
								's_ump'   => $_sk['ump']
							   )
					 );				
}
else $rs = '';

//get gwinfo.
$grs = $_pm['mysql']->getOneRecord("SELECT *
									FROM userbb
								   WHERE id={$gid}
								");

if (!is_array($grs)) $skid=1;
else
{
	$sk = str_replace(":", '.' ,$grs['skillist']);
	if (strstr($sk, ",") === false) 	$sk .= "," . $sk;

	$evlmax = "\$max=max(".$sk.");";
	$evlmin  = "\$min=min(".$sk.");";
	eval($evlmax);
	eval($evlmin);
	$ar = split(",", $grs['skillist']);
	foreach($ar as $k => $v)
	{
		$arr = split(":", $v);
		$alljn[$k] = $arr[0];
	}	
	while(true)
	{
		$skid = rand($min, $max);
		if ( in_array($skid, $alljn) )
		{
			break;
		}
	}
}

//�����ұ�����������Ϣ��
$gs = $_pm['mysql']->getOneRecord("SELECT b.*,
											 s.name as s_name, 
											 s.wx as s_wx,
											 s.value as s_value,
											 s.plus as s_plus,
											 s.uhp as s_uhp,
											 s.ump as s_ump,
											 s.id as s_id
										FROM userbb as b,skill as s
									   WHERE b.id=s.bid and b.id={$gid}
									   LIMIT 0,1
									");

// END.
if (!is_array($gs)) $gs='';

if(is_array($rs) && is_array($gs))  
{
//=================== װ��Ч����ʼ =========================
	
	$db_bb = $rs;
	//########################
	// ����װ�����Ե�ս���С�
	#############################
	$att = getzbAttrib($rs['id']);
	$rs['ac']	+= $att['ac'];
	$rs['mc']	+= $att['mc'];
	$rs['hits'] += $att['hits'];
	$rs['speed']+= $att['speed'];
	$rs['miss']	+= $att['miss'];
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
	$bbskillAddHP=0;
	$bbskillAddMP=0;
	//����Թ��﹥����
	$bback = $aobj -> skillack;
	
	$bb = $aobj->skillack . ',' . $rs['s_name'];
	$aobj1 = new Ack($gs, $rs);
	$aobj1 -> getSkillAck();
	//����Գ��﹥����
	$gwac = $aobj1 -> skillack;
	$gw = $gwac . ',' . $gs['s_name'];
	
	//������Ѫ����ħ
	$att = getzbAttrib($rs['id'],$gwac,$bback);
	//$rs['hp'] += $att['hp1'] + $att['hp2'];
	//$rs['mp'] += $att['mp1'];
	$gwac1 = $gwac - $att['hpdx'];
	$gw = $gwac1 . ',' . $gs['s_name'];
	$gw1 = $gwac . ',' . $gs['s_name'];
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
	//print_r($ftgw);exit;
//======================== װ��Ч������ ===============================

	if (!is_array($ftgw))	// �����û���ս����¼����ս�Ĺ������ݡ�
	{   
		$newhp = $gs['hp']-$aobj->skillack - $att['fhp'];
		$newmp = $gs['mp'];
		
		//��Ѫʱ�����ﲻ��Ѫ
		
/*
if($rs['s_uhp']<0||$rs['s_ump']<0){	
			$newhp = $gs['hp'];			
		}
*/

		$_SESSION['fight'.$_SESSION['id']]=array('uid' => $_SESSION['id'],
												 'bid' => $rs['id'],
												 'gid' => $gid,
												 'hp'  => $newhp,
												 'mp'  => $newmp,
												 'fuzu'=> 0,
												 'ftime'=> time(),
												 'fatting'=> 1);
	}
	else if ($ftgw['fuzu']==0)	// ���¹������HP,MP��
	{
		if ($ftgw['bid'] == $rs['id'] && $ftgw['fatting']==1)
		{
			$newhp = $ftgw['hp']-$aobj->skillack;
			$newmp = $ftgw['mp']-$gs['s_ump']; // in here add mp part..<<<<<<<<<<<
		}
		else
		{
			$newhp = $gs['hp']-$aobj->skillack;
			$newmp = $gs['mp']-$gs['s_ump'];
		}
		//��Ѫʱ�����ﲻ��Ѫ
		if($rs['s_uhp']<0||$rs['s_ump']<0){	
			//$newhp = $gs['hp'];
		}

		if ($newhp<0) $newhp = 0;
		if ($newmp<0) $newmp=0;
		$r = $fight;
		$r['hp']			=$newhp;
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
		$newhp = $ftgw['hp'];
		$newmp = $ftgw['mp'];
	}
	
	// �����û�BB��Ϣ��
	// ���BB��ɱ����򲻼��Լ�������.
	
	######################�����ʣ��Ѫ����ħ��###########################
	if ($aobj->skillack < $ftgw['hp']) 
	{
		//����װ���������е�hp
		$sumhp = $att['hp1'] + $att['hp2'] + $row['addhp'];
		if($sumhp > $gwac1)
		{
			$addhp = $sumhp - $gwac1;
			$nhp = $rs['hp'];
			//�жϳ����hp�Ƿ񳬹����ֵ
			$sumhp1 = $addhp + $nhp;
			if($sumhp1 > $srchp1)
			{
				$addhp = $srchp1 - $nhp;
			}
		}
		else
		{
			$nhp = $sumhp + $rs['hp'] - $gwac1;
			$nhp = $sumhp + $rs['hp'] - $gwac1;
			$addhp = 0;
		}
		//$nhp = $rs['hp']-$aobj1->skillack;
		
	}
	//����װ�����ӵ�mp
	else $nhp = $rs['hp'];
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
		$addmp = 0;
	}
	//$nmp = $rs['mp']-$rs['s_ump']; 
	
	if ($nhp<0) $nhp=0;
	if ($nmp<0) $nmp=0;

	$_pm['mysql']->query("UPDATE userbb
				   SET hp={$nhp}, 
				       mp={$nmp},
					     addmp={$addmp},
					   addhp={$addhp}
				 WHERE id={$rs['id']} and uid={$_SESSION['id']}
			  ");
	
	if ($nhp == 0) 
	{
		$drops='���ź���ս��ʧ�ܣ�'; // bb die.
		// in here add fail option of records.
		$cUser = $_pm['mysql']->getOneRecord("SELECT pos,bid,failackvalue
												FROM battlefield_user
											   WHERE uid={$_SESSION['id']}
											");
		$sql = "SELECT hp FROM battlefield WHERE id = {$cUser['pos']}";
		$checkhp1 = $_pm['mysql'] -> getOneRecord($sql);
		if($checkhp1['hp'] >= $cUser['ackvalue'])
		{
			$_pm['mysql']->query("UPDATE battlefield
								 SET hp=hp-{$cUser['ackvalue']}
							   WHERE id={$cUser['pos']} AND hp >= {$cUser['ackvalue']}
							");
		}
		else
		{
			$_pm['mysql']->query("UPDATE battlefield
								 SET hp=0
							   WHERE id={$cUser['pos']}
							");
		}
	}
	else if ($newhp == 0) // gaiwu die
    {
		##################################################
		// �����û����ݡ���¼�û����������ٶԷ���Ӫ����
		$cUser = $_pm['mysql']->getOneRecord("SELECT pos,bid,addjgvalue,ackvalue,doublejg
												FROM battlefield_user
											   WHERE uid={$_SESSION['id']}
											");
		//����ֵ��ȡ��{ս��ʤ����������*[1��(X��20��/100)]}
		$jgvalue = intval( $cUser['addjgvalue']*(1-($rs['czl']-$gs['czl']-20)/1000) );
		if ($cUser['doublejg']==1) $jgvalue = $jgvalue*3;
		else $jgvalue=$jgvalue*2;

		if($jgvalue < 0){
			$jgvalue = 5;
		}
		$_pm['mysql']->query("UPDATE battlefield_user
		                         SET curjgvalue=curjgvalue+{$jgvalue}
							   WHERE uid={$_SESSION['id']}");

		// ���ٶԷ���ӪHP��
		$sql = "SELECT hp FROM battlefield WHERE id != {$cUser['pos']}";
		$checkhp = $_pm['mysql'] -> getOneRecord($sql);
		if($checkhp['hp'] >= $cUser['ackvalue'])
		{
			$_pm['mysql']->query("UPDATE battlefield
								 SET hp=hp-{$cUser['ackvalue']}
							   WHERE id!={$cUser['pos']} AND hp >= {$cUser['ackvalue']}"
							   );
		}
		else
		{
			$_pm['mysql']->query("UPDATE battlefield
								 SET hp=0
							   WHERE id!={$cUser['pos']}"
							   );
		}

		$drops = "<br/><font size=+1>��ϲ��������˱���ս����ʤ����</font><br/>������� <font size=30% color=yellow>{$jgvalue}</font> �������";
		/*
		$word = " , <font style=font-size:130%>{$gs['username']}</font> �ı��� <font style=font-size:130%>{$gs['name']}</font> �ɹ�������� <font style=font-size:130%>1</font> ��ս����";
		
		$task = new task();
		$task->saveGword($word);
		*/
	}
	else $drops='';

	if ($newhp == 0) {
		$r =$_SESSION['fight' . $_SESSION['id']];
		$r['hp']		= $newhp;
		$r['mp']		= $newmp;
		$r['fatting']	= 0;
		$r['fuzu']		= 0;
		$r['gid']		= 0;
		$_SESSION['fight'.$_SESSION['id']]= $r;
	}
	// Free resource.
	$_pm['mem']->memClose();
	header('Content-Type:text/html;charset=GBK'); 
	$sql = "SELECT addmp,addhp FROM userbb WHERE uid = {$_SESSION['id']} and id = {$rs['id']}";
		$add = $_pm['mysql'] -> getOneRecord($sql);
		$nhp += $add['addhp'];
		if($nhp > $srchp1)
		{
			$nhp = $srchp1;
		}
		$nmp += $add['addmp'];
		if($nmp > $srcmp1)
		{
			$nmp = $srcmp1;
		}
	if(!empty($att['hp1']) && empty($att['mp1']))
	{
		$echo_str =  $nhp . ',' . $nmp. ',' . $bb.',<br />��Ѫ'.$att['hp1'].'#'. $newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	
	else if(!empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] > 0)
	{
		$echo_str = $nhp . ',' . $nmp. ',' . $bb.',<br />��Ѫ'.$att['hp1'].'&nbsp;==<br />��ħ'.$att['mp1'].'&nbsp;#'. $newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	else if(!empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] < 0)
	{
		$echo_str = $nhp . ',' . $nmp. ',' . $bb.',<br />��Ѫ'.$att['hp1'].'&nbsp;==<br /ʧħ'.$att['mp1'].'&nbsp;#'. $newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	else if(empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] < 0)
	{
		$echo_str = $nhp . ',' . $nmp. ',' . $bb.'<br /> ʧħ'.$att['mp1'].'&nbsp;#'. $newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	else if(empty($att['hp1']) && !empty($att['mp1']) && $att['mp1'] > 0)
	{
		$echo_str = $nhp . ',' . $nmp. ',' . $bb.',<br />��ħ'.$att['mp1'].'&nbsp;#'. $newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	else
	{
		$echo_str = $nhp . ',' . $nmp. ',' . $bb.'#'.$newhp . ',' . $gw.'#' . $drops . '#' . $word;	
	}
	$echo_str .= "*".$Crit;	//�Ƿ񱩻�
	$ack_type = 0;
	$echo_str .= "*".$ack_type;	//���й���
	echo $echo_str;
}
else
{	$drops='���� ' . $grs['name'].' �����ˣ�����';
	header('Content-Type:text/html;charset=GBK'); 
	echo '0,0,0#0,0#' . $drops . '#' . $word;	
}

// =========================




?>