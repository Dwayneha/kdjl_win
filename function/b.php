<?php 
/*
 * 此文件自20081217只用来清理memcache，已经修改为直接修改其它服务器的memcache
 */

session_start();
require_once('../config/config.game.php');
print_r($_pm['mem']->get('pm_team_fight_434'));
?>