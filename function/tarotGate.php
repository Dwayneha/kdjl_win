<?php
header('Content-Type:text/html;charset=gbk');
require_once('../config/config.game.php');
require_once('../sec/dblock_fun.php');
secStart($_pm['mem']);
$a = getLock($_SESSION['id']);
if(!is_array($a)){
	realseLock();
	die('��������æ�����Ժ����ԣ�');
}

require_once(dirname(__FILE__).'/../socketChat/config.chat.php');
$s=new socketmsg();
$team=new team($_SESSION['team_id'],&$s);


$teamInfo=$team->getTeamInfo();
if($_GET['id'])
{
	if(!isset($_SESSION['teamfb']))
	{
		$_SESSION['teamfb'] = array();
		$_SESSION['teamfb'][] = $_GET['id'];
	}
	else
	{
		foreach($_SESSION['teamfb'] as $info)
		{
			if($info == $_GET['id'])
			{
			
				unset($_SESSION['teamfb']);
				die("error");
			}
			else
			{
				$_SESSION['teamfb'][] = $_GET['id'];
			}
		}
	}
}
$ct=0;
foreach($teamInfo['members'] as $mem)
{
	if($mem['state']==1)
	{
		$ct++;
		$uidarr1[] = $mem['uid'];
	}
}

if($ct<2){
	realseLock();
	$team->setTeamState(array(
							'team_fuben_card_step_num'=>-1,
							'team_fuben_step'=>array(0,0),
							'fubensjoj' => 0
							));
	
	$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|goTarot'),$uidarr1);//echo '['.__LINE__."]<br>";
	die('��������æ�����Ժ�����111��');
}

if($_GET['op'] == 'show'){//��ʾѪ��
	
	$teamInfo=$team->getTeamInfo();
	
	foreach($teamInfo['members'] as $mem){
		if($mem['state'] == 1){
			$marr = $_pm['mysql'] -> getOneRecord('SELECT player.nickname as nickname,hp,mp,srchp,srcmp,addhp,addmp,level,player.headimg FROM player,userbb WHERE player.id = '.$mem['uid'].' AND player.mbid = userbb.id');
			$isleader=$team->isTeamLeader($mem['uid'],$_SESSION['team_id']);
			if($isleader){//�ӳ�../images/bb/t13.gif
				$leader = '<div class="leader">	<!--�ӳ�-->
							<div class="name">'.$marr['nickname'].'</div>
							<div class="level">'.$marr['level'].'</div>
							<div class="avatar"><img src="../images/tarot/face'.$marr['headimg'].'.gif" /></div>
							<div class="red"><p style="width:'.(100*($marr['hp']+$marr['addhp'])/($marr['srchp']+$marr['addhp'])).'%"></p></div>	<!--Ѫ�������޸�p��width�ٷֱ�ֵ-->
							<div class="blue"><p style="width:'.(100*($marr['mp']+$marr['addmp'])/($marr['srcmp']+$marr['addmp'])).'%"></p></div>
						</div>';
			}else{//��Ա
				$member .= '<div class="team">	<!--��Ա-->
							<div class="name">'.$marr['nickname'].'</div>
							<div class="level">'.$marr['level'].'</div>
							<div class="avatar"><img src="../images/tarot/face'.$marr['headimg'].'.gif" /></div>
							<div class="red"><p style="width:'.(100*($marr['hp']+$marr['addhp'])/($marr['srchp']+$marr['addhp'])).'%"></p></div>
							<div class="blue"><p style="width:'.(100*($marr['mp']+$marr['addmp'])/($marr['srcmp']+$marr['addmp'])).'%"></p></div>
						</div>';
			}
			unset($marr);
		}
	}
	echo $leader.$member;
}
else if($_GET['op'] == 'o'){
	//��ʾ������ҵ��ƺ������û�з�����
	$flag = '1';
	$ar = unserialize($_pm['mem']->get('tarot_info1_'.$_SESSION['team_id']));
	if(is_array($ar)){
		foreach($ar as $v){
			if($v['type'] == 1){
				$ar1[] = $v;
			}else{
				$ar2[] = $v;
			}
			if($v['uid'] == $_SESSION['id']){
				continue;
			}
			
			$jsstr.='**|'.$v['type'].'~,~'.$v['msg'];
			$i++;
		}
	}
	if(count($ar2) < 1){
		$type2 = 5;
	}else{
		$type2 = 5 - count($ar2);
	}
	$type1 = 5 - count($ar1);
	if($type1 > 0){
		$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE sj = 0 AND flag = 0 AND mapid = '.$_SESSION['team_inmap'];
		$row = $_pm['mysql'] -> getRecords($sql);
		$len = count($row) - 1;
		for($j = 0;$j < $type1;$j++){
			$res = $row[rand(0,$len)];
			$msg = '<span class="text">'.showt($res['effect']).'</span>';
			$jsstr.='**|1~,~'.$msg;
			$ar[]=array('type' => 1,'msg' => $msg,'uid' => 0);
		}
	}
	if($type2 > 0){
		$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE sj != 0 AND flag = 0 AND mapid = '.$_SESSION['team_inmap'];
		$row = $_pm['mysql'] -> getRecords($sql);
		$len = count($row) - 1;
		for($j = 0;$j < $type2;$j++){
			$res = $row[rand(0,$len)];
			$msg = '<span class="text2">'.showt($res['effect']).'</span>';
			$jsstr.='**|2~,~'.$msg;
			$ar[]=array('type' => 2,'msg' => $msg,'uid' => 0);
		}
	}
	$_pm['mem']->set(array('k'=>'tarot_info1_'.$_SESSION['team_id'],'v'=>$ar));
	echo $jsstr;
}else{
	
	$srctime = 3;
	#################����һ�����ʱ��################
	$time = $_SESSION['time'.$_SESSION['id']];
	if(empty($time)){	
		$_SESSION['time'.$_SESSION['id']] = time();
	}else{
		$nowtime = time();
		$ctime = $nowtime - $time;
		if($ctime < $srctime){
			realseLock();
		//	die("11");//û�дﵽ���ʱ��
		}
		else{
			$_SESSION['time'.$_SESSION['id']] = time();
		}
	}

	$id = intval($_GET['id']);
	if($id < 1 || $id > 10){
		realseLock();
		die('1');//��Ч������
	}
	if($id <= 5){
		$point1 = $team -> get_team_funben_card_step();
	}else{
		$point2 = $team -> get_team_funben_card_step($_SESSION['id'],'_sj');
	}

	//�ж��Ƿ��ǵ�����;
	if($point1 == 3 || $point2 == 3){
	$point = 3;
	}else{
		$point = $id <= 5?$point1:$point2;
	}
	if($point == 3)	//���Ƽ�¼
	{
		if(is_array($_SESSION['point3_card_record']))
		{
			if(in_array($_GET['id'],$_SESSION['point3_card_record']))
			{
				$_pm['mem']->del('tarot_info_'.$_SESSION['team_id']);
				$_pm['mem'] -> del('tarot_times_'.$_SESSION['team_id']);
				$res_team = $_pm['mysql'] -> getOneRecord("SELECT * FROM team WHERE creator = '{$_SESSION['id']}'");
				
				$_pm['mysql'] ->query("DELETE FROM team_members WHERE team_id = '{$res_team['id']}'");
				$_pm['mysql'] ->query("DELETE FROM team WHERE creator = '{$_SESSION['id']}'");
				unset($_SESSION['teamfb']);
				die("error");
			}
		}
		$_SESSION['point3_card_record'][] = $_GET['id'];
	}
	$teamState = $team ->getTeamState();
	
	//$point = 1;
	if($point == 0){
		realseLock();
		die('2');//�Ѿ�����
	}
	if($teamState['team_fuben_card_step_num'] == 3){
		$isleader=$team->isTeamLeader($_SESSION['id'],$_SESSION['team_id']);
		if(!$isleader){
			realseLock();
			die('3');//���Ƕӳ������ܷ���
		}
	}
	if($id <= 5 && $point != 3){
		$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE sj = 0 AND flag = 0 AND mapid = '.$_SESSION['team_inmap'];
		if($_SESSION['id'] == 1053107){
			$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE id=390';
		}
		
	}else if($id >= 5 && $point != 3){
		$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE sj != 0 AND flag = 0 AND mapid = '.$_SESSION['team_inmap'];
	}else if($point == 3){
		$tarot_times = unserialize($_pm['mem']->get('tarot_times_'.$_SESSION['team_id']));
		//echo $tarot_times.'<br />';
		$tarot_times1 = $tarot_times + 1;
		$_pm['mem']->set(array('k'=>'tarot_times_'.$_SESSION['team_id'],'v'=>$tarot_times1));
		if($tarot_times > 7){
			$_pm['mem']->del('tarot_info_'.$_SESSION['team_id']);
			$_pm['mem'] -> del('tarot_times_'.$_SESSION['team_id']);
			$team -> set_team_funben_card_prize_got();
			realseLock();
			die('4');//��Ч������
		}else if($tarot_times == 7){
			$_pm['mem']->del('tarot_info_'.$_SESSION['team_id']);
			$_pm['mem'] -> del('tarot_times_'.$_SESSION['team_id']);
			$team -> set_team_funben_card_prize_got();
			$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE flag = 1 AND boss != "0" AND boss != "" AND mapid = '.$_SESSION['team_inmap'];
		}else{
			$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE flag = 1 AND mapid = '.$_SESSION['team_inmap'];
			//$sql = 'SELECT id,effect,sj,boss,img,name FROM tarot WHERE flag = 1 and id = 420';
		}
	}//echo $sql.'<br />';
	$tarot = $_pm['mysql'] -> getRecords($sql);

	if(!is_array($tarot)){
		realseLock();
		die('5');//��Ч������
	}
	$max = count($tarot) - 1;
	$rand = rand(0,$max);
	$newTarot = $tarot[$rand];
	
	if($newTarot > 0 && $newTarot['sj'] > 0){
		$_pm['mysql'] -> query('UPDATE player_ext SET sj = sj-'.$newTarot['sj'].' WHERE uid = '.$_SESSION['id'].' AND sj >= '.$newTarot['sj']);
		if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
			realseLock();
			die('6');//û���㹻��ˮ��
		}
	}
	
	if($newTarot['boss'] != 0 || ($point < 3 && $id <= 5)){//��������Ϊboss���ߵ�һ���ط�����Ƶ�ʱ����Ϊ��ȡ������״̬
		$team -> set_team_funben_card_prize_got();
		$_pm['mem']->del('tarot_info_'.$_SESSION['team_id']);
		$_pm['mem'] -> del('tarot_times_'.$_SESSION['team_id']);
		if($newTarot['boss'] != 0){
			//д��������
			$team -> setTeamMonsters($newTarot['boss']);
		}
	}
	
	if($point < 3 && $id > 5){
		$team -> set_team_funben_card_prize_got($_SESSION['id'],'_sj');
	}
	
	
	//Ч�� ��д��ʽ��all_hp_add:10%|all_money_add:100|all_giveitems:747:1:3:1,738:1:10:1,739:1:10:1|all_fight:1|fight_one:0|fight_all:0|all_money_less:100|all_hp_less:10%|all_exp_add:100|money_add:100|exp_add:100|giveitems:1225:15,1308:10,734:1,1142:3|fight_one:0
	$effect = explode('|',$newTarot['effect']);
	$_SESSION['carddddd'].='|card id='.$newTarot['id'].'|';
	if($newTarot['boss'] != 0){
		if( $_SESSION['gs'] == 3 || !empty($_SESSION['gs']) )
		{
			$_SESSION['gs'] = "";
			unset($_SESSION['gs']);
		}
		//��boss
		$_pm['mem']->del('tarot_info_'.$_SESSION['team_id']);
		$team -> setTeamMonsters($newTarot['boss']);
		$teamInfo=$team->getTeamInfo();
		foreach($teamInfo['members'] as $mem){
			if($mem['state'] == 1){
				$uidarr[] = $mem['uid'];
			}
		}
		$team->setTeamState(
							array(
								'team_fuben_boss'=>1								)
							);
		echo 'boss===>';
		
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|getTeamFightMod'),$uidarr);
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->����boss�ˣ�����'),$uidarr);
	}else{
		foreach($effect as $v){
			$arr = explode(':',$v);
			switch ($arr[0]){
				case 'all_hp_less'://ȫ��HP����
					$teamInfo=$team->getTeamInfo();
					if(strpos($arr[1],'%') === false){//���ǰٷֱ�
						foreach($teamInfo['members'] as $mem){
							if($mem['state'] == 1){
								$mbid = getMbid($mem['uid']);
								$hp = getMaxHp($mbid);
								if($hp['totalhp'] <= $arr[1]){
									$_pm['mysql'] -> query('UPDATE userbb SET hp = 0,addhp = 0 WHERE id = '.$mbid);
								}else{
									$h = intval($arr[1] - $hp['hp']);
									if($h < 0){
										$h = $arr[1];
										$ah = 0;
									}else{
										$h = $hp['hp'];
										$ah = $h;
									}
									$_pm['mysql'] -> query('UPDATE userbb SET hp = hp - '.$h.',addhp = addhp - '.$ah.' WHERE id = '.$mbid.' AND hp >= '.$h.' AND addhp >= '.$addhp);
								}
								$uidarr[] = $mem['uid'];
							}
						}
					}else{//�ǰٷֱ�
						$num = str_replace('%','',$arr[1]) * 0.01;
						foreach($teamInfo['members'] as $mem){
							if($mem['state'] == 1){
								$mbid = getMbid($mem['uid']);
								$_pm['mysql'] -> query('UPDATE userbb SET hp = hp - srchp * '.$num.',addhp = addhp - addhp * '.$num.' WHERE id ='.$mbid);
								$uidarr[] = $mem['uid'];
							}
						}
					}
					
					
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|changhp'),$uidarr);
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->ȫ�����'.$arr[1].'��Ѫ����'),$uidarr);
					echo 'hp===>';
					break;
				case 'all_money'://ȫ����ͬ�Ƚ�ҽ�����ͷ����ͷ��
					$teamInfo=$team->getTeamInfo();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							moneyAdd($mem['uid'],$arr[1]);
							$uidarr[] = $mem['uid'];
						}
					}
					if($arr[1] < 0){
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->ȫ��۳�'.abs($arr[1]).'��ң�'),$uidarr);
					}else{
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->ȫ����'.$arr[1].'��ң�'),$uidarr);
					}
					echo 'money===>';
					break;
				case 'all_giveitems'://ȫ������ͬ���߽���
					$teamInfo=$team->getTeamInfo();
					$uidarr=array();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$uidarr[] = $mem['uid'];
							
						}
					}
					
					echo 'items===>';
					$itemstr = str_replace('all_giveitems:', '', $v);
					$it = getItem($uidarr,$itemstr);
					if($it == '���ź�����û�л���κε��ߣ�'){
						$nit = '���ź�����û�л���κε��ߣ�';
					}else $nit = 'ȫ��'.$it;
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->'.$nit),$uidarr);
					break;
				case 'all_fight'://����ս��  ��ȡ����
					if( $_SESSION['gs'] == 3 || !empty($_SESSION['gs']) )
					{
						$_SESSION['gs'] = "";
						unset($_SESSION['gs']);
					}
					$team -> setTeamMonsters($arr[1]);
					echo 'fight===>';
					$teamInfo=$team->getTeamInfo();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$uidarr[] = $mem['uid'];
						}
					}
					$team->setTeamState(
							array(
								'team_fuben_boss'=>0								)
							);
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->����ս����������������'),$uidarr);
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|getTeamFightMod'),$uidarr);
					break;
				case 'hit_one'://���һ�˱��߳�����
					
					$teamInfo=$team->getTeamInfo();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$isleader=$team->isTeamLeader($mem['uid'],$_SESSION['team_id']);
							if($isleader){//�ӳ������߳�
								continue;
							}
							$memarr[] = $mem['uid'];
						}
					}
					$len = count($memarr) - 1;
					$i = rand(0,$len);
					
					foreach($uidarr1 as $v){
						if($v == $memarr[$i]){
							continue;
						}
						$nuid[] = $v;
					}
					
					$hit = $_pm['mysql'] -> getOneRecord('SELECT id,nickname FROM player WHERE id = '.$memarr[$i]);
					if(count($nuid) > 1){
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->���Ķ���'.$hit['nickname'].'���߳�ս����'),$nuid);
					}else{//ֻ�жӳ�һ���˵�ʱ��
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|outTarot->���ڸ����������㣬ǿ���뿪��������սʧ�ܡ�'),$nuid);
						$team->setTeamState(array(
							'team_fuben_card_step_num'=>-1,
							'team_fuben_step'=>array(0,0),
							'fubensjoj' => 0
							));
					}					
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|outTarot->����̫����϶�ħ���㽫��ǿ���߳����������´������ɣ���ս����ʧ�ܡ�'),$memarr[$i]);
					echo 'hit_one->'.$hit['nickname'].'===>';
					$team -> kickMember($memarr[$i],true);					
					break;
				case 'hit_all'://ȫ�屻�߳�����
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|goTarot->����̫����϶�ħ�����ǽ���ǿ��t�������������������ɣ���ս����ʧ��!'),$uidarr1);
					echo 'hit_all===>';
					$team -> disbandTeam(false);
					break;
				case 'all_exp_add'://ȫ���þ��齱��
					$teamInfo=$team->getTeamInfo();
					$t = new task();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$t->saveExps($arr[1],$mem['uid']);
							$uidarr[] = $mem['uid'];
						}
					}
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->ȫ����'.$arr[1].'�㾭�飡'),$uidarr);
					echo 'exp_all===>';
					break;
				case 'all_hp_add'://ȫ��HP����
					$teamInfo=$team->getTeamInfo();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$mbid = getMbid($mem['uid']);
							$zbarr = getzbAttrib($mbid);
							$hp = getMaxHp($mbid)>0?getMaxHp($mbid):0;
							$chp = ($hp['scrhp'] - $hp['hp'])>0?($hp['scrhp'] - $hp['hp']):0;
							$addchp = ($arr['hp'] - $hp['addhp'])>0?($arr['hp'] - $hp['addhp']):0;
							$cchp = ($chp+$addchp)>0?($chp+$addchp):0;
							if(!is_array($zbarr) ||empty($zbarr['hp'])){
								$zbarr['hp'] = 0;
							}
							if(strpos($arr[1],'%') === false){//���ǰٷֱ�
								if($arr[1] > $cchp){//����
									$_pm['mysql'] -> query('UPDATE userbb SET hp = srchp,addhp = '.$zbarr['hp'].' WHERE id = '.$mbid);
									/*echo 'UPDATE userbb SET hp = srchp,addhp = '.$zbarr['hp'].' WHERE id = '.$mbid;
									echo '<br />'.__line__.'<br />';*/
								}else{
									if($addchp > $arr[1]){
										$_pm['mysql'] -> query('UPDATE userbb SET addhp = addhp + '.$arr[1].' WHERE id = '.$mbid);
										/*echo 'UPDATE userbb SET hp = srchp,addhp = '.$zbarr['hp'].' WHERE id = '.$mbid;
										echo '<br />'.__line__.'<br />';*/
									}else{
										$a = intval($arr[1] - $addchp);
										$_pm['mysql'] -> query('UPDATE userbb SET addhp = '.$zbarr['hp'].',hp = hp+'.$a.' WHERE id = '.$mbid);
										/*echo 'UPDATE userbb SET hp = srchp,addhp = '.$zbarr['hp'].' WHERE id = '.$mbid;
										echo '<br />'.__line__.'<br />';*/
									}
								}
							}else{
								$num = str_replace('%','',$arr[1]) * 0.01;
								$hhp = round($hp['srchp'] * $num) + $hp['hp'];
								$haddhp = round($zbarr['hp'] * $num) + $hp['addhp'];
								if($hhp > $hp['srchp']){
									$hhp = $hp['srchp'];
								}
								if($haddhp > $zbarr['hp']){
									$haddhp = $zbarr['hp'];
								}
								$_pm['mysql'] -> query('UPDATE userbb SET addhp = '.$haddhp.',hp='.$hhp.' WHERE id = '.$mbid);
							}
							$uidarr[] = $mem['uid'];
						}
					}
					
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|changhp'),$uidarr);
					$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|alertTarot->ȫ������'.$arr[1].'��Ѫ����'),$uidarr);
					echo 'hp_all===>';
					break;
				case 'money_add'://���˻�ý�ҽ���
					moneyAdd($_SESSION['id'],$arr[1]);
					if($arr[1] < 0){
						$ret='�۳���ң�'.$arr[1];
					}else{
						$ret='��ý�ң�'.$arr[1];
					}
					break;
				case 'exp_add'://���˻�þ��齱��
					$t = new task();
					$t->saveExps($arr[1]);
					$ret='��þ��飺'.$arr[1];
					break;
				case 'giveitems'://���˻�õ��߽���
					$itemstr = str_replace('giveitems:', '', $v);
					$ret = getItem(array($_SESSION['id']),$itemstr);
					break;
				case 'hit_me'://���˱��߳�����
					$isleader=$team->isTeamLeader($_SESSION['id'],$_SESSION['team_id']);
					if($isleader){
						$team -> disbandTeam(false);
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|goTarot->����̫����϶�ħ�����ǽ���ǿ���߳����������´������ɣ���ս����ʧ�ܣ�'),$uidarr1);
					}else{
						$team -> kickMember($_SESSION['id'],true);
						$ret = $_SESSION['nickname'].'���߳�ս����';
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|goTarot->����̫����϶�ħ���㽫��ǿ���߳����������´������ɣ���ս����ʧ�ܡ�'),$_SESSION['id']);
					}
					$teamInfo=$team->getTeamInfo();
					foreach($teamInfo['members'] as $mem){
						if($mem['state'] == 1){
							$uidarr[] = $mem['uid'];
						}
					}
					if(count($uidarr) == 1){
						$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|goTarot->���ڸ����������㣬��ǿ���뿪��������սʧ�ܡ�'),$uidarr);
						$team->setTeamState(array(
							'team_fuben_card_step_num'=>-1,
							'team_fuben_step'=>array(0,0),
							'fubensjoj' => 0
							));
					}
					break;
				default:
					echo '�ƣ�'.$newTarot['id'].'��д����'.$newTarot['effect'].'��������';
					break;
			}
		}
	}
	if($point < 3){
		$ar = unserialize($_pm['mem']->get('tarot_info1_'.$_SESSION['team_id']));
		if($id <= 5){
			$e = '<span class="text">'.$_SESSION['nickname'].'<br />'.$ret.'</span>';
			$type = 1;
		}
		else{
			$e = '<span class="text2">'.$_SESSION['nickname'].'<br />'.$ret.'</span>';
			$type = 2;
		}
		echo $e;
		$ar[]=array('type' => $type,'msg' => $e,'uid' => $_SESSION['id']);
		$_pm['mem']->set(array('k'=>'tarot_info1_'.$_SESSION['team_id'],'v'=>$ar));
	}else{
		//������Ҫ���������ƴ�����
		$ar = unserialize($_pm['mem']->get('tarot_info_'.$_SESSION['team_id']));
		$ar[]=array('id' => $id,'img' => $newTarot['img']);
		echo '<pre>
line='.__LINE__.'
';
var_dump($newTarot);
echo '</pre>
';
		$_pm['mem']->set(array('k'=>'tarot_info_'.$_SESSION['team_id'],'v'=>$ar));
		$rs=$s->sendMsg(iconv('gbk','utf-8','SYSN|tarot->'.$id.'->'.$newTarot['img']),$uidarr1);
		//echo $rs.'aaa';
		//echo '['.__LINE__."]<br>";
		echo $newTarot['img'];
	}
}
realseLock();
function getMbid($uid){//�õ�����id
	global $_pm;
	$arr = $_pm['mysql'] -> getOneRecord('SELECT mbid FROM player WHERE id = '.$uid);
	return $arr['mbid'];
}

