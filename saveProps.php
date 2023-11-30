<style type="text/css">
<!--
body,td,th {
	font-size: 12px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.STYLE1 {font-size: 16px}
-->
</style>
<?php
error_reporting(0);
set_time_limit(3600);
//装备效果放入数据库   2009.10.14
require_once('config/config.game.php');
$id = intval($_GET['id']);
if($id >= 1){
	die(doSql($id));
}

$sql = 'SELECT id,name FROM props WHERE yb > 0 or buy > 0 or prestige > 0 ORDER BY id DESC';
$arr = $_pm['mysql'] -> getRecords($sql);
if(!is_array($arr)){
	die('没有任何道具数据！');
}
if($_GET['id'] == 'all'){
	echo '更新了以下数据：'.'<br />';
	foreach($arr as $v){
		$check = doSql($v['id']);
		if($check == '操作成功！'){
			echo $v['name'].'<br />';
		}else{
			echo '<font color=red>失败：'.$v['name'].'</font>'.'<br />';
		}
		flush();
		ob_flush();
	}
	exit;
}
//执行更新语句
function doSql($id){
	global $_pm;
	$equip = new equipment();
	$str = $equip -> div($id,0,0,1);
	
	$sql = "UPDATE props SET note = '$str' WHERE id = $id";
	$_pm['mysql'] -> query($sql);
	
	if(mysql_affected_rows($_pm['mysql'] -> getConn()) == 1){
		$newStr = '操作成功！';
	}else $newStr = '操作失败！';
	return $newStr;
}
?>
<a href="?id=all" class="STYLE1"><font color="#FF0000">更新所有道具</font></a>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
  <tr>
    <td width="110" height="25" align="center" bgcolor="#FFFFFF">操作</td>
    <td width="114" height="25" align="center" bgcolor="#FFFFFF">ID</td>
    <td width="272" height="25" align="center" bgcolor="#FFFFFF">名称</td>
  </tr>
  <?php
  	foreach($arr as $v){
  ?>
  <tr>
    <td height="25" align="center" bgcolor="#FFFFFF"><a href="?id=<?=$v['id']?>">更新</a></td>
    <td height="25" align="center" bgcolor="#FFFFFF"><?=$v[id]?></td>
    <td height="25" align="center" bgcolor="#FFFFFF"><?=$v['name']?></td>
  </tr>
  <?php
  	}
  ?>
</table>