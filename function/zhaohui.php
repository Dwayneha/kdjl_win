<?php
session_start();
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
require_once('../sec/sec_common_fnc.php');
extract($_REQUEST);//$nums����
secStart($_pm['mem']);
if(zhaohui())
{
	global $_pm,$user;
	$user = $_pm['user']->getUserById($_SESSION['id']);

	$memtimeconfig = unserialize($_pm['mem']->get('db_timeconfignew'));
	$arr = $memtimeconfig['recallPlayer'];	
	$sql_log = "select taskid from tasklog where uid={$user['id']} and taskid>9999";
	$arr_log = $_pm['mysql'] -> getRecords($sql_log);	//34,35
	if(is_array($arr_log))
	{
		foreach($arr_log as $key => $value)
		{
			$shu = 999900;
			$flag[] = $value['taskid']-$shu;//34 35 36 37 0 =>
		}
	}
	else
	{
		$num = count($arr);
		for($i=0;$i<$num;$i++)
		{
			if($arr[$i]['Id']==$nums)
			{
				$one = explode(',', $arr[$i]['days']);//array(0->111:2,1->112:4)
				$id = $i;
			}
		}
		$idlist = '';
		foreach($one as $a => $b)//ȡ�ý�Ʒ
		{
			$bb = explode(':', $b);
			$cishu = $bb[1];
			$idlist .= str_repeat(','.$bb[0],$cishu);
		}
		$mag = "9999".$arr[$id]['Id'];
		$sql_log = "insert into tasklog(uid,taskid) values({$_SESSION['id']},{$mag})"; 
		$_pm['mysql'] -> query($sql_log);
		$sql = "SELECT taskid FROM tasklog WHERE taskid = {$mag} AND uid = {$_SESSION['id']}";
		$checkarr = $_pm['mysql'] -> getRecords($sql);
		if(count($checkarr) != 1){
			die('���Ѿ���ȡ����Ʒ�ˣ�');
		}
		$result = saveGetPropsa(substr($idlist,1));//true or false����Ʒ 
		echo "��ȡ��Ʒ�ɹ���";
	}
	if(is_array($flag)){
		if(!in_array($nums,$flag))
		{
			$num = count($arr);
			for($i=0;$i<$num;$i++)
			{
				if($arr[$i]['Id']==$nums)
				{
					$one = explode(',', $arr[$i]['days']);//array(0->111:2,1->112:4)
					$id = $i;
				}
			}
			$idlist = '';
			foreach($one as $a => $b)//ȡ�ý�Ʒ
			{
				$bb = explode(':', $b);
				$cishu = $bb[1];
				$idlist .= str_repeat(','.$bb[0],$cishu);
			}
			
			$mag = "9999".$arr[$id]['Id'];
			$sql_log = "insert into tasklog(uid,taskid) values({$_SESSION['id']},{$mag})"; 
			$_pm['mysql'] -> query($sql_log);
			$sql = "SELECT taskid FROM tasklog WHERE taskid = {$mag} AND uid = {$_SESSION['id']}";
			$checkarr = $_pm['mysql'] -> getRecords($sql);
			if(count($checkarr) != 1){
				die('���Ѿ���ȡ����Ʒ�ˣ�');
			}
			$result = saveGetPropsa(substr($idlist,1));//true or false����Ʒ 
			echo "��ȡ��Ʒ�ɹ���";
		}
		else
		{
			echo "�Բ������Ѿ���ȡ���˽�Ʒ��";
		}
	}
}
else
{
	echo "�Բ������Ѿ���ȡ���˽�Ʒ�����㻹û�дﵽ��Ӧ�ĵȼ�!";
}
?>