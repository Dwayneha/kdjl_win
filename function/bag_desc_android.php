<?php

require_once('../config/config.game.php');
$mapData = $_pm['mysql']->getOneRecord("select contents from welcome where code='public' ");
$str=$mapData['contents'];
$str2='口袋精灵2 手机挂机辅助：<br>
<li>超卡哇伊的宠物形象和游戏画风，女神的最爱，你懂得。</li>
<li>独有的手机战斗放置系统，在线挂机，战斗无止境。</li>
<li>神奇的进化合成宠物系统，让你早日拥有最强的龙宠不是梦。</li>
<li>超炫的宠物技能和丰富的装备搭配，战斗更有挑战性。</li>
<li>超过400只五系宠物等你领养！根本停不下来。</li>';
echo "OK".iconv("gbk","utf-8",$str);
echo "<br><br><br><br>";
?>
