<?php 
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Usage: ������Ӫ�ӿڡ�
   ��Ҫ�� 1. ����Ƿ��Ѿ�������Ӫ��
   		  2. ��֤˫�������Ƿ�ﵽ�������
          3. ��֤˫�������Ĳ�࣬�Ƿ�����������ѡ�����Ӫ��

*@Write Date: 2008.08.27
*@Usage: Aoyun
*@Note: 
*/
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK');

/*if (!defined('BATTLE_TIME_START'))
	define(BATTLE_TIME_START, "20:00");
if (!defined('BATTLE_TIME_END'))
	define(BATTLE_TIME_END, "22:00");
if (!defined('BATTLE_TIME_WEEK'))
	define(BATTLE_TIME_WEEK, 5);*/

secStart($_pm['mem']);
$_pm['mysql'] -> query(" UPDATE player_ext SET F_Medicine_Buff = '' WHERE uid = '".$_SESSION['id']."'");	
$n = intval($_REQUEST['n']);
if ($n!=1 && $n!=2) die('0');

$today = date("Y-m-d",time());
$user  = $_pm['user']->getUserById($_SESSION['id']);
$battleinfo = $_pm['mysql']->getOneRecord("SELECT maxuser,bf_ml_num,bf_level_limit,level_get,ends
                                             FROM battlefield
											WHERE id={$n}
										 ");
										

// ս������ʱ�俪�ء�
$week = date("N", time());
$hourM= date("H:i", time());

$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));

foreach($battletimearr as $v)
{
	if($v['titles'] == "battle")
	{
		if(empty($days))
		{
			$days = $v['days'];
		}
		else
		{
			$days .= ",".$v['days'];
		}
	}
}

foreach($battletimearr as $bv)
{
	if($bv['titles'] != "battle")
	{
		continue;
	}
	if($week == $bv['days'] && $hourM >= $bv['starttime'] && $hourM <= $bv['endtime'])
	{
		$checkstr = 1;
		break;
	}
	else if ($week != $bv['days'] || ($hourM < $bv['starttime'] || $hourM > $bv['endtime']) )
	{
		$str = 'ս��δ������ս������ʱ�䣺ÿ��'
			 . str_replace(array(1,2,3,4,5,6,7),array('һ','��','��','��','��','��','��'),$days)
			 .' '.$bv['starttime']. '��-'. $bv['endtime'] .'�㿪�ţ�';
	}
	else if ($battleinfo['ends'] == 1)
	{
		$str = 'ս���Ѿ�������ս������ʱ�䣺ÿ��'
			 . str_replace(array(1,2,3,4,5,6,7),array('һ','��','��','��','��','��','��'),$days)
			 .' '.$bv['starttime']. '��-'. $bv['endtime'] .'�㿪�ţ�';
	}
}

if(empty($checkstr))
{
	die($str);
}
// ####### end ##############################

