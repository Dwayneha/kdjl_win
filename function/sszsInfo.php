<?php
header('Content-Type:text/html;charset=GB2312');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$id = intval($_GET['id']);

require_once('../sec/dblock_fun.php');
$a = getLock($_SESSION['id']);
if(!is_array($a)){
	realseLock();
	die('��������æ�����Ժ����ԣ�');
}
if($_GET['op'] == 'img'){
	if($id < 0){
		realseLock();
		die('idС��1');//idС��1
	}
	$membbid = unserialize($_pm['mem']->get('db_bbid')); 
	$bbarr = $_pm['mysql'] -> getRecords('SELECT super_zs.id,next_pet_id FROM super_zs WHERE cur_pet_id = '.$id);//echo 'SELECT next_pet_id,cardimg FROM super_zs,bb WHERE bb.id=super_zs.cur_pet_id AND cur_pet_id = '.$id;
	//print_r($bbarr);exit;
	if(!is_array($bbarr)){
		realseLock();
		die('δ����');
	}
	foreach($bbarr as $k => $v){
		if($k == 0){
			$res .= '<div class="sd_pet r00" id="p_p'.($k+1).'">
		<img src="'.IMAGE_SRC_URL.'/bb/'.$membbid[$v['next_pet_id']]['cardimg'].'" onclick="sszsstr('.$v["id"].',0,this);sszsbbid='.$v["id"].';copyWord(\''.$v["name"].'\')" style="opacity:1; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=100,finishOpacity=100);cursor:pointer;" />
		</div>';
		}else{
			$res .= '<div class="sd_pet r00" id="p_p'.($k+1).'">
		<img src="'.IMAGE_SRC_URL.'/bb/'.$membbid[$v['next_pet_id']]['cardimg'].'" onclick="sszsstr('.$v["id"].',0,this);sszsbbid='.$v["id"].';copyWord(\''.$v["name"].'\')" style="opacity:0.5; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=50,finishOpacity=50);cursor:pointer;" />
		</div>';
		}
	}
	echo $res;
}else if($_GET['op'] == 'str'){
	$sid = intval($_GET['sid']);
	if($sid >��){
		$sql = 'SELECT next_pet_id,name,need_level,need_czl,need_props,next_pet_id FROM super_zs,bb WHERE bb.id = cur_pet_id AND cur_pet_id = '.$sid.' limit 1';
	}else{
		$sql = 'SELECT next_pet_id,name,need_level,need_czl,need_props,next_pet_id FROM super_zs,bb WHERE bb.id = cur_pet_id AND super_zs.id = '.$id;
	}
	$arr = $_pm['mysql'] -> getOneRecord($sql);
	if(!is_array($arr)){
		realseLock();
		die('δ����');
	}
	$memprops = unserialize($_pm['mem']->get('db_propsid')); 
	$parr1 = explode(',',$arr['need_props']);
	foreach($parr1 as $v){
		$parr = explode('|',$v);
		$pstr .= $memprops[$parr[0]]['name'].'��'.$parr[1].'��';
	}
	//ת���׶�x100000
	$m = $_pm['mysql'] -> getOneRecord('SELECT zs_progress FROM super_jh WHERE pet_id ='.$arr['next_pet_id']);
	$needmoney = $m['zs_progress'] * 100000;
	$membbid = unserialize($_pm['mem']->get('db_bbid'));
	$pstr = substr($pstr,0,-2);
	$str = 'ת������ȼ���'.$arr['need_level'].'<br />
			ת������ɳ���'.$arr['need_czl'].'<br />
			ת�������ң�'.$needmoney.'<br />
			ת��������ϣ�'.$pstr.'<br />
			ת������'.$membbid[$arr['next_pet_id']]['name'].'	 
		  ';
	echo $str;
}else if($_GET['op'] == 'zs'){
	$oldbid = intval($_GET['old']);
	$newbid = intval($_GET['newid']);
	$wp1 = intval($_GET['wp1']);
	$wp2 = intval($_GET['wp2']);
	$srctime = 30;
	#################����һ�����ʱ��################
	$time = $_SESSION['time'.$_SESSION['id']];
	if(empty($time))
	{	
		$_SESSION['time'.$_SESSION['id']] = time();
	}
	else
	{
		$nowtime = time();
		$ctime = $nowtime - $time;
		if($ctime < $srctime && $_GET['type'] != 'do' && $_GET['type1'] != 'check')
		{
			realseLock();
			die("���Ժ������");//û�дﵽ���ʱ��
		}
		else
		{
			$_SESSION['time'.$_SESSION['id']] = time();
		}
	}

	if($oldbid < 1 && $newbid < 1){
		realseLock();
		die('��û��ѡ��Ҫת���ĳ��������ѡ��Ĳ�����ʥ���');
	}
	$bb = $_pm['mysql'] -> getOneRecord('SELECT * FROM userbb WHERE id = '.$oldbid);
	if(!is_array($bb)){
		realseLock();
		die('û����Ӧ�ĳ��');
	}
	
	if($bb['wx'] != 7){
		realseLock();
		die('�ó������ʥ�������ʹ�ô˹��ܣ�');
	}
	
	if($newbid > 0){
		$sql = 'SELECT cur_pet_id,need_level,need_czl,need_props,base_success_rate,next_pet_id FROM super_zs WHERE id = '.$newbid;
	}else {
		$membbname = unserialize($_pm['mem']->get('db_bbname'));
		$sql = 'SELECT cur_pet_id,need_level,need_czl,need_props,base_success_rate,next_pet_id FROM super_zs WHERE cur_pet_id = '.$membbname[$bb['name']]['id'].' limit 1';
	}
	$need = $_pm['mysql'] -> getOneRecord($sql);
	
	//�ж������Ƿ�����
	if($bb['level'] < $need['need_level'] || $bb['czl'] < $need['need_czl']){
		realseLock();
		die('�ȼ�����'.$need['need_level'].'��ɳ�����'.$need['need_czl']);
	}
	
	//��Ҫ��Ʒ�ж�
	$parr1 = explode(',',$need['need_props']);
	if(is_array($parr1)){
		foreach($parr1 as $v){
			$parr = explode('|',$v);
			$b = $_pm['mysql'] -> getOneRecord('SELECT id,sums FROM userbag WHERE uid = '.$_SESSION['id'].' AND pid = '.$parr[0].' AND sums >= '.$parr[1]);//echo 'SELECT id,sums FROM userbag WHERE uid = '.$_SESSION['id'].' AND pid = '.$parr[0].' AND sums >= '.$parr[1];
			if(!is_array($b)){
				realseLock();
				die('��Ӧ�ı���Ʒ������');
			}
		}
	}
	
	if($wp1 == $wp2 && $wp1 > 0){
		$wpcheck = $_pm['mysql'] -> getOneRecord('SELECT sums FROM userbag WHERE id = '.$wp1.' AND sums >= 2');
		if(!is_array($wpcheck)){
			realseLock();
			die('���߲��㣡');
		}
	}
	$p1=$p2=array();
	if($wp1 > 0){
		$p1 = $_pm['mysql'] -> getOneRecord( 'SELECT pid,effect FROM userbag,props WHERE userbag.id='.$wp1.' AND props.id = userbag.pid');
	}
	if($wp2 > 0){
		$p2 = $_pm['mysql'] -> getOneRecord( 'SELECT pid,effect FROM userbag,props WHERE userbag.id='.$wp2.' AND props.id = userbag.pid');
	}
	$limit = $_pm['mysql'] -> getOneRecord('SELECT max_czl,zs_line,zs_progress FROM super_jh WHERE pet_id = '.$need['next_pet_id']);
	if(strpos($p1['effect'],'sszs') === false && strpos($p2['effect'],'sszs') === false && strpos($p1['effect'],'cszsczlbh') === false&& strpos($p2['effect'],'cszsczlbh') === false && count($p1)>0&& count($p2)>0){//˵���Ǽ����Ե��ߣ�����ͬʱ������
		realseLock();
		die('ͬʱ���ܼ������������Եĵ��ߣ�');
	}
	//����ж�
	$need_money = $limit['zs_progress'] * 100000;
	
	$user = $_pm['user']->getUserById($_SESSION['id']);
	if($user['money'] < $need_money){
		realseLock();
		die('��Ҳ���'.$need_money);
	}
	if($wp1 > 0){
		$_pm['mysql']->query('UPDATE userbag SET sums = sums - 1 WHERE uid = '.$_SESSION['id'].' AND id = '.$wp1.' AND sums > 0');
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			realseLock();
			die("��û����Ӧ����Ʒ��");
		}
	}
	
	if($wp2 > 0){
		$_pm['mysql']->query('UPDATE userbag SET sums = sums - 1 WHERE uid = '.$_SESSION['id'].' AND id = '.$wp2.' AND sums > 0');
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			realseLock();
			die("��û����Ӧ����Ʒ��");
		}
	}
	
	$newbb = $_pm['mysql'] -> getOneRecord('SELECT * FROM bb WHERE id = '.$need['next_pet_id']);
	//����ɹ���
	$sus = getSuc($bb['level'],$p1,$p2);
	
	if(is_array($parr1)){
		foreach($parr1 as $v){
			$parr = explode('|',$v);
			$sql = 'UPDATE userbag SET sums = sums - '.$parr[1].' WHERE uid = '.$_SESSION['id'].' AND pid = '.$parr[0].' AND sums >='. $parr[1];
			$b = $_pm['mysql'] -> getOneRecord($sql);
			$result = mysql_affected_rows($_pm['mysql'] -> getConn());
			if($result != 1){
				realseLock();
				die("��Ӧ�ı���Ʒ������");
			}
		}
	}
	$_pm['mysql'] -> query('UPDATE player SET money = money - '.$need_money.' WHERE id = '.$_SESSION['id']);
	$num = rand(1,10000);//echo $num.'<br />';
	//$num = 0;
	if($num <= $sus){//�ɹ�
		$log .= $user['nickname'].' �ɹ�:';
		$nbb = makebb($bb,$newbb,$limit['zs_progress'],$p1,$p2);
		clearBB($bb);
		$task = new task();
		$word = '�����ʥ���� '.$nbb['name'];
		$task->saveGword($word);
		echo '5';
	}else{
		$log .= $user['nickname'].' ʧ��:';
		echo '6';
	}
	//��־����
	if(!is_array($p1)){
		$p1['pid'] = 0;
	}
	if(!is_array($p2)){
		$p2['pid'] = 0;
	}
	$log .= '������Ʒ:'.$p1['pid'].'+'.$p2['pid'].',ԭ���'.print_r($bb,1);
	if(is_array($nbb)){
		$log .= 'ת��·�ߣ�'.$limit['zs_line'].',�³��'.print_r($nbb,1);
	}
	$log .= date('Y-m-d H:i:s');
	$_pm['mysql']->query("INSERT INTO gamelog(ptime,seller,buyer,pnote,vary)
		                      VALUES(unix_timestamp(),'{$_SESSION['id']}','{$_SESSION['id']}','{$log}',104)
							");
	realseLock();
}
function getSuc($level,$wp1,$wp2){
	/*����=ȡ��λС��������ȼ�/30*��1+����Ч������*100
��ֵ=1��10000��ȡ�����������ֵ<=������ʱ��ɹ�*/
	global $_pm;
	$num = 0;
	if(count($wp1) > 0){
		$num += str_replace('sszs:','',$wp1['effect']);
	}
	if(count($wp2) > 0){
		$num += str_replace('sszs:','',$wp2['effect']);
	}
	//echo $level.'<br />'.$num.'=======>';
	$res = round(($level/30*(1+$num)),2) * 100;
	return $res;
}

