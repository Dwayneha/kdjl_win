<?php
ini_set('display_errors',true);
set_time_limit(60);
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK');
$m = $_pm['mem'];
$u = $_pm['user'];
//secStart($m);

$user		= $u->getUserById($_SESSION['id']);
$userBag	= $u->getUserBagById($_SESSION['id']);
$petsAll	= $_pm['user']->getUserPetById($_SESSION['id']);
$bbs = unserialize($_pm['mem']->get(MEM_BB_KEY));
$memtask = unserialize($_pm['mem']->get(MEM_TASK_KEY));

$bid = $_REQUEST['bid'];
$title_vary = $_REQUEST['title_vary'];
$page = $_REQUEST['page'];

$tsk = new task();
foreach($petsAll as $k=>$pv)
{
	if($pv['id'] != $user['mbid'])
	{
		unset($petsAll[$k]);
	}
}
if($title_vary == 1)//��ʾ�����Լ�Ĭ�Ͻ�����������
{
	$title = array();
	$task_accept = $_pm['mysql']->getRecords("select * from task_accept where uid = {$_SESSION['id']} order by id asc");
	$title_arr .= ' ';
	//�������������ʾ
	foreach($_task['varytype'] as $key => $value)
	{
		if($key == 1)
		{
			$title_arr .= '<ul class="lev"><li id="task'.$key.'" class="on" onClick="setTab(\'task\','.$key.',12)"><a style="cursor:pointer"onclick="getTaskDetail(\''.$key.'\');bid='.$key.';void(0);" ><p>'.$value.'</p></a></li></ul>';
			$title_arr .= '<ul id="con_task_'.$key.'" class="con"></ul>';
		}
		else if($key == 2)
		{
			$title_arr .= '<ul class="lev"><li id="task'.$key.'" onClick="setTab(\'task\','.$key.',12)"><a style="cursor:pointer"onclick="getTaskDetail(\''.$key.'\');bid='.$key.';void(0);" ><p onclick="taskASwap(this)">'.$value.'</p></a></li></ul>';
			$title_arr .= '<ul id="con_task_'.$key.'" class="con hiden"></ul>';
		}
		else
		{
			$title_arr .= '<ul class="lev"><li id="task'.$key.'" onClick="setTab(\'task\','.$key.',12)"><a style="cursor:pointer"onclick="getTaskDetail(\''.$key.'\');bid='.$key.';void(0);" ><p onclick="taskASwap(this)">'.$value.'</p></a></li></ul>';
			$title_arr .= '<ul id="con_task_'.$key.'" class="con hiden"></ul>';
		}
	}
	$title_arr .= "@@@@";//����Ϊ���ʾ
	$active_content = "";
	$week = date('N',time());
	$active = $_pm['mysql']->getRecords("select * from system_activity where week = {$week}");
	
	$title_arr .= '<ul>';
	if(is_array($active))
	{
		$j = 1;
		$sum = count($active);
		$kong = 4-$sum;
		foreach($active as $ac_key => $ac_value)
		{
			if(is_array($ac_value))
			{
				$title_arr .= '<li><a style="cursor:pointer"onclik="void(0)"><span onmouseover="javascript:showcontent_ac(\''.$ac_value['title'].'\',\''.$ac_value['time'].'\',\'active'.$j.'\',event);" onmouseout="javascript:closecontent();" id=active_'.$j.'><img src="'.$ac_value['pic'].'"></span></a></li>';
				$j++;
			}
		}
		for($i=0;$i<$kong;$i++)
		{
			$title_arr .= ' <li><a style="cursor:pointer"onclik="void(0)">���޻</a></li>';	
		}
		
	}
	else
	{
		for($i=0;$i<4;$i++)
		{
			$title_arr .= ' <li><a style="cursor:pointer"onclik="void(0)">���޻</a></li>';	
		}
	}
	$title_arr .= ' <li id="date"></li>';
	$title_arr .= '</ul>';

	echo $title_arr;
}
if($title_vary == 2)//��ʾÿһ���������������
{
	//�ұ߸�������С������ʾ
	
	$task_details = $_pm['mysql']->getRecords("select * from task where color = {$bid}");
	
	if(is_array($task_details))
	{
		foreach($task_details as $key => $key_v)//��IDΪKEY ����������
		{
			
			$task_all[$key_v['id']] = $key_v;
		}
	}
	//��ѯ���ѽӵ�����
	$user_task = $_pm['mysql']->getRecords("select * from task_accept where uid = {$_SESSION['id']}");
		
	if(is_array($user_task))
	{
		foreach($user_task as $user_task_key => $user_task_value)
		{
			$user_task_array[] =  $user_task_value['taskid'];
		}
	}
	else
	{
		$user_task_array[] =  0;
	}
	//$title_details .=  '<ul class="list l2">';
	if(is_array($task_details))
	{
		$nowtime = date("YmdHis");
		$timearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));
		foreach($timearr as $tv)
		{
			if($tv['titles'] == "task")
			{
				$taskcheckarr[] = $tv;
			}
		} 
		$taskArr = array();
		$rwlidarr = array();
		foreach($task_details as $v_key => $v)//color ������
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
			//�Ƿ�ɼ������ж�								  
			if(!empty($v['limitlv']))
			{
				$limitarr = explode(",",$v['limitlv']);
				if(is_array($limitarr))
				{
					$flag=false;
					foreach($limitarr as $vl)
					{
						$limitarrs = explode(":",$vl);
						switch($limitarrs[0])
						{
							case "level"://�ȼ�����
								foreach($petsAll as $bb)
								{
									if($bb['id'] == $user['mbid'])
									{
										$blv = $bb['level'];
									}
								}
								if(empty($blv))
								{
									$flag=true;
								}
								$lvarr = explode("|",$limitarrs[1]);
								if(empty($lvarr[1]))
								{
									if($blv < $lvarr[0])
									{
										$flag=true;
									}
								}
								else
								{
									if($blv < $lvarr[0] || $blv > $lvarr[1])
									{
										$flag=true;
									}
								}
								break;
								
							case "czl"://�ɳ�����
								foreach($petsAll as $bb)
								{
									if($bb['id'] == $user['mbid'])
									{
										$bbczl = $bb['czl'];
									}
								}
								if(empty($bbczl))
								{
									$flag=true;
								}
								$lvarr = explode("|",$limitarrs[1]);
								if(empty($lvarr[1]))
								{
									if($bbczl < $lvarr[0])
									{
										$flag=true;
									}
								}
								else
								{
									if($bbczl < $lvarr[0] || $bbczl > $lvarr[1])
									{
										$flag=true;
									}
								}
								break;
						}
					}
					if($flag)
					{
						continue;
					}
				}
			}
			if(empty($v['cid']))
			{
				$sql = "SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = {$v['id']} AND taskid != 88888";
				$checkarr = $_pm['mysql'] -> getOneRecord($sql);
				if(is_array($checkarr))
				{
					continue;
				}
				else
				{
					$title_small = explode('|',$v['fromnpc']);
					$title_small_next[$title_small[1]] = $v;					
				}
			}
			else
			{
				$cidarr = explode(":",$v['cid']);
				if($cidarr[0] == "rwl")
				{
					if(!empty($v['xulie']))
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
				}
				else if ($cidarr[0] == "paihang")
				{
					if($cidarr[1] != $user['paihang'])
					{
						continue;
					}
					else
					{
						$title_small = explode('|',$v['fromnpc']);
						$title_small_next[$title_small[1]] = $v;
					}
				}
				else//self 
				{
					//if($v['hide'] == 1 && $v['id'] != $taskid)
					if($v['hide'] == 1 && $v['id'] != $taskid)
					{
						$title_small = explode('|',$v['fromnpc']);
						$title_small_next[$title_small[1]] = $v;
					}
				}
			}
		}	
	}
	
	if(is_array($rwlidarr))
	{
		foreach($rwlidarr as $i=>$v)//����������
		{
			if(!is_array($rwlidarr[$i])) break;//18 22
			$mixed = array_intersect($user_task_array,$rwlidarr[$i]);//����һ��ɾһ��
			if(empty($mixed))//��ʾ��ǰ�ӵ�������������ˣ�����ʾ��������ʾ  ��1����ǰ����������������ˣ�2;��ǰû�н���������
			{
				$sql = "SELECT * FROM tasklog WHERE uid = {$_SESSION['id']} and xulie = {$i}";//�����ǰ��û�������������
				$result = $_pm['mysql'] -> getOneRecord($sql);
				if(is_array($result))//��ǰ�����������������ʾ��һ���������������� 
				{
					$taskinfo = $task_all[$result['taskid']];
					$a = explode("|",$taskinfo['cid']);
				//	print_r($a)."<br />";
				
					if(empty($a[1]) || !is_numeric($a[1]))
					{
						continue;
					}
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
					//echo $taskinfo['fromnpc'];//2|1
					$title_small = explode('|',$taskinfo['fromnpc']);
					$title_small_next[$title_small[1]] = $task_all[$a[1]];
					//print_r($title_small_next);
				}
				else//û���������������ӵ�һ����ʼ����
				{
					foreach($task_details as $t)//$task_details Ϊ�����
					{
						if($t['xulie'] == $i && $t['hide'] == 1)
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
							$title_small = explode('|',$t['fromnpc']);
							$title_small_next[$title_small[1]] = $t;
						}
					}
				}
	
			}
		}//for ѭ������
	}
	//print_r($title_small_next);
	$array = BubbleSort($title_small_next);//��˳������	
	
	
	if(is_array($array))
	{
		//if(count($array)>50) die('--big array--');
		$title_details='';
		$ct=0;
		
		foreach($array as $keys => $values)//getTasks($taskid,&$user,&$petsAll,&$bbs,&$memtask)
		{
			//if(!in_array($values['id'],array(1132,1133,1136,1137)))continue;
			$ct++;
			//if($ct>13) die('--��ѭ��--:'. memory_get_usage() );
			//if($ct>13) die(__LINE__.' --��ѭ��--:'. memory_get_usage().print_r($values,1));
				if(empty($values)) continue;
				$flagnum = '';
				$in=in_array($values['id'],$user_task_array);
				$gt=getTasks($values,$user,$petsAll,$bbs);
				if($in&& $gt!==false)//������ID�Ƿ����ѽ�����ID ��
				{
					$check = "�ѽ�";//��ʾ������ť
				//	$flagnum = 2;
					$flagnum = 1;
					$npcnum = $values['oknpc'];
					$title_details .= '<li class="t"><a style="cursor:pointer"onclik="void(0)"><p onclick="taskASwap(this);javascript:OpenLogin('.$flagnum.','.$values['id'].','.$npcnum.',3)">'.$values['title'].'</p></a></li>';
				}else if(!$in && $gt!==false){
					$check = "�ɽ�";//��ʾ���ܰ�ť
					$flagnum = 1;
					$npcnum = $bid;
					$title_details .= '<li class="a"><a style="cursor:pointer"onclik="void(0)"><p onclick="taskASwap(this);javascript:OpenLogin('.$flagnum.','.$values['id'].','.$npcnum.',4)">'.$values['title'].'</p></a></li>';
				}else if(!$gt)
				{	
					$flagnum = 1;
					$check = '���ɽ�';//��ʾ�رհ�ť
					$npcnum = $bid;
					//$title_details .= '<li><p class="p1" onclick="javascript:OpenLogin('.$flagnum.','.$values['id'].','.$npcnum.',5)">'.$values['title'].'</p><span>'.$check.'</span></li>';
					$title_details .= '<li class="t"><a style="cursor:pointer"onclik="void(0)"><p onclick="taskASwap(this);javascript:OpenLogin('.$flagnum.','.$values['id'].','.$npcnum.',5)">'.$values['title'].'</p></a></li>';
				}
			//echo strlen($title_details)."<br/>\n";$title_details='';
			//if($ct>13) die(__LINE__.' --��ѭ��--:'. memory_get_usage());
		}
		/*
die('len='.strlen($title_details));
		flush();
		ob_flush();
*/
	}
	else
	{
		$title_details .= '��������';
	}
	$title_details .= '</ul>';
	echo $title_details;
}

