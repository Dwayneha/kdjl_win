<?php 
/**
@Usage: ��ȡ�������
���齱����exp=����*�����ս����ȼ�*1000
819:28,820:28,821:28,822:56,823:28,824:28,825:28,826:56,827:28,828:28,829:28,830:56,831:28,832:28,833:28,834:56,835:28,836:28,837:28,838:56,839:28,840:28,841:28,842:56,843:28,844:28,845:28,846:56,818:1
*/
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK');
secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
//$bag		= $_pm['user']->getUserBagById($_SESSION['id']);
$action = $_REQUEST['action'];
$props		= unserialize($_pm['mem']->get(MEM_PROPS_KEY));
$day = intval(date("Ymd"));
$tl = intval(date("H",time()));
$timearr1 = unserialize($_pm['mem']->get(MEM_TIMENEW_KEY));
$timearr = $timearr1['dati'];
foreach($timearr as $k => $v)
{
	$dayarr = explode("-",$v['days']);
	if($tl >= $v['starttime'] && $tl < $v['endtime'])
	{
		$checktime = 1;
		break;
	}
}

$prize = $timearr1['datiprops'];
foreach($prize as $v)
{
	$prizearr[$v['starttime']] = $v;
}

if($day < $dayarr[0] || $day > $dayarr[1])
{
	die(100);
}

// time limit end.
if($checktime != 1)
{
	die('�����콱ʱ���ڣ�');
}

if($action == "answer")
{
	die("1");
}
else{
	$rs = $_pm['mysql']->getOneRecord("SELECT * 
										 FROM aoyun_player
										WHERE uid={$_SESSION['id']}
									 ");
	if($rs['oksum'] == 0 || $rs['qsums'] < 30)
	{
		die("10");
	}
	$bb = $_pm['mysql']->getOneRecord("SELECT level,id
												 FROM userbb 
												WHERE uid={$_SESSION['id']} and id={$user['mbid']}");
	if (!is_array($bb)) die('�������ȵ�����������ս��������ܻ�ý���������!');
	if (is_array($rs))
	{
		if ($rs['times'] > 0 && $rs['result']==1 && $rs['qsums']>=30)
		{
			if($rs['oksum'] <= 5 && $rs['oksum'] > 0)
			{
				$exp = $rs['oksum']*$prizearr['0-5']['endtime']*$bb['level'];
				$arr = explode("|",$prizearr['0-5']['days']);
				foreach($arr as $v)
				{
					$newarr = explode(":",$v);
					$randnum = rand(1,$newarr[1]);
					if($randnum == 1)
					{
						$task = new task();
						$task->saveGetPropsMore($newarr[0],$newarr[2]);
						foreach($props as $p)
						{
							if($p['id'] == $newarr[0])
							{
								$str .= $p['name']."&nbsp;".$newarr[2]."��,";
							}
						}
					}
				}
			}
			else if($rs['oksum'] <= 13 && $rs['oksum'] > 5)
			{
				$exp = $rs['oksum']*$prizearr['6-13']['endtime']*$bb['level'];
				$arr = explode("|",$prizearr['6-13']['days']);
				foreach($arr as $v)
				{
					$newarr = explode(":",$v);
					$randnum = rand(1,$newarr[1]);
					if($randnum == 1)
					{
						$task = new task();
						$task->saveGetPropsMore($newarr[0],$newarr[2]);
						foreach($props as $p)
						{
							if($p['id'] == $newarr[0])
							{
								$str .= $p['name']."&nbsp;".$newarr[2]."��,";
							}
						}
					}
				}
			}
			else if($rs['oksum'] <= 22 && $rs['oksum'] > 13)
			{
				$exp = $rs['oksum']*$prizearr['14-22']['endtime']*$bb['level'];
				$arr = explode("|",$prizearr['14-22']['days']);
				foreach($arr as $v)
				{
					$newarr = explode(":",$v);
					$randnum = rand(1,$newarr[1]);
					if($randnum == 1)
					{
						$task = new task();
						$task->saveGetPropsMore($newarr[0],$newarr[2]);
						foreach($props as $p)
						{
							if($p['id'] == $newarr[0])
							{
								$str .= $p['name']."&nbsp;".$newarr[2]."��,";
							}
						}
					}
				}
			}
			else if($rs['oksum'] <= 29 && $rs['oksum'] > 22)
			{
				$exp = $rs['oksum']*$prizearr['23-29']['endtime']*$bb['level'];
				$arr = explode("|",$prizearr['14-22']['days']);
				foreach($arr as $v)
				{
					$newarr = explode(":",$v);
					$randnum = rand(1,$newarr[1]);
					if($randnum == 1)
					{
						$task = new task();
						$task->saveGetPropsMore($newarr[0],$newarr[2]);
						foreach($props as $p)
						{
							if($p['id'] == $newarr[0])
							{
								$str .= $p['name']."&nbsp;".$newarr[2]."��,";
							}
						}
					}
				}
			}
			else if($rs['oksum'] >= 30)
			{
				$exp = $rs['oksum']*$prizearr['30']['endtime']*$bb['level'];
				$arr = explode("|",$prizearr['30']['days']);
				foreach($arr as $v)
				{
					$newarr = explode(":",$v);
					$randnum = rand(1,$newarr[1]);
					if($randnum == 1)
					{
						$task = new task();
						$task->saveGetPropsMore($newarr[0],$newarr[2]);
						foreach($props as $p)
						{
							if($p['id'] == $newarr[0])
							{
								$str .= $p['name']."&nbsp;".$newarr[2]."��,";
							}
						}
					}
				}
			}
			
			$task = new task();
			$task->saveExps($exp,$bb['id']);
			$str1 = substr($str,0,-1);
			$task->saveGword("ͨ���ʹ���<֪ʶ�ʴ�>����˴������鼰&nbsp;".$str1."");
			
	
			/*$_pm['mysql']->query("UPDATE userbb 
									 SET nowexp=nowexp+{$exp}
								   WHERE id={$bb['id']}
								");*/
	
			//times limit.
			if ($timecheck == 1) $tm = 1;
			else $tm=2;
	
			$_pm['mysql']->query("UPDATE aoyun_player
									 SET result=0,
										 times={$tm}
									WHERE uid={$_SESSION['id']}
								");
			// Rand get props.
			
			$newstr = "��ϲ�����".$str.$exp."����";
			die($newstr);
		}
		else
		{
			die('���Ѿ���ȡ����ʱ����Ѿ����ڣ���ο�����˵����');
		}
	}
}

$_pm['mem']->memClose();
//####################

function randProps()
{
	$plist ='819:28,820:28,821:28,822:56,823:28,824:28,825:28,826:56,827:28,828:28,829:28,830:56,831:28,832:28,833:28,834:56,835:28,836:28,837:28,838:56,839:28,840:28,841:28,842:56,843:28,844:28,845:28,846:56,818:1';
	$arr = explode(',', $plist);
	foreach ($arr as $k => $v)
	{
		$dl = explode(':', $v);
		if (rand(1, $dl[1]) ==1) // hits!!!
		{
			$task = new task();
			$task->saveGetProps($dl[0]);
			break;
		}
	}
}
?>