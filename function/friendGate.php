<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.01
*@Update Date: 2008.05.22
*@Usage: ��Ӻ��ѡ�
*@Note: none
*/

header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');
secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$_REQUEST['name'] = trim($_REQUEST['name']);
$tu	= mysql_real_escape_string($_REQUEST['name']);
define("MEM_BLACKLIST_KEY","db_blacklist");
$blacklist = unserialize($_pm['mem'] -> get(MEM_BLACKLIST_KEY));
if ($tu=='' or empty($tu)) die('����ȷ������ҽ�ɫ����');
	
if ($_REQUEST['op'] == 'add')	//	��Ӻ��ѡ�
{
	$fret = $_pm['mysql']->getOneRecord("SELECT nickname FROM player WHERE nickname='{$tu}'");
	if (is_array($fret))
	{
		$fname = $fret['nickname'];
		
		$friendlist = $user['friendlist'];
		
		$black = $_pm['mysql'] -> getOneRecord("SELECT nickname,list FROM blacklist WHERE uid = {$_SESSION['id']}");
		if($black['list'] != ''){
			$farr = explode(',',$black['list']);
			if(array_search($fname, $farr) !== FALSE){
				die('����������ĺ������У����ܼ�����ѣ�');
			}
		}
		
		$self = $_pm['mysql'] -> getOneRecord("SELECT nickname FROM player WHERE id = {$_SESSION['id']}");
		if($self['nickname'] == $fname){
			die('������������Լ���');
		}
		
		if (strlen($friendlist)<3)
		{
			$friendlist = $fname;
		}
		else
		{
			$arr = explode(',', $friendlist);
			if (count($friendlist)>=20)
				die('��Ŀǰֻ�����20�����ѣ�');
			
			if (array_search($fname, $arr) === FALSE)
			{
				$friendlist .= ',' . $fname;
			}
			else die('���û��Ѿ��Ǻ����ˣ�');
		}
		
		$_pm['mysql']->query("UPDATE player 
					   SET friendlist='{$friendlist}'
					 WHERE id={$_SESSION['id']}
				  ");
		liststr($friendlist);
	}
	else die('��Ч����ҽ�ɫ��!');
}
else if($_REQUEST['op'] == 'del') // ɾ�����ѡ�
{	
		$friendlist = $_REQUEST['name'];
		if (strlen($friendlist)<3)
		{
			die('���û��������ĺ��ѣ�');
		}
		else
		{
			$arr = explode(',', $user['friendlist']);
			$s = array_search($_REQUEST['name'], $arr);
			if ($s === FALSE)
			{
				die('���û��������ĺ��ѣ�');
			}
			else
			{
				$friendlist = '';
				foreach($arr as $k => $v)
				{
					if ($v == $_REQUEST['name']) continue;
					$friendlist .= ','.$v;
				}
				$friendlist = substr($friendlist,1);
			}
		}
		
		$_pm['mysql']->query("UPDATE player 
					   SET friendlist='{$friendlist}'
					 WHERE id={$_SESSION['id']}
				  ");
		liststr($friendlist);
}
else if($_REQUEST['op'] == 'addblacklist')//���������
{
	$err = 10;
	
	$fret = $_pm['mysql']->getOneRecord("SELECT nickname FROM player WHERE nickname='{$tu}'");//�ҵ�Ҫ������������û�
	$fname = $fret['nickname'];
	if(!is_array($fret))
	{
		die("����ȷ������Ҫ�Ӻ������Ľ�ɫ����");
	}
	$friend = $_pm['mysql'] -> getOneRecord("SELECT nickname,friendlist FROM player WHERE id = {$_SESSION['id']}");
	if($friend['friendlist'] != ''){
		$farr = explode(',',$friend['friendlist']);
		if(array_search($fname, $farr) !== FALSE){
			die('����������ĺ��ѣ������ܼ����������');
		}
	}
	if($friend['nickname'] == $fname){
		die('������������Լ���');
	}
	
	$barr = $_pm['mysql'] -> getOneRecord("SELECT list FROM blacklist WHERE uid = {$_SESSION['id']}");
	
	if(!empty($barr['list']))
	{
		//$list = substr($blacklist[$_SESSION['id']],1,-1);
		$arr = explode(",",$barr['list']);
		$num = count($arr);
		if($num >= 30)
		{
			die("����ǰֻ�ܼ�30�����������!");
		}
		if (array_search($fname, $arr) === FALSE)
		{
			$blacklist = $barr['list'].','.$fname;
			$_pm['mysql'] -> query("UPDATE blacklist SET list = '{$blacklist}' WHERE uid = {$_SESSION['id']}");
			$_pm['mem']->del('db_blacklist');//���¼����ڴ�����
			$ret2 = $_pm['mysql']->getRecords("select uid,list from blacklist");
			foreach($ret2 as $k => $v)
			{
				$newarr[$v['uid']] = $v['list'];
			}
			$_pm['mem']->set(array('k'=>'db_blacklist','v'=>$newarr));
			liststr1($blacklist);
		}
		else die('���û��Ѿ���������������ˣ�');
	}
	else
	{
		$_pm['mysql'] -> query("INSERT INTO blacklist (uid,list) VALUES ({$_SESSION['id']},'{$fname}');");
		$_pm['mem']->del('db_blacklist');//���¼����ڴ�����
		$ret2 = $_pm['mysql']->getRecords("select uid,list from blacklist");
		foreach($ret2 as $k => $v)
		{
			$newarr[$v['uid']] = $v['list'];
		}
		$_pm['mem']->set(array('k'=>'db_blacklist','v'=>$newarr));
	}
	liststr1($fname);
	echo $err;
}
else if($_REQUEST['op'] == 'deleteblacklist')//�Ӻ�����ȡ��
{
	$err = 10;
	$fret = $_pm['mysql']->getOneRecord("SELECT nickname FROM player WHERE nickname='{$tu}'");//�ҵ�Ҫȡ�����������û�
	if(!is_array($fret))
	{
		die("����ȷ������Ҫ�Ӻ�����ȡ���Ľ�ɫ����");
	}
	$fname = $fret['nickname'];
	$blacklist = $_pm['mysql']->getOneRecord("SELECT list FROM blacklist WHERE uid='{$_SESSION['id']}'");
	//$arr = explode(',',$blacklist[$_SESSION['id']]);
	if(!is_array($blacklist))
	{
		die("���û��������ĺ������У�");
	}
	
	$arr = explode(',',$blacklist['list']);
	$s = array_search($fname,$arr);
	if ($s === FALSE)
	{
		die('���û��������ĺ������У�');
	}
	else
	{
		$blacklist = 0;
		foreach($arr as $k => $v)
		{
			if ($v == $fname) continue;
			if(empty($blacklist)){
				$blacklist = $v;
			}else{
				$blacklist .= ','.$v;
			}
		}
		$blacklists = $blacklist;
	}
	if(empty($blacklists)){
		$_pm['mysql'] -> query("delete from blacklist where uid = {$_SESSION['id']}");
	}else{
		$_pm['mysql'] -> query("UPDATE blacklist SET list = '{$blacklists}' WHERE uid = {$_SESSION['id']}");
	}
	$_pm['mem']->del('db_blacklist');//���¼����ڴ�����
	$ret2 = $_pm['mysql']->getRecords("select uid,list from blacklist");
	foreach($ret2 as $k => $v)
	{
		$newarr[$v['uid']] = $v['list'];
	}
	$_pm['mem']->set(array('k'=>'db_blacklist','v'=>$newarr));
	if(empty($blacklists)){
		echo $err;
	}else
	{
		liststr1($blacklists);
	}
}


function liststr($friendlist)
{
	if (empty($friendlist)) return false;
	$arr = explode(',',$friendlist);
	if(!is_array($arr)) $arr[0]=$friendlist;
	$f = '';
	foreach($arr as $k => $v)
	{
		$f .= "<span style='cursor:pointer;display:block;' onclick=\"chat('{$v}');\"><u>".$v . '</u></span>';
	}
	header('Content-Type:text/html;charset=GBK');
	die('#'.$f);
}
function liststr1($friendlist)
{
	if (empty($friendlist)) return false;
	$arr = explode(',',$friendlist);
	if(!is_array($arr)) $arr[0]=$friendlist;
	$f = '';
	foreach($arr as $k => $v)
	{
		if(empty($v)){
			continue;
		}
		$f .= "<span style='cursor:pointer;display:block;' onclick=\"blacks('{$v}');\"><u>".$v . '</u></span>';
	}
	header('Content-Type:text/html;charset=GBK');
	die('#'.$f);
}
?>