<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: ̷�

*@Write Date: 2008.09.24
*@Update Date: 
*@Usage:�õ��û��Զ�ս���ķ�ʽ
*@Note: NO Add magic props.
  ��ģ����Ҫ���ܣ�
  1)�õ��û��Զ�ս���ķ�ʽ
*/
require_once('../config/config.game.php');

secStart($_pm['mem']);
$way = intval($_REQUEST['way']);
if(empty($way))
{
	$_SESSION['way'.$_SESSION['id']] = "money";//Ĭ��Ϊ��Ұ�
}
if($way == 1)
{
	$_SESSION['way'.$_SESSION['id']] = "money";//��Ұ�
	$err = 1;
}
else if($way == 2)
{
	$_SESSION['way'.$_SESSION['id']] = "yb";//Ԫ����
	$err = 2;
}
echo $err;
?>