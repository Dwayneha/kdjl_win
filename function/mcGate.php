<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.19
*@Update Date: 2008.07.13
*@Usage: Save and Get pets of user
*/

header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');
if($_REQUEST['from'] != 1)
{
	secStart($_pm['mem']);
}
$id = intval($_REQUEST['id']); // table: userbb => id
$op = $_REQUEST['op'];


if ($_pm['user']->check(array('int' => $id)) === false) die('���ݴ���1��'); // ��������ݡ�

$user	 = $_pm['user']->getUserById($_SESSION['id']);



$userbb  = $_pm['user']->getUserPetById($_SESSION['id']);
$mc		 = 0;
$bagmc	 = 0;
if($op =='z' && $id == $user['mbid']){
	die('�Ѿ�����ս��');
}
if (!is_array($userbb) || !is_array($user)) die('���ݴ���2��');

$valid = false;
foreach ($userbb as $k => $v)
{
	if ($v['muchang'] == 1) $mc++;
	else if($v['muchang'] == 0) $bagmc++;
	if ($v['id'] == $id) $valid=true;
}
if ($valid === false) die('���ݴ���3��');

// Set main fight pets

if ($op =='z' && $id != $user['mbid'])
{

	//����ս�����ʱ��Ҫ�ж�����ǰ�������ǲ��ǲ������л����������
	$sql = "SELECT taskid FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = 9999";
	$arr = $_pm['mysql'] -> getOneRecord($sql);
	if(is_array($arr))
	{
		die("10");//�����л���ս��
	}
	foreach($userbb as $v){
		if($v['id'] == $id && $v['muchang'] != 0 ){
			die('�������ı���������Ϊ��սŶ��');
		}
	}
	$_pm['mysql']->query("UPDATE player 
				   SET fightbb={$id},mbid={$id}
				 WHERE id={$_SESSION['id']}
			  ");
	//$_pm['user']->updateMemUser($_SESSION['id']);
	if($_REQUEST['from'] == 1)
	{
		die("OK");
	}
	die('������ս�����ɹ�!');
}
else if($op =='change' && $id != $user['mbid'])
{
	foreach($userbb as $v){
		if($v['id'] == $id && $v['muchang'] != 0 ){
			die('�������ı���������Ϊ��սŶ��');
		}
	}
	$_pm['mysql']->query("UPDATE player 
				   SET mbid={$id},task = '',tasklog = ''
				 WHERE id={$_SESSION['id']}
			  ");
			  
	$_pm['mysql']->query("DELETE FROM tasklog WHERE uid = {$_SESSION['id']} and taskid = 9999
			  ");
	die("������ս�����ɹ���");
}
// Save pets
if ($op=='s' && $mc<$user['maxmc'] && $bagmc>1 && $user['mbid'] != $id)
{
	
	$_pm['mysql']->query("UPDATE userbb
				   SET muchang=1
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
	if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
		die('�Ѿ���������');
	}
	//$_pm['user']->updateMemUserbb($_SESSION['id']);
	die("�����ɹ�!");
} 
else if ($op == 'g' && $bagmc<3)
{
	if(!empty($user['fieldpwd'])&&empty($_SESSION['loginField'.$_SESSION['id']])){
	  die('���ȵ�¼ !');
	}
	$res = $_pm['mysql']-> getOneRecord("SELECT * FROM userbb  WHERE uid={$_SESSION['id']} and id={$id} and (chchengsx is null or chchengsx = '')");
	if(!is_array($res))
	{
		die("�����в���ȡ��");
	}
	$_pm['mysql']->query("UPDATE userbb
				   SET muchang=0
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
	if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
		die('�˳����Ѿ�Я����');
	}
	//$_pm['user']->updateMemUserbb($_SESSION['id']);
	die("�����ɹ�!");
}
// del
else if ($op == 'd' && $user['mbid'] != $id)
{
	if($user['money']<10000){
		die('��û���㹻��Ľ��Ŷ��');
	}
	//print_r($user);
    $pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
    if(empty($pwd) && !empty($user['fieldpwd']))
    {
        die("���������룡");//���������룡
    }
    $pwd = abs(crc32(md5($pwd)));
    if($pwd != $user['fieldpwd']  && !empty($user['fieldpwd']))
    {
        die("1");//�������
    }
    //exit;
	//������־��5.13
	$time = time();
	$bb = $_pm['mysql'] -> getOneRecord("SELECT * FROM userbb WHERE id = {$id}"); 
	if(!empty($bb['zb']))
	{
		$str = "װ����";
		//4:291791,9:268156,5:424046,6:300872,3:328876,1:379748,2:334791,7:308728
		$arr = explode(",",$bb['zb']);
		foreach($arr as $v)
		{
			$bagid = explode(":",$v);
			$newarr = $_pm['mysql'] -> getOneRecord("SELECT pid FROM userbag WHERE id = {$bagid[1]}");
			$str .= $newarr['pid'].',';
		}
	}
	$str .= "�ȼ���".$bb['level'];
	$str .=",�ɳ���".$bb['czl'];
	$str .=",���֣�".$bb['name'];
	$_pm['mysql'] -> query("INSERT INTO gamelog (ptime,seller,buyer,pnote,vary) values({$time},{$_SESSION['id']},{$_SESSION['id']},'{$str}',16)");
	// del sk. 
	$_pm['mysql']->query("DELETE FROM skill
				 WHERE bid={$id}
			  ");
	// del zb.
	$_pm['mysql']->query("DELETE FROM userbag
				 WHERE uid={$_SESSION['id']} and zbpets={$id}
			  ");
	// del bb.
	$_pm['mysql']->query("DELETE FROM userbb
				 WHERE uid={$_SESSION['id']} and id={$id}
			  ");
	//$_pm['user']->updateMemUserbb($_SESSION['id']);
	//$_pm['user']->updateMemUsersk($_SESSION['id']);
	//$_pm['user']->updateMemUserbag($_SESSION['id']);

	die("�����ɹ�!");
}
$_pm['mem']->memClose();
unset($db);

if($bagmc==3)
{
	if($op == 's' && $user['mbid'] == $id)
	{
		die('�ó���Ϊ��ս����޷�������');
	}
	if($op == 'g' || $op == 's')
	{
		die('�˳����Ѿ�Я����');
	}
	else
	{
		die('�����ͬʱֻ��Я��3��������');
	}
	
}
else if($mc==$user['maxmc'])
{
	die('���ĳ�������ռ����������ܼ������౦����');
}
else if($bagmc==1)
{
	die('������Я��һ���������Ա�μ�ս��!');
}
die("����ʧ��!");
?>
