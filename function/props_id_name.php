<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2008.05.08
*@Update Date: 2008.05.29
*@Usage:Fightting Function.
*@Note: NO Add magic props.
  ��ģ����Ҫ���ܣ�
  1)���㹥����������BB�͹��
  2)ͬʱ��¼�û�ս���Ĺ������ݣ�����HP,MP,
  3)������Ʒ�����ݻ��ʣ�
*/
session_start();
header('Content-Type:text/html;charset=GBK');

require_once('../config/config.game.php');

$rows=$_pm['mysql']->getRecords('select id ,name from props');
foreach($rows as $row)
{
	echo $row['id'].'-'.$row['name'].'<br/>';
}