<?php 
/**
@Usage: ��Ϸ��������ܡ�
2.	�ж���������Ƿ���С��й����͡�����,����ID:817������У���������6��˫��������ᣨID��745��
*/
exit();

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user		= $_pm['user']->getUserById($_SESSION['id']);
$bag		= $_pm['user']->getUserBagById($_SESSION['id']);

// time limit start
$tl = mktime(10,0,0,date("m",time()),date("d",time()),date("y",time()));

$lb = $_pm['mysql']->getOneRecord("SELECT id,cet
									 FROM libao
									WHERE pname='{$user['id']}'
								 ");
if (is_array($lb) &&  (($lb['cet'] > $tl) || time()<$tl  ) )
{
	die('���Ѿ���ȡ���ˣ�������10���������');
}

$rs = $_pm['mysql']->getOneRecord("SELECT id 
									 FROM userbag
									WHERE uid={$_SESSION['id']} and pid=817 and sums>0
								");

if (is_array($rs)) // �û�Я���иõ���,3��˫����
{
	$tsk = new task();
	$tsk->saveGetProps('745,745,745,745,745');
	// ɾ��һ�����߲������Ѿ���ȡ�����ı�ǡ�
	$_pm['mysql']->query("UPDATE userbag
						     SET sums=abs(sums-1)
						   WHERE uid={$_SESSION['id']} and id={$rs['id']} and sums > 0
						");
	if (is_array($lb)) // �Ѿ����ڸ��û�������ʱ�䡣
	{
		$_pm['mysql']->query("UPDATE libao
							     SET cet='".time()."'
							   WHERE id='{$lb['id']}'
							");
	}
	else $_pm['mysql']->query("INSERT INTO libao(pname,flag,cet)
							   VALUES('{$user['id']}',1,'".time()."')
							  ");
	die('��ϲ������ȡ�����ɹ���');   
}
else die('����ƭ�ң����İ�����û���й����͵ĵ��߰ɣ�');
?>