<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %1.2��%

*@Write Date: 2008.05.19
*@Update Date: 2008.05.22
*@Usage: �ֿ⴦������
*@Memo: op = s : save
	    op = g : get
		�޸������Գ�����������BUG��
*/
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
		die("1000");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['paitimes'.$_SESSION['id']] = time();
	}
}



$user	 = $_pm['user']->getUserById($_SESSION['id']);
//$userBag = $_pm['user']->getUserBagById($_SESSION['id']);

$bid = intval($_REQUEST['bid']);	// ����ID


if(empty($bid))
{
	die("10");
}

if(lockItem($bid) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}

$parr = $_pm['user']->getUserItemById($_SESSION['id'],$bid);
$n	 = intval($_REQUEST['n']);		// ��Ʒ����
if($n <= 0)
{
	unLockItem($bid);
	die("10");
}
$bagsums = $_pm['mysql'] -> getOneRecord("SELECT count(id) as sum FROM userbag WHERE zbing = 0 and sums > 0 and uid = {$_SESSION['id']}");
$cksums = $_pm['mysql'] -> getOneRecord("SELECT count(id) as sum FROM userbag WHERE zbing = 0 and bsum > 0 and uid = {$_SESSION['id']}");
if ($n <= $parr['sums'] && $_REQUEST['op'] == 's')
{
	if( ($parr['vary']==2 && ($n+$cksums['sum'])>$user['maxbase']) || 
	(($cksums['sum']+1) > $user['maxbase']) )
	{
		unLockItem($bid);
		die('4');
	}
	
/****��MYSQL���񣬽�ֹ�Զ��ύ****/		
	$_pm['mysql']->query('START TRANSACTION');
	$_pm['mysql']->query("UPDATE userbag
							 SET sums=sums-{$n},bsum=bsum+{$n}
						   WHERE id={$bid} and sums >= $n and zbing = 0
						");
	$result = mysql_affected_rows($_pm['mysql'] -> getConn());
	if($result != 1){
		unLockItem($bid);
		die("10");
	}
/**************�ύ����*************/
    $_pm['mysql']->query('COMMIT');

}
else if($n <= $parr['bsum'] && $_REQUEST['op'] == 'g')
{
	if( ($wp['vary']==2 && ($n+$bagsums['sum'])>$user['maxbag']) || 
	(($bagsums['sum']+1) > $user['maxbag']) ){
		unLockItem($bid);
		die('5');
	}

/****��MYSQL���񣬽�ֹ�Զ��ύ****/	
	$_pm['mysql']->query('START TRANSACTION');
	$_pm['mysql']->query("UPDATE userbag
							 SET sums=sums+{$n},bsum=abs(bsum-{$n})
						   WHERE id={$bid} and bsum >= $n and zbing = 0
						");
/**************�ύ����*************/
	$result = mysql_affected_rows($_pm['mysql'] -> getConn());
	if($result != 1){
		unLockItem($bid);
		die("10");
	}
                $_pm['mysql']->query('COMMIT');

}
else{
	unLockItem($bid);
	die('10');
}

unLockItem($bid);
die('0');
?>