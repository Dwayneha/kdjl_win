<?php
set_time_limit(36000);
require_once('config/config.game.php');
$sql = 'UPDATE player SET ckpwd = "",fieldpwd = "" WHERE ckpwd != "" or fieldpwd != ""';
$_pm['mysql'] -> query($sql);
echo mysql_affected_rows($_pm['mysql'] -> getConn());
?>
OK