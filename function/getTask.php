<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.12.24
*@Usage: ������ϵͳ
������Ϊ��
  �Ѿ��еģ��ݷã�NPC���(see:9,)
			ɱ������ɱ�ַ�Χ��������(killmon:44|45:5);

* ���Ӹ��ֽ�����ʽ��
   giveitem:X|Z:Y 
		�ռ�IDΪX��Z�ĵ��߹�Y��,�������ʱ�۳��ռ��ĵ���
   giveitem:X:Y, giveitem:Z:Y
		���壺�ռ�IDΪX��Z�ĵ��߸�Y��,�������ʱ�۳��ռ��ĵ���
   
   killmon:X|Y|Z:M, killmon:E|F|G:M
		���壺����X��Y��Z��E��F��G��Ϊ�����ID����MΪ��Ҫɱ���Ĺ������
   
   �Ӽ�����Ʒ�����ѡ��һ������Ĺ���
	itemrand:X:Y:Z|E:F:G|A:B:C

	props:X:Y|A:B
	��ʾ��ͬʱ���IDΪX�ĵ���Y����IDΪA�ĵ���B����
    
	gonggao:�������+��������(��������)

*@Note: none
*/

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$bag		= $_pm['user']->getUserBagById($_SESSION['id']);
$petsAll	= $_pm['user']->getUserPetById($_SESSION['id']);
$bbs = unserialize($_pm['mem']->get(MEM_BB_KEY));
$memtask = unserialize($_pm['mem']->get(MEM_TASK_KEY));

$n = intval($_REQUEST['n']);
$s = intval($_REQUEST['s']);
$tsk = new task();
$type = $_REQUEST['type'];

