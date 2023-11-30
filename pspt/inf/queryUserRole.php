<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config/config.game.php');
if (empty($_GET['user_account']) || empty($_GET['valid_date']) || empty($_GET['sign'])) {
	die('error1');
}

$time = time();
if ($_GET['valid_date'] <= $time) {
	die('error2');
}

$encryKey = '7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM';
$flag = md5($_GET['user_account'].$_GET['valid_date'].$encryKey);
if ($flag != $_GET['sign']) {
	die('error3');
}

$arr = $_pm['mysql'] -> getOneRecord("SELECT id,nickname FROM player WHERE name = '{$_GET['user_account']}'");

if (!is_array($arr)) {
	die('error4');
}

$str = $arr['id'].'&'.$arr['nickname'];
$newstr = iconv('gbk','utf-8',$str);
echo $newstr;
unset($time,$arr,$str);
?>