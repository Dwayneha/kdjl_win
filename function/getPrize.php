<?php
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK');
secStart($_pm['mem']);
require_once('../sec/dblock_fun.php');
$a = getLock($_SESSION['id']);
if(!is_array($a)){
	msg('�벻Ҫ������,лл��');
}
$welcome = memContent2Arr("db_welcome",'code');
$uarr = array();
$now = date('Ymd');
$user = $_pm['user']->getUserById($_SESSION['id']);
$mempropsid = unserialize($_pm['mem']->get('db_propsid'));
$u = $_pm['mysql'] -> getOneRecord('SELECT prize_every_day FROM player_ext WHERE uid = '.$_SESSION['id']);
$uarr = explode('|',$u['prize_every_day']);
$prize_str = $welcome['holiday_prize']['contents'];
$arr = explode('|',$prize_str);
if($_GET['type'] == 1){
	if($arr[0] == 0){//�ճ�����
		msg('��δ����');
	}else{
		if($uarr[0] < $now){
			$row = explode(',',$arr[0]);
			$task = new task();
			foreach($row as $rv){
				$res = explode(':',$rv);
				$task->saveGetPropsMore($res[0],$res[1]);
				$s.=','.$mempropsid[$res[0]]['name'].'x'.$res[1];
			}
			$s = substr($s,1);
			
			$newstr = $now.'|'.$uarr[1].'|'.$uarr[2];
			$_pm['mysql']->query("UPDATE player_ext SET prize_every_day = '$newstr' WHERE uid = ".$_SESSION['id']);
			msg('ÿ�ս�����ȡ�ɹ������'.$s);
		}else{
			msg('�Ѿ���ȡ');
		}
	}
}else if($_GET['type'] == 2){
	if($arr[1] == 0){//��ĩ����
		msg('��δ����');
	}else{
		$week = date('w');
		if($week != 0 && $week != 6){
			msg('������ĩ');
		}else{
			if($week == 0){//������
				$yes = date("Ymd", strtotime("1 days ago"));//��Ҫ�ж�����Ҳû����ȡ
				if($uarr[1] < $yes){
					$weekprizeflag = 1;//��δ��ȡ
				}else{
					msg('�Ѿ���ȡ');
				}
			}else{
				if($uarr[1] < $now){
					$weekprizeflag = 1;//��δ��ȡ
				}else{
					msg('�Ѿ���ȡ');
				}
			}
		}
	}
	if($weekprizeflag == 1){
		$row = explode(',',$arr[1]);
		$task = new task();
		foreach($row as $rv){
			$res = explode(':',$rv);
			$task->saveGetPropsMore($res[0],$res[1]);
			$s.=','.$mempropsid[$res[0]]['name'].'x'.$res[1];
		}
		$s = substr($s,1);
		
		$newstr = $uarr[0].'|'.$now.'|'.$uarr[2];
		$_pm['mysql']->query("UPDATE player_ext SET prize_every_day = '$newstr' WHERE uid = ".$_SESSION['id']);
		msg('��ĩ������ȡ�ɹ������'.$s);
	}
}else if($_GET['type'] == 3){
	$harr = explode(';',$arr[2]);//20100917:1*20,2*30;20101001:5*20,6*30
	if(is_array($harr)){
		foreach($harr as $hv){
			$row = explode(':',$hv);
			if($now == $row[0]){//�ǽڼ���
				$flag = 1;
				if($uarr[2] == $row[0]){
					msg('�Ѿ���ȡ');
				}else{
					$holidayprizeflag = 1;//��δ��ȡ
				}
				break;
			}
		}
	}else{
		msg('û�����ýڼ��գ�');
	}
	if($flag != 1){
		msg('���ǽڼ��գ������콱��');
	}
	if($holidayprizeflag == 1){//����
		//�õ����õĽ�����Ʒ
		$rs = explode(',',$row[1]);
		$task=new task();
		foreach($rs as $rv){
			$res = explode('*',$rv);
			$task->saveGetPropsMore($res[0],$res[1]);
			$s.=','.$mempropsid[$res[0]]['name'].'x'.$res[1];
		}
		$holidayprizestr = substr($holidayprizestr,6);
		$newstr = $uarr[0].'|'.$uarr[1].'|'.$now;
		$_pm['mysql']->query("UPDATE player_ext SET prize_every_day = '$newstr' WHERE uid = ".$_SESSION['id']);
		$s=substr($s,1);
		msg('�ڼ��ս�����ȡ�ɹ������'.$s);
	}
}


function msg($m)
{
	realseLock();
	die($m);
}
?>