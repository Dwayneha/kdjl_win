<?php 
/**
@Usage:ս��ʹ�õ���Ӱ����Ӫ�ű���
@Write: 2008-09-02
@Note:
  ���䱦ʯ�����ٶԷ�Ů��100�������������������50��
  �������ʵ���ָ��ҷ�Ů������1000�㣬�����������500��
  Ů��ʥˮ������ս���ڻ��˫��������ս��������ʧЧ
  ------------------------------------------------
  4: ��ȡ����
  5����ȡ����
  6����ȡ����
*/
session_start();
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GBK'); 
secStart($_pm['mem']);

$srctime = 5;
#################����һ�����ʱ��################
$time = $_SESSION['paitimes'.$_SESSION['id']];
if(empty($time))
{	
	$_SESSION['paitimes'.$_SESSION['id']] = time();
}
else
{
	$nowtime = time();
	$ctime = $nowtime - $time;
	if($ctime < $srctime)
	{
		die("û�дﵽ���ʱ��!");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['paitimes'.$_SESSION['id']] = time();
	}
}
//die('ά���У�');
$memtimeconfig = unserialize($_pm['mem']->get('db_timeconfignew'));
$arr = $memtimeconfig['usejg'];
$useJG = true;
foreach($arr as $v){
	if(is_array($v) && $v['days'] == '1'){
		$useJG=false;
	}
}
define('USEJG',$useJG);

if(lockItem(1) === false)
{
	die('��������æ�����Ժ������');
}

$num  = intval($_REQUEST['t']);
$num  = $num<1?0:$num;

