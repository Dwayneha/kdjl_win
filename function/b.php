<?php 
/*
 * ���ļ���20081217ֻ��������memcache���Ѿ��޸�Ϊֱ���޸�������������memcache
 */

session_start();
require_once('../config/config.game.php');
print_r($_pm['mem']->get('pm_team_fight_434'));
?>