if($title_vary == 3)//��ʾ�����Լ�Ĭ�Ͻ�����������
{
	$task_accept = $_pm['mysql']->getRecords("select * from task_accept where uid = {$_SESSION['id']} order by id asc");
	$task_accept_arr .= '';
	if(is_array($task_accept))
	{
		foreach($task_accept as $task_accept_key => $task_accept_value)
		{
			$task_accepttitle = $_pm['mysql']->getOneRecord("select * from task where id = {$task_accept_value['taskid']}");
			$task_accept_array[] =  $task_accepttitle;
			$state[$task_accept_value['taskid']] = $task_accept_value['state'];
		}
		foreach($task_accept_array as $accept_key => $accept_value)//task 
		{
					$taskinfo = $accept_value;
					if($tsk->completeTaskShow($user, $taskinfo))
					{
						$accept = '�ɽ�';//���ν��ܰ�ť
						//$ac = explode('|',$accept_value['fromnpc']);
			 			$task_accept_arr .= '<li class="u"><a style="cursor:pointer"onclik="void(0)"><p onclick="taskASwap(this);javascript:OpenLogin(2,'.$accept_value['id'].','.$accept_value['oknpc'].',1)">'.$accept_value['title'].'</p></a></li>';
			 			
					}
					else
					{
						$accept = '���ɽ�';//ֻ��һ��������ť
						//$ac = explode('|',$accept_value['fromnpc']);
						 $task_accept_arr .= '<li class="c"><a style="cursor:pointer"onclik="void(0)"><p onclick="taskASwap(this);javascript:OpenLogin(2,'.$accept_value['id'].','.$accept_value['oknpc'].',2)">'.$accept_value['title'].'</p></a></li>';
					}
		}
	}
	echo $task_accept_arr;
}










?>