function makebb($oldbb,$newbb,$zsjd,$wp1,$wp2){
	global $_pm;
	$eff = '';
	$czlnum = 10;
	if(count($wp1) > 0){
		if(strpos($wp1['effect'],'sszs') === false&&strpos($wp1['effect'],'cszsczlbh') === false){
			$eff = $wp1['effect'];
		} 
		if(strpos($wp1['effect'],'cszsczlbh') !== false){
			$czlnum += str_replace('cszsczlbh:','',$wp1['effect']);
		}
	}
	if(count($wp2) > 0){
		if($eff != ''){
			$czlnum += str_replace('cszsczlbh:','',$wp2['effect']);
		}else{
			if(strpos($wp2['effect'],'sszs') === false&&strpos($wp2['effect'],'cszsczlbh') === false){
				$eff = $wp2['effect'];
			}else{
				$czlnum += str_replace('cszsczlbh:','',$wp2['effect']);
			}
		}
	}
	$pac = $pmc = $phit = $pmiss = $pspeed = $php = $pmp = 0;
	if($eff != ''){
		$propseff = explode(':',$eff);
		switch($propseff[0]){
			case "addac": $pac = $propseff[1];break;
			case "addmc": $pmc = $propseff[1];break;
			case "addhit": $phit = $propseff[1];break;
			case "addmiss": $pmiss = $propseff[1];break;
			case "addspeed": $pspeed = $propseff[1];break;
			case "addhp": $php = $propseff[1];break;
			case "addmp": $pmp = $propseff[1];break;
		}
	}
	
	//ת�����Լ��㹫ʽ��(��ʼ����*ת���׶�+��ǰ����*�ȼ�/6000+��ǰ����*�ɳ�/9000)*���ٷְ�+���߰ٷֱ�)
	$hp = round(($newbb['hp']*$zsjd+$oldbb['hp'] * $oldbb['level']/6000 + $oldbb['hp'] * $oldbb['czl']/9000) * ($php+1));
	$mp = round(($newbb['mp']*$zsjd+$oldbb['mp'] * $oldbb['level']/6000 + $oldbb['mp'] * $oldbb['czl']/9000) * ($pmp+1));
	$mc = round(($newbb['mc']*$zsjd+$oldbb['mc'] * $oldbb['level']/6000 + $oldbb['mc'] * $oldbb['czl']/9000) * ($pmc+1));
	$ac = round(($newbb['ac']*$zsjd+$oldbb['ac'] * $oldbb['level']/6000 + $oldbb['ac'] * $oldbb['czl']/9000) * ($pac+1));
	$hits = round(($newbb['hits']*$zsjd+$oldbb['hits'] * $oldbb['level']/6000 + $oldbb['hits'] * $oldbb['czl']/9000) * ($phit+1));
	$miss = round(($newbb['miss']*$zsjd+$oldbb['miss'] * $oldbb['level']/6000 + $oldbb['miss'] * $oldbb['czl']/9000) * ($pmiss+1));
	$speed = round(($newbb['speed']*$zsjd+$oldbb['speed'] * $oldbb['level']/6000 + $oldbb['speed'] * $oldbb['czl']/9000) * ($pspeed+1));
	$czl = round($oldbb['czl']*$czlnum*0.01,1);
	//echo '$ac = round(('.$newbb['ac'].'*'.$zsjd.'+'.$oldbb['ac'] .'*'. $oldbb['level'].'/6000 + '.$oldbb['ac'].' * '.$oldbb['czl'].'/9000) * ('.$pac.'+1))';exit;
	/*echo "INSERT INTO userbb(
								   name,
								   uid,
								   username,
								   level,
								   wx,
								   ac,
								   mc,
								   srchp,
								   hp,
								   srcmp,
								   mp,
								   skillist,
								   stime,
								   nowexp,
								   lexp,
								   imgstand,
								   imgack,
								   imgdie,
								   hits,
								   miss,
								   speed,
								   kx,
								   remakelevel,
								   remakeid,
								   remakepid,
								   muchang,
								   czl,
								   headimg,
								   cardimg,
								   effectimg
								  )
				VALUES(
					   '{$newbb['name']}',
					   '{$_SESSION['id']}',
					   '{$_SESSION['nickname']}',
					   '1',
					   '{$newbb['wx']}',
					   '{$ac}',
					   '{$mc}',
					   '{$hp}',
					   '{$hp}',
					   '{$mp}',
					   '{$mp}',
					   '{$newbb['skillist']}',
					   unix_timestamp(),
					   '{$newbb['nowexp']}',
					   '100',
					   '{$newbb['imgstand']}',
					   '{$newbb['imgack']}',
					   '{$newbb['imgdie']}',
					   '{$hits}',
					   '{$miss}',
					   '{$speed}',
					   '{$newbb['kx']}',
					   '{$newbb['remakelevel']}',
					   '{$newbb['remakeid']}',
					   '{$newbb['remakepid']}',
					   '0',
					   '{$czl}',
					   't{$newbb['id']}.gif',
					   'k{$newbb['id']}.gif',
					   'q{$newbb['id']}.gif'
					   )
			  ";exit;*/
	$_pm['mysql']->query("INSERT INTO userbb(
								   name,
								   uid,
								   username,
								   level,
								   wx,
								   ac,
								   mc,
								   srchp,
								   hp,
								   srcmp,
								   mp,
								   skillist,
								   stime,
								   nowexp,
								   lexp,
								   imgstand,
								   imgack,
								   imgdie,
								   hits,
								   miss,
								   speed,
								   kx,
								   remakelevel,
								   remakeid,
								   remakepid,
								   muchang,
								   czl,
								   headimg,
								   cardimg,
								   effectimg
								  )
				VALUES(
					   '{$newbb['name']}',
					   '{$_SESSION['id']}',
					   '{$_SESSION['nickname']}',
					   '1',
					   '{$newbb['wx']}',
					   '{$ac}',
					   '{$mc}',
					   '{$hp}',
					   '{$hp}',
					   '{$mp}',
					   '{$mp}',
					   '{$newbb['skillist']}',
					   unix_timestamp(),
					   '{$newbb['nowexp']}',
					   '100',
					   '{$newbb['imgstand']}',
					   '{$newbb['imgack']}',
					   '{$newbb['imgdie']}',
					   '{$hits}',
					   '{$miss}',
					   '{$speed}',
					   '{$newbb['kx']}',
					   '{$newbb['remakelevel']}',
					   '{$newbb['remakeid']}',
					   '{$newbb['remakepid']}',
					   '0',
					   '{$czl}',
					   't{$newbb['id']}.gif',
					   'k{$newbb['id']}.gif',
					   'q{$newbb['id']}.gif'
					   )
			  ");
	$jnall = split(",", $newbb['skillist']);
	foreach($jnall as $a => $b)
	{
		$arr = split(":", $b);
		
		$memskillsysid = unserialize($_pm['mem']->get('db_skillsysid'));
		$jn = $memskillsysid[$arr[0]];
		// #################################################				
		if ($jn['ackvalue']=='') continue; // ���Ӹ������ܡ�
		//##################################################
		
		$ack  = split(",", $jn['ackvalue']);
		$plus = split(",", $jn['plus']);
		$uhp  = split(",", $jn['uhp']);
		$ump  = split(",", $jn['ump']);
		$img  = split(",", $jn['imgeft']);

		// Insert userbb jn.	
		/*��ȡ�ղ������ID��*/
		$newbb1 = $_pm['mysql']->getOneRecord("SELECT * 
									  FROM userbb
									 WHERE uid={$_SESSION['id']}
									 ORDER BY stime DESC
									 LIMIT 0,1			                                         
								  ");
		$bbid = $newbb1['id'];

		$_pm['mysql']->query("INSERT INTO skill(bid,name,level,vary,wx,value,plus,img,uhp,ump,sid)
					VALUES(
						   '{$bbid}',
						   '{$jn['name']}',
						   '{$arr['1']}',
						   '{$jn['vary']}',
						   '{$jn['wx']}',
						   '{$ack[0]}',
						   '{$plus[0]}',
						   '{$img[0]}',
						   '{$uhp[0]}',
						   '{$ump[0]}',
						   '{$jn['id']}'
						  )
				  ");
		$sql = "UPDATE player
				SET mbid = {$bbid}
				WHERE id = {$_SESSION['id']}";
		$_pm['mysql'] -> query($sql);
	}
	return $newbb1;
}

function clearBB($bb)
{//return;
	global $_pm;
	$id = $bb['id'];
	
	
	
	// del sk. 
	$_pm['mysql']->query("DELETE FROM skill
				 WHERE bid={$id}
			  ");
			  
	// del zb.
	$_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and zbpets={$id}
			  ");
	// del bb.
	$_pm['mysql']->query("DELETE FROM userbb
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
			  
}
$_pm['mem']->memClose();
realseLock();
?>