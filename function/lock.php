<?php
header('Content-Type:text/html;charset=GB2312');
require_once('../config/config.game.php');
secStart($_pm['mem']);
$id = intval($_GET['id']);
if($id < 1){
	die('2');//��������
}

$pcheck = $_pm['mysql'] -> getOneRecord("SELECT cantrade FROM userbag WHERE id = $id AND uid = {$_SESSION['id']}");
if($pcheck['cantrade'] == 3){
	die('4');//�Ѿ�����
}

$_pm['mysql'] -> query("UPDATE userbag SET sums = sums - 1 WHERE uid = {$_SESSION['id']} AND pid = 2355 AND sums >= 1");
if(mysql_affected_rows($_pm['mysql'] -> getConn()) != 1){
	die('1');//û�м�������
}

$_pm['mysql'] -> query("UPDATE userbag SET cantrade = 3 WHERE id = $id AND uid = {$_SESSION['id']}");
die('3');
?>