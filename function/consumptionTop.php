<?php
require_once('../config/config.game.php');
secStart($_pm['mem']);
header('Content-Type:text/html;charset=GBK');
$sql='select sum(yb) fee,nickname from yblog where buytime>'.strtotime(date("Y-m-d ").'00:00:00').' and buytime<'.strtotime(date("Y-m-d ").'20:59:59').' group by nickname order by sum(yb) desc limit 3';
$rows = $_pm->getRecords($sql);
function givePrize($name,$pstr,&$tsk)
{
	global $_pm;
	$user=$_pm['mysql']->getOneRecord('select id from player where name="'.$name.'" limit 1');
	if(!$user)
	{
		echo mysql_error();
		return;
	}
	$prize=explode('|',$pstr);
	foreach($prize as $p)
	{
		$t=explode(':',$p);
		if(!$tsk->saveGetPropsMore($t[0],$t[1],0,$user['id']))
		{
			$log='insert into gamelog set buyer="'.date('Ymd').'",vary=239,seller='.$user['id'].',ptime='.time().',pnote="���Ž���ʧ��,�û�:'.$name.',��Ʒid:'.$t[0].',����:'.$t[1].'"';
		}else{
			$log='insert into gamelog set buyer="'.date('Ymd').'",vary=239,seller='.$user['id'].',ptime='.time().',pnote="���Ž����ɹ�,�û�:'.$name.',��Ʒid:'.$t[0].',����:'.$t[1].'"';
		}
		$_pm['mysql']->query($log);
	}
}
if($rows)
{
	$memtimeconfig = unserialize($_pm['mem']->get('db_timeconfignew'));
	$config=$memtimeconfig['consumptionTop'][0];
	if($config['starttime']==0)
	{
		die("�û�п���");
	}
	if($_GET['act']=='show')
	{
		/*foreach($rows as $row)
		{
			
		}*/
		echo '<pre>
		line='.__LINE__.'
		';
		var_dump($rows);
		echo '</pre>
		';
	}
	else if($_GET['act']=='calc')
	{
		if($config['starttime']>date('H') || $config['endtime']<date('H'))
		{
			die('ֻ�� '.$config['starttime'].' �� '.$config['endtime'].' �����콱��');
		}
		$a = getLock(1);
		$ck=$_pm['mysql']->getOneRecord('select id from gamelog where vary=239 and seller = '.$_SESSION['id'].' AND buyer="'.date('Ymd').'" limit 1');//��鷢��
		if(!$ck)
		{
			$prizes=explode(',',$config['days']);
			$task=new task();
			$top = 100;
			foreach($rows as $rk => $rv){
				if($rv['nickname'] == $_SESSION['name']){//�ж��Ƿ�������ǰ����
					$top = $rk;
					$newrow = $rv;
					break;
				}
			}
			if($top == 100){
				die('���ź�����û�н���ǰ������������鿴�������ơ���');
			}
			foreach($prizes as $k=>$v)
			{
				if($k >= $top){
					$res = explode(';',$v);
					if($res[1] < $newrow['fee']){
						//die('���Ѳ��� '.$res[1].' �����콱��');
						givePrize($newrow['nickname'],$res[0],&$task);
						$flag = 1;
						break;
					}
				}
				givePrize($newrow['nickname'],$res[0],&$task);
			}
		}else {
			die('�Ѿ��콱�ˣ�');
		}
		realseLock();
	}
}else{
	if($_GET['act']=='show')
	{
		echo '��û������,�Ͻ�������ƷӮȡ��!';
	}
}
?>