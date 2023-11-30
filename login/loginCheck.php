<?php
/**
* user name. Check.
*/
require_once("../config/config.game.php");
//secStart($_pm['mem']);
header('Content-Type:text/html;charset=gbk');
session_start();
if(preg_match_all("/[^0-9a-zA-Z_\\x7f-\\xff]+/",$_GET['n'],$aaa))
//if(preg_match_all("/[^0-9a-zA-Z_]+/",$_GET['n'],$aaa))
{
	die('error');
}
$_user = strlen(iconv('utf-8','gbk',$_GET['n']))==0?$_GET['n']:iconv('utf-8','gbk',$_GET['n']);
$db = new mysql();
$rs = $db->getOneRecord("SELECT id FROM player WHERE nickname = {'$_user'}");	
if (is_array($rs))
{
	die("<span style='color:#f00;font-size:12px'>ÒÑ¾­´æÔÚ</span>");
}

echo 'OK';
?>
