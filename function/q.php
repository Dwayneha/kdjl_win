<?php
header('Content-Type:text/html;charset=GB2312');
/*require_once('kernel/socketmsg.v1.php');
require_once('socketChat/config.chat.php');
$s=new socketmsg();
echo $s->sendMsg(iconv('gb2312','utf-8','SYSN|information-->'),array(765));*/
//echo $s->sendMsg(iconv('gb2312','utf-8','SYS|SYS information-->'),array(765));
//echo $s->sendMsg(iconv('gb2312','utf-8','an|an information-->'),array(765));
require_once('../config/config.game.php');
/*$user = $_pm['user']->getUserById($_SESSION['id']);
echo catchTask($user,$_GET['id']);*/
$task = new task();
$task->saveGword("��������ˢ���Ѷ� 4 �ǵ���ս��<span style='cursor:pointer' color='#f44ebf' onclick='ggshow(\"��ҵ�ǰ���裺˯��������<br />������10460&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;������7792<br />���У�13360&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;������37920<br />�ɳ���32.9<br />��ǰ������Ϣ��<br />���&nbsp;��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;����&nbsp;��<br />��������&nbsp;��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\");'><a href='javascript:void(0)'>����ϸ��Ϣ��</a></span>");
?>
