<?php
/**
*@Usage: ս�����
*@Author: GeFei Su.
*@Write Date:2008-08-27
*@Copyright:www.webgame.com.cn
*/
require_once('../config/config.game.php');
secStart($_pm['mem']);
$today = date("Y-m-d", time());

// ս������ʱ�俪�ء�
$week =	date("N", time());
$hourM=	date("H:i", time());

$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));

foreach($battletimearr as $bv)
{
	if($bv['titles'] != "battle")
	{
		continue;
	}
	if(($week == $bv['days'] && $hourM >= $bv['endtime']) || battle_end() === true) // ս��ʱ�����������ս���رձ�ǡ���ʼ����������������ݣ����������ȡ������
	{
		// ���ݵ�ǰʣ���HP�����ж�˭�ɹ���ʧ�ܡ�
		$checkstr = 1;
		$zyrs = $_pm['mysql']->getRecords("SELECT hp,id,posname
											 FROM battlefield
											WHERE countf=0
											ORDER BY hp DESC
										 ");
		if (is_array($zyrs)) // ��һ��ͳ��
		{
			$_pm['mysql']->query("UPDATE battlefield
									 SET success=1
								   WHERE id='{$zyrs[0]['id']}'
							   ");
			$exists = $_pm['mysql']->getOneRecord("SELECT id,countf,success,posname
													 FROM battlefield
													WHERE countf=0 and success=1
												  ");
			if (is_array($exists) && $exists['countf']==0) // �رո��±�ǣ�����ʼ�����������
			{
				$_pm['mysql']->query("UPDATE battlefield
										 SET countf=1,startf=0,ends=1
									");
				
				
				//�����ظ�����
				/*$check = unserialize($_pm['mem'] -> get('battle_prize_check'));
				$timenow = time() - 300;
				if(!empty($check) && $check <= $timenow) return;*/
				$_pm['mem'] -> set(array('k'=>'battle_prize_check','v'=>time()));
				 
				 
				if($str != $hi) return;
				
				// ս��ʤ������
				$pub = new task();
				if ($exists['posname'] == $zyrs[0]['posname'])
					$fail =  $zyrs[1]['posname'];
				else $fail=  $zyrs[0]['posname'];
				$word = '[ϵͳ����] ����ս��������'.$fail.'����������ɾ���'.$exists['posname'].'ȡ����ʤ����';
				for($i=0;$i<5;$i++)
					$pub-> saveGword($word, 1);
	
				// ��ȡʤ����������ҵ������Ϣ�����б���ս���������¡�
				$all = $_pm['mysql']->getRecords("SELECT id 
													FROM battlefield_user
												   WHERE lastvtime>unix_timestamp({$today}) and curjgvalue>0 and pos={$exists['id']}
												   ORDER BY curjgvalue DESC
												   LIMIT 0,10
												");
			   if (is_array($all))
			   {
				   foreach ($all as $k => $rs)
				   {
					   $boxnum = 0;
					   $jgvl   = 0;
					   switch(($k+1))
					   {
						  case 1: $boxnum=10; $jgvl = 2000; break;
						  case 2: 
						  case 3: $boxnum=6; $jgvl = 1500;break;
						  case 4:
						  case 5:
						  case 6: $boxnum=4; $jgvl = 1000;break;
						  case 7:
						  case 8:
						  case 9:
						  case 10: $boxnum=2; $jgvl = 500;break;
						  default: $boxnum=$jgvl=0;
					   }
					  // ������ҵ�����.
					  $_pm['mysql']->query("UPDATE battlefield_user 
											   SET tops=".($k+1).", boxnum={$boxnum}, curjgvalue=curjgvalue+{$jgvl}
											 WHERE id={$rs['id']}
										   ");
				   }
			   }
			   // ʧ�ܷ�����ͳ�ƿ�ʼ
			   // ��ȡʧ�ܷ�������ҵ������Ϣ�����б���ս���������¡�
				$all = $_pm['mysql']->getRecords("SELECT id 
													FROM battlefield_user
												   WHERE lastvtime>unix_timestamp({$today}) and curjgvalue>0 and pos!={$exists['id']}
												   ORDER BY curjgvalue DESC
												   LIMIT 0,10
												");
			   if (is_array($all))
			   {
				   foreach ($all as $k => $rs)
				   {
					   $boxnum = 0;
					   $jgvl   = 0;
					   switch(($k+1))
					   {
						  case 1: $boxnum=5; $jgvl = 1000; break;
						  case 2: 
						  case 3: $boxnum=3; $jgvl = 500;break;
						  case 4:
						  case 5:
						  case 6: $boxnum=2; $jgvl = 300;break;
						  case 7:
						  case 8:
						  case 9:
						  case 10: $boxnum=1; $jgvl = 100;break;
						  default: $boxnum=$jgvl=0;
					   }
					   // ������ҵ�����.
					   $_pm['mysql']->query("UPDATE battlefield_user 
												SET tops=".($k+1).", boxnum={$boxnum}, curjgvalue=curjgvalue+{$jgvl}
											  WHERE id={$rs['id']}
										   ");
				   }
			   }
			   $time = time();
			   $_pm['mysql'] -> query("INSERT INTO gamelog (ptime,buyer,seller,pnote,vary) VALUES($time,'1','1','jgprize','200')");
			   // ʧ�ܷ�����ͳ�ƽ���
			}
		} // end out of if
		break;
	}
	/*else if ($week != $bv['days'] && ($hourM < $bv['starttime'] || $hourM > $bv['endtime']) )
	{
		die('<center><span style="font-size:12px;">ս��δ����3��</span></center>'); // record log in here.
	}*/
}

// ս�������������Է�Ů������Ϊ0����ʱ�������
/**
* @Usage: ս���Ƿ������
* @Param: none
* @Return: true of false
* Note: 
     ������2�������һ���ǶԷ�HP=0��������ս��ʱ�������
*/
function battle_end()
{
	global $_pm;
	$ends = $_pm['mysql']->getOneRecord("SELECT id
										   FROM battlefield
										  WHERE hp=0
										  LIMIT 0,1
									   ");
	if (is_array($ends))
	{
		return true;
	}
	else return false;
}
$cUser = $_pm['mysql']->getOneRecord("SELECT jgvalue,curjgvalue
										FROM battlefield_user 
									   WHERE uid={$_SESSION['id']}");

//###########################
// @Load template.
//###########################
$tn = $_game['template'] . 'tpl_battle_box.html';
if (file_exists($tn))
{
	$tpl = @file_get_contents($tn);
	
	$src = array('#userjg#',
				 '#usertop#',
	             '#desclist#',
				 '#usercurjg#'				 
				);                                                                                         
	$des = array($cUser['jgvalue'],
	             '',
				 '',
				 $cUser['curjgvalue']	         
				);
	$cet = str_replace($src, $des, $tpl);
}
// gzip echo. if maybe.
ob_start('ob_gzip');
echo $cet;
ob_end_flush();
?>
