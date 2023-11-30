<?php
require_once('./config/config.game.php');
$sid = $_SERVER['session_id'];

$sql = "UPDATE chat_login_auth SET is_online=0 WHERE sid='{$sid}'";
$_pm['mysql']->query($sql);
echo "ok";



