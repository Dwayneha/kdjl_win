<?php
//ini_set('display_errors',true);
//error_reporting(E_ALL);

require_once(dirname(__FILE__).'/config/config.game.php');
if($_GET['f'] == '' && !isset($_GET['dbg'])){
	die('!!');
}
$c = md5($_SERVER['HTTP_HOST'].'vget7lkl@#^%gbhfild');
if($_GET['f'] != $c && !isset($_GET['dbg'])){
	die('!!!!!!!!');
}
if($_GET['op'] == 'num'){
	$sql = 'SELECT count(id) as num FROM ticket_'.date('Ymd');
	$arr = $_pm['mysql'] -> getOneRecord($sql);
	if(mysql_error() || !is_array($arr) || !$arr['num']){
		$num = 0;
	}else{
		$num = $arr['num'];
	}
	echo $num;
}else if($_GET['op'] == 'hit'){

	//�صȽ�:1-5,3-5:1:2;һ�Ƚ�:1-5,3-5:1:2;���Ƚ�:1-5,3-5:1:2;���Ƚ�:1-5,3-5:1:2;���˽�:1-5,3-5:3:1
	$sql = 'SELECT id,uid,ticket_num FROM ticket_'.date('Ymd');
	$ticket = $_pm['mysql'] -> getRecords($sql);
	if(mysql_error()||!is_array($ticket)){
		die('sql�������û������');
	}
	$arr = explode(';',$_GET['str']);
	if(!is_array($arr)){
		die('û������');
	}
	$now = date('Y-m-d');

	
	foreach($arr as $v){
		$narr = explode(':',$v);
		//֧��һ������;
		//���������һ��ֻ�ܵ�һ�
		$str1=$_pm['mem'] -> get('ticket_prize_arr_'.$now);
		if($str1)
			$uidarr = unserialize($str1);
		else
			$uidarr=array();
		
		$tarr=in_arr($uidarr,$ticket);
		$uid = $tarr['uid'];
		$uidarr[]=$tarr['ticket_num'];
		$_pm['mem'] -> set(array('k'=>'ticket_prize_arr_'.$now,'v'=>$uidarr));
		$parr1 = explode(',',$narr[1]);
		$ruser = $_pm['mysql'] -> getOneRecord('SELECT nickname FROM player WHERE id = '.$uid);
		//���н��Ŵ������ݿ��У���ʾ�������û�
		$log = $tarr['ticket_num'];
		write_log(107,$log,$uid);
		foreach($parr1 as $pv){
			$parr = explode('-',$pv);
			if(saveGetPropsMore_S($parr[0],$parr[1],$uid)){
				$log = $ruser['nickname'].'���'.date('Y-m-d').'��'.$narr[0].',�õ�������Ʒ'.$parr[0].'X'.$parr[1].'��';
				write_log(105,$log,$uid);
			}else{//������Ʒʧ��
				$log = $ruser['nickname'].'���'.date('Y-m-d').'��'.$narr[0].',δ�ܵõ�������Ʒ'.$parr[0].'X'.$parr[1].'��';
				write_log(106,$log,$uid);
			}
		}
		echo $tarr['ticket_num'];
	}
}

function write_log($vary,$log,$seller){
	global $_pm;
	$_pm['mysql'] -> query('insert into gamelog set buyer="'.date('Y-m-d').'",vary='.$vary.',seller='.$seller.',ptime='.time().',pnote="'.$log.'"');
}

function in_arr($arr,$newarr){
	if(count($arr)>=count($newarr))
	{
		die('full_set');
	}
	$tarr = $newarr[rand(0,(count($newarr)-1))];
	
	if(empty($arr)){
		return $tarr;
	}else if(in_array($tarr['ticket_num'],$arr)){
		return in_arr($arr,$newarr);
	}else{
		return $tarr;
	}
}

function saveGetPropsMore_S($pid,$num,$uid)
{
	global $_pm;
	if ($pid == '' or $pid == 0) return false;
	global $db;
	$l=0;
	
	$rs = false;
	$rs = $_pm['mysql']->getOneRecord("SELECT * FROM userbag WHERE uid={$uid} and pid={$pid}");
	if (is_array($rs))
	{
		if ($rs['vary'] == 1) // ���۵�����.
		{
			$tt = time();
			$sql = "UPDATE userbag
						   SET sums=sums+$num,
							   stime={$tt}
						 WHERE id={$rs['id']}
					  ";
			$_pm['mysql']->query($sql);
			$str .= $sql;
		}
		else
		{
			$sql = "INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
						VALUES(
							   '{$uid}',
							   '{$pid}',
							   '{$rs['sell']}',
							   '{$rs['vary']}',
							   {$num},
							   unix_timestamp()
							  );
					  ";
			$_pm['mysql']->query($sql);
			$str .= $sql;
	   }	   
	}
	else{
		$rs = $_pm['mysql'] -> getOneRecord("SELECT * FROM props WHERE id = $pid");
		if (is_array($rs))
		{
			$sql = "INSERT INTO userbag(uid,pid,sell,vary,sums,stime)
						VALUES(
							   '{$uid}',
							   '{$pid}',
							   '{$rs['sell']}',
							   '{$rs['vary']}',
							   {$num},
							   unix_timestamp()
							  )
					  ";
			$_pm['mysql']->query($sql);
			$str .= $sql;
		}else{
			return false;
		}
	}		
	unset($rs);
	return true;
}
?>