$user	= $_pm['user']->getUserById($_SESSION['id']);
//$bag	= $_pm['user']->getUserBagById($_SESSION['id']);
require_once('../sec/dblock_fun.php');
$a = getLock($_SESSION['id']);
if(!is_array($a))
{
	realseLock();
	unLockItem($id);
	die('��������æ�����Ժ����ԣ�');
}
switch ($num)
{
	case 1:	usePropsOfBattle(1);break;	//  ���䱦ʯ
	case 2: usePropsOfBattle(2);break;	//  �������ʵ
    case 3: usePropsOfBattle(3);break;	//  Ů��ʥˮ
	case 4: 
	{
		getBattleGoldBox(4);
		break;	//  ��ȡ����
	}
	case 5: 
	{
		getBattleExp(5);
		break;		//  ��ȡ����
	}
	case 6: 
	{
		getBattleProps(6);
		break;	//  ��ȡ����
	}
	default:
		unLockItem(1);
		realseLock();
		die("����ʹ��ʧ�ܣ�");
}
realseLock();
unLockItem(1);
function initJGLog(){
	global $_pm;
	$sql = "
	CREATE TABLE if not exists`jg_log` (
	  `id` int(8) NOT NULL AUTO_INCREMENT,
	  `uid` int(11) NOT NULL DEFAULT '0',
	  `usejg` int(9) DEFAULT '0',
	  `type` varchar(10) DEFAULT '',
	  `num` varchar(10) DEFAULT '',
	  `pid` varchar(50) DEFAULT '',
	  `times` int(10) DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `uid` (`uid`)
	) ENGINE=MyISAM CHARSET=gbk; 
	";
	 $_pm['mysql']->query($sql);
	 echo mysql_error();
}
function logJgUse($jg,$type,$num,$pid){
	global $_pm;
	$sql = '
	insert into jg_log
		(uid,usejg,type,num,pid,times)
	values(
		'.$_SESSION['id'].','.$jg.',"'.$type.'","'.$num.'","'.$pid.'",unix_timestamp()
	)
	';
	$_pm['mysql']->query($sql);
	echo mysql_error();
}
// ����ҵĵ�����Ч��
function usePropsOfBattle($n)
{
	global $_pm;
	$ubid = 0;
	$cUser = $_pm['mysql']->getOneRecord("SELECT pos,bid,failackvalue,id,nscf,addhp,subhp
											FROM battlefield_user
										   WHERE uid={$_SESSION['id']}
											");
    if ($n == 1) 
	{
		$arr = $_pm['user']->getUserBagItemById($_SESSION['id'],203);
		if(is_array($arr) && $arr['sums'] > 0){
			$ubid = $arr['pid'];
		}
		if ($ubid>0)
		{
			// ��ȴʱ���� 60��
			if ($cUser['subhp']+60>time()) {
				unLockItem(1);
				realseLock();
				die('����ʹ��ʱ����ȴ�У���� '.($cUser['subhp']+60-time()).' �����ԣ�');
			}

			// ���Է�Ů���HP�Ƿ�С�����Ƶ����ݡ�
			$limit = $_pm['mysql']->getOneRecord("SELECT hp
												    FROM battlefield 
												   WHERE id!={$cUser['pos']}
												");
			if ($limit['hp'] < 1000) {
				unLockItem(1);
				realseLock();
				die('�Է�Ů���������� 1000 �㣬�޷�ʹ�øõ���!');
			}

			// ս���Ƿ������
			if (battle_timeout_check()===true)
			{
				unLockItem(1);
				realseLock();
				die('����ս���Ѿ�����������ʹ�øõ��ߣ�');
			}

			$_pm['mysql']->query("UPDATE battlefield
									 SET hp=hp-100
								   WHERE id!={$cUser['pos']} and hp>=1000
								");
			$_pm['mysql']->query("UPDATE battlefield_user
									 SET curjgvalue=curjgvalue+50,
									     subhp=".time()."
								   WHERE id={$cUser['id']}
								");
			$brs = $_pm['mysql']->getOneRecord("SELECT posname 
			                                      FROM battlefield
												 WHERE id!={$cUser['pos']}
												 LIMIT 0,1
											  ");
			// Format: :"XXX(�����) ʹ�á����䱦ʯ������Է�Ů��(�Է���Ӫ������)Ů��HP����100�㡣
			$word = " ,ʹ�� <���䱦ʯ>,����Է�Ů��,{$brs['posname']}Ů��HP���� 100 ��!";
			aword($word);
			echo 'ʹ�õ��߳ɹ����������� 50 ��';
		}
	}
	else if ($n == 2)
	{
		$arr = $_pm['user']->getUserBagItemById($_SESSION['id'],204);
		if(is_array($arr) && $arr['sums'] )
		{
			$ubid = $arr['pid'];
		}
		if ($ubid>0)
		{
			// ս���Ƿ������
			if (battle_timeout_check()===true)
			{
				unLockItem(1);
				realseLock();
				die('����ս���Ѿ�����������ʹ�øõ��ߣ�');
			}

			if ($cUser['addhp']+600>time()) 
			{
				unLockItem(1);
				realseLock();
				die('����ʹ��ʱ����ȴ�У���� '.($cUser['addhp']+600-time()).' �����ԣ�');
			}

			$selfField = $_pm['mysql']->getOneRecord("SELECT id,srchp,hp,posname
														FROM battlefield 
													   WHERE id={$cUser['pos']}");
			$week=date("N", time());
			$hourM=date("H:i", time());
			$battletimearr = unserialize($_pm['mem']->get(MEM_TIME_KEY));

			foreach($battletimearr as $bv)
			{
				if($bv['titles'] != "battle")
				{
					continue;
				}
				if($selfField['hp'] != 0 && $week == $bv['days'] && $hourM >= $bv['starttime'] && $hourM <= $bv['endtime'])
				{
					$checkstr = 1;
					break;
				}
			}
			if(empty($checkstr))
			{
				unLockItem(1);
				realseLock();
				die('ս���ѽ���������ʹ�øõ��ߣ�');
			}
			
            if ($selfField['hp']+1000>$selfField['srchp']) 
				 $sumhp = $selfField['srchp'];
			else $sumhp = $selfField['hp']+1000;

			$_pm['mysql']->query("UPDATE battlefield
									 SET hp={$sumhp}
								   WHERE id={$cUser['pos']}
								");
			$_pm['mysql']->query("UPDATE battlefield_user
									 SET curjgvalue=curjgvalue+500,
									     addhp=".time()."
								   WHERE id={$cUser['id']}
								");
			$word = " ,ʹ��<������Ĺ�ʵ>,{$selfField['posname']}Ů��HP�ָ� 1000 ��!";
			aword($word);
			echo 'ʹ�õ��߳ɹ����������� 500 ��';
		}
	}
	else if ($n == 3)
	{
		$arr = $_pm['user']->getUserBagItemById($_SESSION['id'],205);
		if(is_array($arr) && $arr['sums'] )
		{
			$ubid = $arr['pid'];
		}
		if ($ubid>0)
		{
			if ($cUser['nscf']==1) {
				unLockItem(1);
				die('ÿ���ʱ��ֻ��ʹ�õ��ߵõ�һ��Ů��͸���');
			}
			
			// ս���Ƿ������
			if (battle_timeout_check()===true)
			{
				unLockItem(1);
				die('����ս���Ѿ�����������ʹ�øõ��ߣ�');
			}

			$_pm['mysql']->query("UPDATE battlefield_user
									 SET doublejg=1,nscf=1
								   WHERE id={$cUser['id']}
								");
		}
		else
		{
			unLockItem(1);
			realseLock();
			die("��û����ص���Ʒ~��");
		}
	}

	if ($ubid>0) // $uid => table:userbag's id
	{
		$_pm['mysql']->query("UPDATE userbag
							     SET sums=abs(sums-1)
							   WHERE pid={$ubid} and uid={$_SESSION['id']} and sums > 0
		                     ");
		unLockItem(1);
		realseLock();
		die('ʹ�õ��߳ɹ���');
	}
	else {
		unLockItem(1);
		realseLock();
		die("����ʹ��ʧ�ܣ�");
	}
}

/**
*@Usage: ��ȡ����
*@Param: $v =>  ��������
*@Return: void(0);
*/
function getBattleGoldBox($n)
{
	if(!USEJG)
	{
		realseLock();	
		die('����ʹ����ʱ�رգ���������ԣ�');
	}

	global $_pm;

	$boxid = 0;
    switch($_REQUEST['v'])
	{
		case 1: $boxid=1059;break;// ��Ȼ����
		case 2: $boxid=1060;break;// ��ҹ����
		case 3: $boxid=1061;break;// ��ʥ����
		default: 
			unLockItem(1);
			realseLock();
			die('��û�����������Ѿ���ȡ������');
	}

	// ��ȡ�û��ľ������������ж�Ӧ������
	$uinfo = $_pm['mysql']->getOneRecord("SELECT boxnum  
	                                        FROM battlefield_user
										   WHERE uid={$_SESSION['id']}
										");
    if (!is_array($uinfo) || $uinfo['boxnum']<1) {
		unLockItem(1);
		realseLock();
		die('��û�����������Ѿ���ȡ������');
	}
	$tsk = new task();
	$idlist='';
	for($i=0; $i<$uinfo['boxnum'];$i++)
	{
		$idlist .= $idlist==''?	$boxid:','.$boxid;
	}

	$tsk->saveGetProps($idlist);
	// �����û���ȡ��ǡ�
	$_pm['mysql']->query("UPDATE battlefield_user
	                         SET boxnum=0
						   WHERE uid={$_SESSION['id']}
						 ");
	initJGLog();
	logJgUse(0,'GoldBox','x1',$idlist);
	unLockItem(1);
	realseLock();
	die('��ϲ������� '.$uinfo['boxnum'].' ����!');
}

/**
*@Usage: ��ȡ����
*@Param: $j => ��ȡ�ľ�����
*@Return: void(0);
��@Note: ÿ������һ��ľ���ֵ=��ս����ȼ�*100
*/
function getBattleExp($n)
{
	global $_pm;
	if(!USEJG)
	{
		realseLock();
		die('����ʹ����ʱ�رգ���������ԣ�');
	}

//===
//die('�һ���ʱ�رգ�');

	$jg = intval($_REQUEST['j']);
	$jg = $jg<1?0:$jg;
    // ��õ�ǰ�û��ľ�������
	$cur = $_pm['mysql']->getOneRecord("SELECT jgvalue
	                                      FROM battlefield_user
										 WHERE uid={$_SESSION['id']} and jgvalue>0
									  ");
   if (is_array($cur) && $cur['jgvalue'] >= $jg)
   {
		$user	 = $_pm['user']->getUserById($_SESSION['id']);
		$bb      = $_pm['mysql']->getOneRecord("SELECT level 
												 FROM userbb 
												WHERE uid={$_SESSION['id']} and id={$user['mbid']}
											 ");
        if (!is_array($bb)){
			unLockItem(1);
			realseLock();
			 die('���ȵ�����������ս���');
			}
		
		// �۳�������
		$_pm['mysql']->query("UPDATE battlefield_user 
		                         SET jgvalue=jgvalue-{$jg}
							   WHERE uid={$_SESSION['id']} and jgvalue >= $jg
							");
		$result = mysql_affected_rows($_pm['mysql'] -> getConn());
		if($result != 1){
			unLockItem(1);
			realseLock();
			die('�������㣡');
		}
		$exp   = $jg*$bb['level']*100;
        // �洢���飺
		$t = new task();
		$t->saveExps($exp);
		
		initJGLog();
		logJgUse($jg,'BattleExp',$exp,0);
		unLockItem(1);
		realseLock();
		die('��ϲ������ս�������� '.$exp.' �㾭��');
   }else {
   		realseLock();
   		unLockItem(1);
		die('����ս�����ֲ��㣡');
	}
}

/**
*@Usage: ��ȡ����
*@Param: $p => ����id, $s => ��ȡ�ĵ���������
*@Return: void(0);
*/
function getBattleProps($n)
{
	global $_pm;
	if(!USEJG)die('����ʹ����ʱ�رգ���������ԣ�');

//die('�һ���ʱ�رգ�');

    $pid = intval($_REQUEST['p']);
	$pid = $pid<1?0:$pid;
    $num = intval($_REQUEST['s']);
	$num = $num<1?0:$num;

	if ($num>0 && $pid>0)
	{
		$existsP = $_pm['mysql']->getOneRecord("SELECT need
		                                          FROM battlefield_props
												 WHERE pid={$pid}
											   ");
		if (is_array($existsP))
		{
			$need = $num*$existsP['need'];
			// ��ȡ�û��ľ���ֵ
			$cur = $_pm['mysql']->getOneRecord("SELECT jgvalue
												  FROM battlefield_user
												 WHERE uid={$_SESSION['id']} and jgvalue>0
											  ");
			if ($cur['jgvalue'] >= $need)
			{
				$tsk = new task();
				
				$res = $tsk->saveGetPropsMore($pid,$num);
				if($res === "200")
				{
					realseLock();
					unLockItem(1);
					die("���ı������������������Լ��ı�����");
				}
				// �����û�����
				$_pm['mysql']->query("UPDATE battlefield_user
										 SET jgvalue=jgvalue-{$need}
									   WHERE uid={$_SESSION['id']} AND jgvalue >= $need
									 ");
				$result = mysql_affected_rows($_pm['mysql'] -> getConn());
				if($result != 1){
					realseLock();
					unLockItem(1);
					die('�������㣡');
				}
				initJGLog();
				logJgUse($need,'BattleProps',$num,$pid);
				realseLock();
				unLockItem(1);
				die('��ϲ������ȡ���߳ɹ�!');
			}
			else {
				unLockItem(1);
				realseLock();
				die('���ľ�������������');
			}
		}
	}
}
// Say word to game chat.
function aword($msg)
{
	$aw = new task();
	$aw-> saveGword($msg);
}

/**
*@Usage: ս���Ƿ������
*/
function battle_timeout_check()
{
	global $_pm;
	$ends = $_pm['mysql']->getOneRecord("SELECT id
										   FROM battlefield
										  WHERE ends=1
										  LIMIT 0,1
									   ");
	if (is_array($ends))
	{
		return true;
	}
	else return false;
}

$_pm['mem']->memClose();
//####################
?>