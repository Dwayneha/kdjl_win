<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: ��ѯ������ȼ���ʾ������ص�������Ϣ��
*@Note: none
*/
header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');

secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$task = unserialize($_pm['mem']->get(MEM_TASK_KEY));
$taskitem = $task[$user['task']];
/*$taskitem	= $_pm['mem']->dataGet(array('k'	=>	MEM_TASK_KEY,
										 'v'	=>	"if(\$rs['id']== '{$user['task']}') \$ret=\$rs;"
									));*/
									
$props = unserialize($_pm['mem']->get('db_propsid'));

$_gpc = unserialize($_pm['mem']->get(MEM_GPC_KEY));

if(!is_array($taskitem))
{
	echo "��û�н����κ�����";
}
else
{
	$needarr = neednpc($taskitem['okneed']);
	$fromnpc = explode("|",$taskitem['fromnpc']);
	$str .= '�������NPC��<u>'. $_task['oknpc'][$fromnpc[0]].'</u><br/>';
	$str .= '��ɽ���NPC��<u>'. $_task['oknpc'][$taskitem['oknpc']].'</u><br/>';
	if(is_array($needarr))
	{
		foreach($needarr as $k => $v)
		{
			switch($k)
			{
				case "item":
					foreach($v as $item)
					{
						foreach($item as $ik => $iv)
						{
							$par = $props[$iv[0]];
							$strs .= "�ռ�".$par['name']."&nbsp;".$ik."��<br />";
						}
					}
					break;
				case "money":
					$strs .= "��Ҫ��ң�".$v[0]."��<br />";
					break;
				case "jifen":
					$strs .= "��Ҫ���֣�".$v[0]."��<br />";
					break;
				case "ww":
					$strs .= "��Ҫ������".$v[0]."��<br />";
					break;
				case "lv":
					$lvarr = explode("|",$v[0]);
					if($v[1] == 0)
					{
						$strs .= "��Ҫ�ȼ���".$lvarr[0]."������<br />";
					}
					else
					{
						$strs .= "��Ҫ�ȼ���".$lvarr[0]."-".$lvarr[1]."��<br />";
					}
					break;
				case "killmon":
					foreach($v as $kss => $kill)
					{
						$str1 = "";
						foreach($kill as $vss)
						{
							foreach($_gpc as $g)
							{
								if($vss == $g['id'])
								{
									if(strpos($str1,$g['name']) === false)
									{
										if(!empty($str1))
										{
											$str1 .= "��".$g['name'];
										}
										else
										{
											$str1 = $g['name'];
										}
									}
								}
							}
						}
						$gpcnum = explode(",",$kss);
						$strs .= "ɱ������:".$str1."&nbsp;".$gpcnum[0]."��<br />";
					}
					break;
			}
		}
		$str .= "����Ŀ�꣺<br />".$strs."<br /><hr><br />";
	}
	if(!empty($user['tasklog']))
	{
		$arr = neednpc($user['tasklog']);
		foreach($arr as $k => $v)
		{
			switch($k)
			{
				case "item":
					foreach($v as $item)
					{
						foreach($item as $ik => $iv)
						{
							$pa = $props[$iv[0]];
							$log .= "�ռ�".$pa['name']."&nbsp;".$ik."��<br />";
							/*foreach($props as $p)
							{
								if($iv[0] == $p['id'])
								{
									$log .= "�ռ�".$p['name']."&nbsp;".$ik."��<br />";
								}
							}*/
						}
					}
					break;
				case "money":
					$log .= "��Ҫ��ң�".$v[0]."��<br />";
					break;
				case "ww":
					$log .= "��Ҫ������".$v[0]."��<br />";
					break;
				case "lv":
					$lvarr = explode("|",$v[0]);
					if($v[1] == 0)
					{
						$log .= "��Ҫ�ȼ���".$lvarr[0]."������<br />";
					}
					else
					{
						$log .= "��Ҫ�ȼ���".$lvarr[0]."-".$lvarr[1]."��<br />";
					}
					break;
				case "killmon":
					foreach($v as $kss => $kill)
					{
						$str1 = "";
						foreach($kill as $vss)
						{
							foreach($_gpc as $g)
							{
								if($vss == $g['id'])
								{
									if(strpos($str1,$g['name']) === false)
									{
										if(!empty($str1))
										{
											$str1 .= "��".$g['name'];
										}
										else
										{
											$str1 = $g['name'];
										}
									}
								}
							}
						}
						$gpcnum = explode(",",$kss);
						$log .= "ɱ������:".$str1."&nbsp;".$gpcnum[0]."��<br />";
					}
					break;
			}
		}
		$str .= "��ǰɱ�ֽ��ȣ�<br />".$log;
	}
	else{
		$str .= "��ǰɱ�ֽ���Ϊ0";
	}	
}
if (!empty($str)) {
	echo $str;
}
/*$taskresult = '����'.$taskitem['title'].'<br/><hr style="height:1px;border:1px solid green">';

if (is_array($taskitem) && $taskitem['okneed'] != '')
{
	$taskresult .= '�������NPC��<u>'. $_task['npc'][$taskitem['fromnpc']].'</u><br/>';

	$arr = explode(',', $taskitem['okneed']);
	foreach($arr as $k => $v)
	{
		$tarr = explode(':', $v);
		if ($tarr[0] == "see")
		{
			$taskresult .= "��Ҫ�ݷã�<u>" . $_task['npc'][$tarr[1]].'</u>';
		}
		else if($tarr[0] == "killmon")
		{
			$t1 = explode('|', $tarr[1]);
			$grs = $_pm['mem']->dataGet(array('k'	=>	MEM_GPC_KEY,
												'v'	=>	"if(\$rs['id']== '{$t1[0]}') \$ret=\$rs;"
										));
			$taskresult .= " <br/>��Ҫ��ܣ� <u>".$grs['name']." ".$tarr[2]."</u> ��";
			unset($t1);
		}
	    else if($tarr[0] == "giveitem")	// 1=>id, 2=>num
		{
			$idlist = str_replace('|',',',$tarr[1]);
			$all = $_pm['mysql']->getRecords("SELECT name 
												FROM props
											   WHERE id in({$idlist})
											");
			$wplist = '';
			foreach($all as $key => $value)
			{
				$wplist = $wplist?', '.$value['name']:$value['name'];
			}

			$taskresult .= " <br/><u>��Ҫ�ռ���Ʒ�� {$wplist} ���κ� ".$tarr[2]."</u> ��";
		}
	}
	// end npcl
	$taskresult .= '<br/>�������NPC��<u>'.$_task['npc'][$taskitem['oknpc']].'</u>';
	//had part.
	if ($user['tasklog'] == '') $taskresult .= '<br/><hr style="height:1px;border:1px solid green">��������δ��ɡ�';
	else
	{
		$hdtask = explode(',',$user['tasklog']);
		$taskresult .= '<br/><hr style="height:1px;border:1px solid green">��������';
		foreach ($hdtask as $k => $v)
		{
			if ($v == '') continue;
			else
			{
				$arr = explode(':', $v);
				if ($arr[0] == "see") 
					$taskresult .= "�ݷ� <u>" . $_task['npc'][$arr[1]] . '</u> ���';
				else if ($arr[0] == "killmon")
				{
					$t1 = explode('|', $arr[1]);
					$grs = $_pm['mem']->dataGet(array('k'	=>	MEM_GPC_KEY,
												'v'	=>	"if(\$rs['id']== '{$t1[0]}') \$ret=\$rs;"
										));
					$taskresult .= "<br/>�Ѵ�� <u>" . $grs['name'] . " " . $arr[2] . '</u> ��';
				}
				else if ($arr[0] == "giveitem")
				{
					$taskresult .= "<br/>���ռ� <u>" . $arr[2] . '</u> ��';
				}
			}
		}
		
	}
	unset($grs);

	echo $taskresult;
}
else echo "��û��������Ϣ!";*/

$_pm['mem']->memClose();
?>