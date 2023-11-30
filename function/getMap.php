<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.12.03
*@Usage: Expore privew. --> 进入地图限制
*@Note: 
*/

header('Content-Type:text/html;charset=GBK');
require_once('../config/config.game.php');
$m = $_pm['mem'];
$res = $_pm['mysql'] -> getOneRecord("SELECT inmap FROM player WHERE id = '".$_SESSION['id']."'");	
echo $res['inmap'];
?>
