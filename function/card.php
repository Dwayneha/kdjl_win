<?php
require_once('../config/config.game.php');
//secStart($_pm['mem']);
header('Content-Type:text/html;charset=GB2312');

$cardid = $_GET['cardid'];
$_SESSION['id'] = intval($_GET['userid']);
$flag = md5($cardid.$_SESSION['id'].'afecy564thgkui');
if($flag != $_GET['flag']){
	die('��������');
}
$user	 = $_pm['user']->getUserById($_SESSION['id']);//�û���Ϣ
if($cardid == ""){
	die('����д������');
}

$memcardnum = unserialize($_pm['mem']->get($cardid.$_SESSION['id']));
if($memcardnum == 'checked'){
	die('�˿��Ѿ���ȡ��');
}else if($memcardnum >= 10){
	die('�������Ѿ�����10�Σ������������ɣ�');
}

//�Ƿ����ڴ˿��ŵĿ�Ƭ

$infoarr = $_pm['mysql'] -> getOneRecord("SELECT id FROM card_info WHERE cardid = '$cardid' AND checked is null");
if(!is_array($infoarr)){
	die('���࿨Ƭ�����ڻ��Ѿ����������');
}



//�Ƿ�Ϊֻ����һ�ν��Ŀ�Ƭ
$checkarr = $_pm['mysql'] -> getOneRecord("SELECT id FROM card_info WHERE uid = {$_SESSION['id']} and cardtype = $id");
if(is_array($checkarr)){
	die('�Բ������Ѿ�������࿨Ƭ��');
}
$time = time();
$_pm['mysql'] -> query("UPDATE card_info SET uid = {$_SESSION['id']},checked = 1,times = $time WHERE cardid = '{$cardid}' AND checked is null");
if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){//�����������
	$num = $memcardnum + 1;
	$handle = $_pm['mem'] -> getHandle();
	//$handle -> set($cardid.$_SESSION['id'], $num, MEMCACHE_COMPRESSED, 43200);
	$handle->set($cardid.$_SESSION['id'], serialize($num), MEMCACHE_COMPRESSED, 50);
	die('���Ż��������');
}else{//������ȷ�����Ž���
	/*$parr = explode(',',$arr['prize']);
	if(is_array($parr)){
		$retstr = '';
		$task = new task();
		foreach($parr as $v){
			$inarr = explode(":",$v);
			$task->saveGetPropsMore($inarr[0],$inarr[1]);
			$prs = $_pm['mysql']->getOneRecord("SELECT name FROM props WHERE id={$inarr[0]}");
			if(empty($retstr)){
				$retstr = '��õ��� '.$prs['name'].'&nbsp;'.$inarr[1].' ��';
			}else{
				$retstr .= ",".$prs['name'].'&nbsp;'.$inarr[1].' ��';
			}
		}
	}*/
	$retstr = 'ok';
	$_pm['mem'] -> set(array('k'=>$cardid.$_SESSION['id'],'v'=>'checked'));
	echo $retstr;
	exit;
}
?>