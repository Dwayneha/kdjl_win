<?php

require_once('../config/config.game.php');
$mapData = $_pm['mysql']->getOneRecord("select contents from welcome where code='public' ");
$str=$mapData['contents'];
$str2='�ڴ�����2 �ֻ��һ�������<br>
<li>���������ĳ����������Ϸ���磬Ů�������㶮�á�</li>
<li>���е��ֻ�ս������ϵͳ�����߹һ���ս����ֹ����</li>
<li>����Ľ����ϳɳ���ϵͳ����������ӵ����ǿ�����費���Ρ�</li>
<li>���ŵĳ��＼�ܺͷḻ��װ�����䣬ս��������ս�ԡ�</li>
<li>����400ֻ��ϵ�����������������ͣ��������</li>';
echo "OK".iconv("gbk","utf-8",$str);
echo "<br><br><br><br>";
?>