function getMaxHp($id){//�õ�ʣ��hp
	global $_pm;
	$arr = $_pm['mysql'] -> getOneRecord('SELECT srchp,hp,addhp,(hp+addhp) as totalhp FROM userbb WHERE id = '.$id);
	return $arr;
}

function moneyAdd($uid,$num){
	global $_pm;
	if($num < 0){
		$_pm['mysql'] -> query('UPDATE player SET money = money +'.$num.' WHERE id = '.$uid.' AND money >= '.$num);
		if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
			$_pm['mysql'] -> query('UPDATE player SET money = 0 WHERE id = '.$uid);
		}
	}else{
		$_pm['mysql'] -> query('UPDATE player SET money = money +'.$num.' WHERE id = '.$uid);
	}
}

function getItem($uidarr,$str){
	global $_pm,$s,$point;
	//echo $str;
	$flag = 0;
	$propslist = explode(',', $str);
	if (is_array($propslist)){
		$task = new task();
		foreach ($propslist as $k => $v){
			$inarr = explode(':', $v);
			if(is_array($inarr)){
				if (rand(1, intval($inarr[2])) == 1){	//  rand hits
					$flag = 1;
					if($uidarr == 0){
						$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
						if(empty($retstr)){
							$retstr = '��� '.$prs['name'].'&nbsp;'.$inarr[1].' ��';
						}else{
							$retstr .= ",".$prs['name'].'&nbsp;'.$inarr[1].' ��';
						}
					}else{
						foreach($uidarr as $v){
							$task->saveGetPropsMore($inarr[0],$inarr[1],0,$v);
							$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
							if(empty($retstr)){
								$retstr = '��� '.$prs['name'].'&nbsp;'.$inarr[1].' ��';
							}else{
								$retstr .= ",".$prs['name'].'&nbsp;'.$inarr[1].' ��';
							}
							if($inarr[3] == '2'){//������
								$p = $_pm['mysql'] -> getOneRecord('SELECT nickname FROM player WHERE id = '.$v);
								$msg=iconv('gbk','utf-8','��ϲ��� '.$p['nickname'].'������������'.$point.'�صĽ�����'.$prs['name'].'&nbsp;'.$inarr[1].' ��');
								$s->sendMsg('an|'.$msg,'__ALL__');
							}
						}
					}
				}
			}
		}
		if($flag == 0){
			return '���ź�����û�л���κε��ߣ�';
		}
		return $retstr;
	}
}

function showt($str){
	$effect = explode('|',$str);
	foreach($effect as $v){
		$arr = explode(':',$v);
		switch ($arr[0]){
			case 'money_add'://���˻�ý�ҽ���
				if($arr[1] < 0){
					$ret='�۳���ң�'.$arr[1];
				}else{
					$ret='��ý�ң�'.$arr[1];
				}
				break;
			case 'exp_add'://���˻�þ��齱��
				$ret='��þ��飺'.$arr[1];
				break;
			case 'giveitems'://���˻�õ��߽���
				$itemstr = str_replace('giveitems:', '', $v);
				$ret = getItem(0,$itemstr);
				break;
			case 'hit_me'://���˱��߳�����
				$ret = '���һ���߳�������';
				break;
			default:
				$ret = '�ƣ�'.$newTarot['id'].'��д����'.$newTarot['effect'].'��������';
				break;
		}
	}
	return $ret;
}
$_pm['mem']->memClose();
?>