<?php
/**
*@������Ұ����е������ռ�Ʒ��
*/

set_time_limit(0);
header('Content-Type:text/html;charset=GBK'); 
require_once('../config/config.game.php');
//secStart($_pm['mem']);

$all = $_pm['mysql']->getRecords("SELECT sum(p.sell) as cnt,uid
						  FROM userbag as b,props as p
						 WHERE p.id = b.pid and (b.sums>0) and p.varyname=4
						 group by b.uid
						 order by cnt desc
					  ");

foreach ($all as $k => $rs)
{
	// ɾ�������ռ���Ʒ��
	if ($rs['cnt']<1) continue;
	$_pm['mysql']->query("DELETE   FROM userbag USING userbag,props 
						   WHERE userbag.pid=props.id and props.varyname=4 and userbag.uid={$rs['uid']}");
	
	// �����û��Ľ�ҡ�
	$_pm['mysql']->query("UPDATE player SET money=money+{$rs['cnt']} WHERE id={$rs['uid']}");
}
echo 'done';
?>