// ��ҵȼ���֤��
$main_bb = $_pm['mysql']->getOneRecord("SELECT czl
										  FROM userbb
										 WHERE id={$user['mbid']}
										");
if ($main_bb['czl'] < $battleinfo['bf_level_limit']) die("������ս����ɳ�������������Ӫ��ս������Ҫ {$battleinfo['bf_level_limit']} �ɳ�!");

// �����ѡ��Ӫ�ĵ�ǰ������
$zrsum = $_pm['mysql']->getOneRecord("SELECT count(id) as cnt
										FROM battlefield_user 
									   WHERE lastvtime>unix_timestamp('{$today}') and pos={$n}
									");
// �����ѡ��Ӫ�ĵ�ǰ������
$dessum = $_pm['mysql']->getOneRecord("SELECT count(id) as cnt
										FROM battlefield_user 
									   WHERE lastvtime>unix_timestamp('{$today}') and pos!={$n}
									");

$currentNum = is_array($zrsum)?$zrsum['cnt']:0;
$desNum = is_array($dessum)?$dessum['cnt']:0;

if (is_array($battleinfo) && $battleinfo['maxuser'] == $currentNum) die('����Ӫ����������');
else  
{ 
    // ��֤˫������������
    if ($currentNum-$desNum >= $battleinfo['bf_ml_num']) die('�ҷ���ǰ���������Է����� '.$bf_ml_num.' �ˣ����㹻����Է��������Ժ�������');
	
	// ������Ҽ�����Ӫ,�����ǰ�Ƿ������Ӫ
	$exists =$_pm['mysql']->getOneRecord("SELECT uid,lastvtime,bid,pos
									        FROM battlefield_user
										   WHERE uid={$_SESSION['id']}
										");

	//ex format: 30-45:10:1|0:1,46-60:20:1|0:1,61-70:30:2|0:1,71-80:40:2|0:1,81-90:50:3|0:1,91-100:60:3|0:1
	$par = explode(',', $battleinfo['level_get']);
	foreach ($par as $k => $v)
	{
		$inparrt = explode(':', $v, 2);
		$inparr  = explode('-', $inparrt[0]);

		//if ($main_bb['level'] >= $inparr[0] && $main_bb['level']<= $inparr[1]) // �ҵ���Ӧ�ȼ���
		if ($main_bb['czl'] >= 10 && $main_bb['czl'] >= $inparr[0] && $main_bb['czl']<= $inparr[1])
		{
			// levels, addjgvalue, ackvalue, failjgvalue, failackvalue, lastvtime
			$att = explode('|', $inparrt[1]); // ��ø���ս������ֵ
			$onepart = explode(':', $att[0]); // �ɹ�����Ӱ��ֵ
			$twopart = explode(':', $att[1]); // ʧ�ܲ���Ӱ��ֵ
			if (is_array($exists)) // ����ҵ�ս����¼������ʱ�䣬��ս����ܽ���ļ��𼰹���ֵ�ȡ�
			{
				// ����Ƿ��Ѿ�����ս��
				if (date("Y-m-d",$exists['lastvtime']) >= $today)
				{
				   $_SESSION['jgbug'] .= __LINE__." B <br>\n";
				    if ($exists['pos']!=$n) die('2'); // ���ܼ���������Ӫ��
					else 
					{
						$_pm['mysql']->query("UPDATE battlefield_user
											 SET addjgvalue={$onepart[0]},
												 ackvalue={$onepart[1]},
												 failjgvalue={$twopart[0]},
												 failackvalue={$twopart[1]},
												 bid={$user['mbid']},
												 levels='{$inparrt[0]}'
										   WHERE uid={$_SESSION['id']}
										 ");

						die('3');  // �Ѿ�������Ӫ�������ټ��롣
					}
				}
				else if ($main_bb['czl'] >= $inparr[0] && $main_bb['czl']<= $inparr[1])
				{
					// ���¼�����Ӫʱ�䡣
					$_pm['mysql']->query("UPDATE battlefield_user
											 SET lastvtime=unix_timestamp(),
												 addjgvalue={$onepart[0]},
												 ackvalue={$onepart[1]},
												 failjgvalue={$twopart[0]},
												 failackvalue={$twopart[1]},
												 doublejg=0,
												 pos={$n},
												 tops=0,
												 jgvalue=curjgvalue+jgvalue,
												 curjgvalue=0,
												 boxnum=0,
												 bid={$user['mbid']},
												 nscf=0,
												 subhp=0,
												 addhp=0,
												 levels='{$inparrt[0]}'
										   WHERE uid={$_SESSION['id']}
										 ");
					$_SESSION['jgbug'] .= __LINE__." A <br>\n";
					die('1');  // ����ɹ���
					break;
				}
			}
			else
			{
				$_SESSION['jgbug'] .= __LINE__." C <br>\n";
				$_pm['mysql']->query("INSERT INTO battlefield_user(uid,pos,bid,jgvalue,levels,addjgvalue,ackvalue,failjgvalue,failackvalue,lastvtime)
									  VALUES({$_SESSION['id']},{$n},{$user['mbid']},0,'{$inparrt[0]}',{$onepart[0]},
											 {$onepart[1]},{$twopart[0]},{$twopart[1]},unix_timestamp()
									        )
									 ");
				die('1');  // ����ɹ���
				break;
			}
		}else continue;
	} // end foreach.
}
?>