<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config/config.game.php');
if (empty($_GET['user_account']) || empty($_GET['role_id']) || empty($_GET['valid_date']) || empty($_GET['valid_key']) || empty($_GET['sign']) || empty($_GET['type'])) {
	die('error');
}

$time = time();
if ($time >= $_GET['valid_date']) {
	die('error');
}

$encryKey = '7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM';
$flag = md5($_GET['user_account'].$_GET['valid_date'].$encryKey);
if ($flag != $_GET['sign']) {
	die('error');
}

$sign = md5($_GET['valid_key'].$encryKey);
$url = 'http://passport.webgame.com.cn/validKey?game_code=pm&valid_key='.$_GET['valid_key'].'&sign='.$sign.'&user_account='.$_GET['user_account'];
$result = @file_get_contents($url);
if ($result != 'valid') {
	die('error');
}

if ($_GET['type'] == 1) {
	$sql = 'ckpwd = ""';
	$field = 'ckpwd';
}elseif ($_GET['type'] == 2){
	$sql = 'fieldpwd = ""';
	$field = 'fieldpwd';
} else {
	die('error');
}
$arr = $_pm['mysql'] -> getOneRecord("SELECT $field FROM player WHERE name = '{$_GET['user_account']}' AND password != '00000000000000000000000000000000'");
if(empty($arr[$field])){
	die('nopwd');
}
$_pm['mysql'] -> query("UPDATE player SET $sql WHERE id = {$_GET['role_id']} and name = '{$_GET['user_account']}'");
//echo "UPDATE player SET $sql WHERE id = {$_GET['role_id']} and name = '{$_GET['user_account']}'";exit;
if (mysql_affected_rows() != 1) {
	die('error');
}else {
	die('success');
}
?>