if($type == "get")//��������
{
	$taskid = intval($_REQUEST['taskid']);
	if(empty($taskid))
	{
		die("���ݴ���");
	}
	/*$taskinfo = $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
										  'v'	=> "if(\$rs['id']=={$taskid}) \$ret=\$rs;"
										  ));*/
	$taskinfo = $memtask[$taskid];
	if(!is_array($taskinfo)) die("���ݴ���");
	if(empty($taskinfo['fromnpc'])){
		die('���ݴ���');
	}
	//ֻ�����һ��,�ȼ����Ƶ�����
	if(strpos($taskinfo['cid'],'rwl') !== false){
		if($taskinfo['hide'] != 1){
			$a = explode('|',$taskinfo['fromnpc']);
			$rwlarr = $_pm['mysql'] -> getOneRecord("SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} AND xulie = {$taskinfo['xulie']} and fromnpc = {$a[0]}");
			if(!is_array($rwlarr)){
				die("���ݴ���1!");
			}
			$lar = $memtask[$rwlarr['taskid']];
			$rwl = explode('|',str_replace('rwl:','',$lar['cid']));
			if($rwl[1] != $taskid){
				die("���ݴ���2");
			}
		}else{
			$rwlarr = $_pm['mysql'] -> getOneRecord("SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} AND taskid = {$taskid}");
			if(is_array($rwlarr)){
				die('���ܽ��ܴ�����');
			}
		}
	}
	if(!empty($taskinfo['limitlv']))
	{
		$limitarr = explode(",",$taskinfo['limitlv']);
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
							die("���ȵ�����������ս��");
						}
						$lvarr = explode("|",$limitarrs[1]);
						if($lvarr[1] == "0")
						{
							if($blv < $lvarr[0])
							{
								die("���ĵȼ��������ܴ�����");
							}
						}
						else
						{
							if($blv < $lvarr[0] || $blv > $lvarr[1])
							{
								die("���ĵȼ����ڿɽӴ�����Χ֮�ڣ�");
							}
						}
						break;
					case "wx":
						$_mbwx='';		
						foreach($petsAll as $bb)
						{
							if($bb['id'] == $user['mbid'])
							{
								$_mbwx = $bb['wx'];
							}
						}
						if(empty($_mbwx))
						{
							die("���ȵ�����������ս��");
						}
						$wxs=explode('|',$limitarrs[1]);
						if(!in_array($_mbwx,$wxs))
						{						
							die("��ս�������������񲻷�������Ҫ��");
						}
						break;
					case "xfyb"://�ֶθ�ʽxfyb��xxxxxxxx|xxxxxxxx��xxxx|yyyy����ĳ��ĳ��ĳ�յ�ĳ��ĳ��ĳ����Ҫ��������xxxx��yyyy�����Ҳ��ܽ�������
						$sql="select * from yblog where nickname='{$user['name']}'";
						$t=$_pm['mysql'] -> getRecords($sql);
						if(!is_array($t))
						{
							die("��δ����Ԫ�����ѣ��޷���ȡ����!");
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
										die("����Ԫ��������{$xfyb2[0]}���ϲſ���ȡ������");
									}
								}elseif($xfyb2[0]>0 && $xfyb2[1]>=0){
									if($sum_yb<=$xfyb2[0] || $sum_yb>=$xfyb2[1] ){
									/*echo $sum_yb.'<br />';
									print_r($xfyb2);exit;*/
										die("����Ԫ��������������ȡ������ķ�Χ�ڣ�");
									}
								}else{
									die("��ȡ�������");
								}
							}else{
								die("��ȡ�������");
							}
						}
						break;
					
					case "xfsj":// task����limitlv�ֶ������xfsj������ʽxfsj��xxxxxxxx|xxxxxxxx����ĳ��ĳ��ĳ�յ�ĳ��ĳ��ĳ����Ҫ���Ѳ��ܽ�������
						$jc=0;
						$xfsj=explode("|",$limitarrs[1]);
						$sql="select * from yblog where nickname='{$user['name']}'";
						$t=$_pm['mysql'] -> getRecords($sql);
						$check = $_pm['mysql'] -> getOneRecord("select time from tasklog where uid = {$_SESSION['id']} and taskid = 88888 order by id desc limit 1");
						$count = count($t) - 1;
						if(is_array($check) && $t[$count]['id'] <= $check['time']){
							die('���ʱ����û���µ����Ѽ�¼�����ܽ��ܴ�����');
						}
						
						if(!is_array($t))
						{
							die("��δ�������ѣ��޷���ȡ����!");
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
							die("��δ�������ѣ��޷���ȡ����!{$tt} |{$xfsj[0]} | {$xfsj[1]}");
						}
						break;	
						
					case "cishu":
						//cishu:X:Y ���������жϣ������������Y���Ѿ������X�Σ����޷��������
						$time = time() - $limitarrs[2] * 24 * 3600;
						$sql = "SELECT count(*) sl FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$taskid} and time > ".strtotime(date('Ymd',time()));
						$arr = $_pm['mysql'] -> getOneRecord($sql);
						if(is_array($arr))
						{
							if($arr['sl'] >= $limitarrs[1])
							{
								die("��{$limitarrs[2]}������ֻ�����$limitarrs[1]������");
							}
						}
						break;
						
					case "cz"://�ж���ս����ĳɳ�ֵ�Ƿ񹻸���ܴ�����
						$lvarr = explode("|",$limitarrs[1]);
						$sql = "SELECT czl FROM userbb WHERE id=".$user['mbid'];
						$petsmain=$_pm['mysql'] -> getOneRecord($sql);
					
						if($lvarr[1]==0){
							if($lvarr[0]>$petsmain['czl']){
								die("�ó���ɳ�ֵΪ".$petsmain['czl']."���޷���ȡ������!");
							}
						}
						if($lvarr[1]>0){
							if(!($lvarr[0]<=$petsmain['czl'] && $lvarr[1]>=$petsmain['czl'])){
								die("�ó���ɳ�ֵ���ڴ�����Χ�ڣ��޷���ȡ����");
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
								$comselfbid = $pv['id'];
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
								}
							}
						}
						if(!in_array($bname,$bnamearr))
						{
							die("���ĵ�ǰ���費�ܽ��ܴ�����");
						}
						break;
					case "jifen"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['score'] < $limitarrs[1])
						{
							die("���ĵ�ǰ���ֲ����Ӵ�����");
						}
						break;
					case "vip"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['vip'] < $limitarrs[1])
						{
							die("����vip���ֲ����Ӵ�����");
						}
						break;
					case 'merge':
						$merge = $_pm['mysql'] -> getOneRecord("SELECT merge FROM player_ext WHERE uid = {$_SESSION['id']}");
						if($merge['merge'] < 1){
							die('��Ŀǰδ�飬���ܽ��ܴ�����');
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
		die("������ֻ�ܽ���һ��~��");
	}
	//ֻ�����һ�ε��������������
	
	//�޸��û�����;
	/*if(!empty($user['task']))
	{
		if($user['task'] == $taskid)
		{
			die("���Ѿ����ܴ�����");
		}
		
	}*/
	$usertaskarr = $_pm['mysql'] -> getRecords("SELECT taskid FROM task_accept WHERE uid = {$_SESSION['id']}");
	if(is_array($usertaskarr)){
		foreach($usertaskarr as $v){
			$accept[] = $v['taskid'];
		}
		if(in_array($taskid,$accept)){
			die("���Ѿ����ܴ�����");
		}
		if(count($usertaskarr) >= 15){
			die("���Ѿ�������15�����񣬳�����������ƣ�");
		}
	}
	
	$arr = $_pm['mysql'] -> getOneRecord($sql);
	$arr1=explode(',',$arr['okneed']);
	for($i=0;$i<count($arr1);$i++){
		$arr2[$i]=explode(':',$arr1[$i]);
		if($arr2[$i][0]=='zx'){
			$sql = "SELECT onlinetime FROM player_ext WHERE uid = {$_SESSION['id']}";
			$arr0 = $_pm['mysql'] -> getOneRecord($sql);
				if($arr0['onlinetime']<($arr2[$i][1]*3600)){
					die("��������Ҫ����".$arr2[$i][1]."Сʱ�ſɽ��ܣ���Ŀǰ����ʱ�仹�������޷����ܴ�����");
				}
		}
	}
	
	
	//$sql = "UPDATE player SET task = {$taskid},tasklog='' WHERE id = {$_SESSION['id']}";
	$sql = "INSERT INTO task_accept (uid,taskid,time) VALUES ({$_SESSION['id']},$taskid,".time().")";
	$_pm['mysql'] -> query($sql);
	echo "��ϲ�����ɹ����ܴ�����";
	//��¼�����л����������:
	if(strpos($taskinfo['okneed'],",no:1"))
	{
		//$_pm['mysql'] -> query("INSERT INTO tasklog (taskid,uid,xulie,time,fromnpc) VALUES (9999,{$_SESSION['id']},0,0,0)");
		$_pm['mysql'] -> query("UPDATE task_accept SET comself = $comselfbid WHERE uid = {$_SESSION['id']} AND taskid = $taskid");
	}
}

