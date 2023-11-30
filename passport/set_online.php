<?php
/**
* online set.
*/
require_once("../config/config.game.php");
header('Content-Type:text/html;charset=gbk');
$db = new mysql();
$db->query("update  chat_login_auth set  is_online=0");
PRintf  ("Updated records:  %d\n", mysql_affected_rows());
?>
