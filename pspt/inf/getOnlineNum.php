<?php
header('Content-Type:text/html;charset=GBK'); 
$encryKey = 'Luv31MKUHL9hJoiuPOeEFPBMJZdRZ423hgGfXF1pyM';

require_once(dirname(dirname(dirname(__FILE__))).'/config/config.game.php');
if($encryKey!=$_GET['key']) exit("0");

$min = 300;
$domainPrefix = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],"."));
$rs = unserialize($_pm['mem']->get($domainPrefix.'_online_user'));
echo $rs+300;
?>