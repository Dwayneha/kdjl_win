<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

@Usage: PAI sell server Gate
@Write date: 2008.05.14
@Update date: 2008.07.16
@Note:
	��Ҫ��������Ϊ���ݿⷽʽ��
*/
session_start();
require_once('../config/config.game.php');
if (!defined(MAX_PAI_VALIDTIME))
	define(MAX_PAI_VALIDTIME, 10800); // �û���Ч������ʱ��

$arrobj = new arrays();
secStart($_pm['mem']);

//����һ����ȴʱ��
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
		die("12");//û�дﵽ���ʱ��
	}
	else
	{
		$_SESSION['paitimes'.$_SESSION['id']] = time();
	}
}

$err = 0;
$user		 = $_pm['user']->getUserById($_SESSION['id']);
$userbag	 = $_pm['user']->getUserBagById($_SESSION['id']);
$bid 	= intval($_REQUEST['bid']); // table userbag -> id
$n	 	= intval($_REQUEST['n']);	// num
if($n <= 0)
{
	die('2');
}
if(lockItem($bid) === false)
{
	die('�Ѿ��ڴ����ˣ�');
}
$price	= intval($_REQUEST['p']);	// price

if($price < 10){
	die('20');
}

if($price <= 0)
{
	unLockItem($bid);
	die('2');
}

$buycode = crc32($_REQUEST['bp']);
$buycode = $buycode<0?(1-$buycode-1):$buycode;

$_arr = new arrays();
$wp = $_arr->dataGet(array('k' => MEM_USERBAG_KEY, 
					  		   'v' => "if(\$rs['uid'] == '{$_SESSION['id']}' && \$rs['id'] == '{$bid}' && \$rs['zbing']==0) \$ret=\$rs;"
						      ),
						 $userbag
						);
if (!is_array($wp)) 
{
	unLockItem($bid);
	die('10');
}


//�������ж������������Ͳ���������������������
if($wp['psum'] > 0 || $wp['psell'] > 0 || $wp['psj'] > 0)
{
	unLockItem($bid);
	die('11');
}

if ( $_pm['user']->check(array('int' => $bid, 'int'=> $n, 'int' => $price)) === false || 
	 $n > 100 || 
	 $price < 1 || 
	 $price > 9999999) $err = 2;
else
{
	//�����Ʒ�Ƿ�ɽ���
	/*
	$propslock = $_pm['mysql'] -> getOneRecord("SELECT props.propslock FROM props,userbag WHERE props.id = userbag.pid and userbag.id = {$bid} and uid = {$_SESSION['id']}");
	*/
	
	// check psell num.
	$painum = 0;
	foreach ($userbag as $x => $y)
	{
		if ($y['psj']>0 && $y['psum']>0) $painum++;
		if ($painum > 3) {
			unLockItem($bid);
			die("4");
		}
	}
	
	$wp= $arrobj->dataGet(array('k' => MEM_BAG_KEY, 
							    'v' => "if(\$rs['id'] == '{$bid}' && \$rs['uid'] == '{$_SESSION['id']}') \$ret=\$rs;"
							   ),
						  $userbag
						 );

	if (!is_array($wp) || $n > $wp['sums']) $err=3;
	else
	{   // �Ƿ�ɽ��׼��
		$propslock = $_pm['mem']->dataGet(array('k' => MEM_PROPS_KEY, 
												'v' => "if(\$rs['id'] == '{$wp['pid']}') \$ret=\$rs;"
									  	 ));
										 
		$wpInfo = $_pm['mysql']->getOneRecord("SELECT cantrade 
											 FROM userbag
											 WHERE id='$bid' and uid=".$_SESSION['id']
											 );
					//0Ϊ���ɽ���            //0 Ϊ�ɽ���
		if($wpInfo['cantrade'] == 0){
			if($propslock['propslock']  == 0){
				unLockItem($bid);
				die("5");
			}
		}else if($wpInfo['cantrade'] != 1){
			unLockItem($bid);
			die("5");
		}							 
		/*if($propslock['propslock']  == 0 && $wpInfo['cantrade']!=1 ){
			unLockItem($bid);
			die("5");
		}*/
		
		
		
		
		$now = time();
		
		$num1 = $_REQUEST['num1'];
		$_pm['mysql'] -> query("insert into gamelog (ptime,seller,buyer,pnote,vary) values($time,{$_SESSION['id']},{$_SESSION['id']},'$num1',155)");
		
		$et  = $now + MAX_PAI_VALIDTIME; 
		
/****��MYSQL���񣬽�ֹ�Զ��ύ****/
	$_pm['mysql']->query('START TRANSACTION');	
    $_pm['mysql']->query("UPDATE userbag
					   SET psj={$price},
						   pstime={$now},
						   petime={$et},
						   sums=sums-{$n},
						   psum=psum+{$n},
						   buycode={$buycode}
					 WHERE id={$bid} and uid={$_SESSION['id']} and sums >= $n
				   ");
				   
/**************�ύ����*************/
                $_pm['mysql']->query('COMMIT');
		
	}
}
$_pm['mem']->memClose();
echo $err;
unLockItem($bid);
?>