<?php
/**
@Usage: Server message send center.
@Version: 1.0.1
@Copyright: www.xjwa.net 
*/
if(!isset($_GET['js'])) exit();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Content-Type:text/html;charset=GBK');
flush();
?>

<?php if (isset($_COOKIE['displayedMsgId'])){ ?>
// displayedMsgId=parseInt('<?php echo intval($_COOKIE['displayedMsgId']); ?>');
<?php } ?>
<?php 
flush();
if(isset($_GET['frsh'])||!is_file(dirname(__FILE__).'/messageData.php')){//没用户发消息的时候保证更新广告
	require_once(dirname(__FILE__).'/chatMessage.php');
}
?>