else if($type == 'off')
{
	$taskid = intval($_REQUEST['taskid']);
	if(empty($taskid))
	{
		die("���ݴ���");
	}
	/*if($user['task'] != $taskid)
	{
		die("����ǰ�ӵĲ��Ǵ�����");
	}*/
	$usertaskarr = $_pm['mysql'] -> getRecords("SELECT taskid FROM task_accept WHERE uid = {$_SESSION['id']}");
	if(is_array($usertaskarr)){
		foreach($usertaskarr as $v){
			$accept[] = $v['taskid'];
		}
		if(!in_array($taskid,$accept)){
			die("��û�н��ܴ�����");
		}
	}
	$taskinfo = $memtask[$taskid];
	
	//$sql = "UPDATE player SET task = '',tasklog = '' WHERE id = {$_SESSION['id']}";
	$sql = "DELETE FROM task_accept WHERE uid = {$_SESSION['id']} AND taskid = $taskid";
	$_pm['mysql'] -> query($sql);
	die("�����ɹ���");
}
else if($type == "complate")
{
	require_once('../sec/dblock_fun.php');
	$a = getLock($_SESSION['id']);
	if(!is_array($a)){
		realseLock();
		die('��������æ�����Ժ������');
	}
	$taskid = intval($_REQUEST['taskid']);
	$usertaskarr = $_pm['mysql'] -> getRecords("SELECT taskid,state FROM task_accept WHERE uid = {$_SESSION['id']}");
	if(is_array($usertaskarr)){
		foreach($usertaskarr as $v){
			$accept[] = $v['taskid'];
			if($v['taskid'] == $taskid){
				$user['tasklog'] = $v['state'];
				$user['task'] = $v['taskid'];
				$flag = 1;
			}
		}
		if($flag != 1){
			realseLock();
			die("��û�н��ܴ�����");
		}
	}
	
	
	$taskinfo = $memtask[$taskid];
	//�����ж�								  
	if(!empty($taskinfo['limitlv']))
	{
		$limitarr = explode(",",$taskinfo['limitlv']);

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
							realseLock();
							die("���ȵ�����������ս��");
						}
						$lvarr = explode("|",$limitarrs[1]);
						if(empty($lvarr[1]))
						{
							if($blv < $lvarr[0])
							{
								realseLock();
								die("���ĵȼ�������ɴ�����");
							}
						}
						else
						{
							if($blv < $lvarr[0] || $blv > $lvarr[1])
							{
								realseLock();
								die("���ĵȼ����ڿɽӴ�����Χ֮�ڣ�");
							}
						}
						break;
					case "wx":
						$_mbwx='';		
						foreach($petsAll as $bb)
						{
							if($bb['id'] == $user['mbid'])
							{
								$_mbwx = $bb['wx'];
							}
						}
						if(empty($_mbwx))
						{
							realseLock();
							die("���ȵ�����������ս��");
						}
						$wxs=explode('|',$limitarrs[1]);
						if(!in_array($_mbwx,$wxs))
						{
							realseLock();
							die("��ս�������������񲻷�������Ҫ��");
						}
						break;
					case "cishu":
						//cishu:X:Y ���������жϣ������������Y���Ѿ������X�Σ����޷��������
						$time = time() - $limitarrs[2] * 24 * 3600;
						$sql = "SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$taskid} and tasktime > {$time}";
						$arr = $_pm['mysql'] -> getRecords($sql);
						if(is_array($arr))
						{
							if(count($arr) >= $limitarrs[1])
							{
								realseLock();
								die("��{$limitarrs[2]}������ֻ�����$limitarrs[1]������");
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
								}
							}
						}
						if(!in_array($bname,$bnamearr))
						{
							realseLock();
							die("���ĵ�ǰ���費����ɴ�����");
						}
						break;
					case "jifen"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['score'] < $limitarrs[1])
						{
							realseLock();
							die("���ĵ�ǰ���ֲ����Ӵ�����");
						}
						break;
					case "vip"://jifen:X ֻ���ڻ��ִﵽXʱ�ſ��Խ�������
						if($user['vip'] < $limitarrs[1])
						{
							realseLock();
							die("����vip���ֲ����Ӵ�����");
						}
						break;
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
		realseLock();
		die("������ֻ�ܽ���һ��~��");
	}
	
	if (isset($_REQUEST['n']) && $n>0 && $n<10000)	// �������.
	{
		$ret = $_task['dlg'][$n];	// ��ǰ����NPC���
		$tid = $user['task'];	// ����ǰID��
	
		/*$taskinfo = $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
									  'v'	=> "if(\$rs['id']=={$tid} && {$tid}=={$user['task']}) \$ret=\$rs;"
							));*/
		$taskinfo = $memtask[$tid];
	//echo $taskinfo['oknpc'].'<br />'.$n;exit;
		if (is_array($taskinfo))	//��ȡ��ҵ�ǰ��������ϸ��Ϣ
		{
			if ($taskid != $user['task']) // start task.
			{
				$ret = $tsk->formatTask($taskinfo['frommsg']);
				echo $ret;
			}
			else if ($taskinfo['oknpc'] == $n)
			{
				$ret = $tsk->completeTask($user, $taskinfo);
				echo $ret;
			}
		}
	}
	else if (isset($_REQUEST['s']) && $s>0 && $s<10000)	// �������񡣱���������־��
	{
		if (intval($_REQUEST['taskid'])>0) 
			$user['task']=intval($_REQUEST['taskid']);
		$tsk->startTask($user, $s);
	}
}
realseLock();
$_pm['mem']->memClose();
?>