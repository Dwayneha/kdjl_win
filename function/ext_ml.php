<?php
/**
*@Version: %version%
*@Copyright: %copyright%
*@Author: %author%

*@Write Date: 2009.12.8
*@Update Date: 
*@Usage: ����
*@Note: none
*/
require_once('../config/config.game.php');
header('Content-Type:text/html;charset=GB2312');
secStart($_pm['mem']);
if($_GET['action'] == 'ml'){
	$mlarr = $_pm['mysql'] -> getRecords('SELECT nickname,ml FROM player,player_ext WHERE player.id = player_ext.uid AND ml > 0 ORDER BY ml DESC limit 50');
	$html = '<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC" >
      <tr>
        <td height="20" colspan="3" align="center" bgcolor="#FFFFFF">��������</td>
        </tr>
      <tr>
        <td height="20" align="center" bgcolor="#FFFFFF">����</td>
        <td height="20" align="center" bgcolor="#FFFFFF">��ɫ</td>
        <td height="20" align="center" bgcolor="#FFFFFF">����</td>
      </tr>';
	  if(empty($mlarr)){
	  	$html .= '<tr>
        <td height="20" colspan="3" align="center" bgcolor="#FFFFFF">���а�Ϊ�գ�</td>
        </tr>';
	  }else{
	  	$i = 1;
	  	foreach($mlarr as $v){
			$html .= '<tr>
        <td height="20" align="center" bgcolor="#FFFFFF">'.$i.'</td>
        <td height="20" align="center" bgcolor="#FFFFFF" style="cursor:pointer" onclick=\'giveTo("'.$v['nickname'].'")\'>'.$v['nickname'].'</td>
        <td height="20" align="center" bgcolor="#FFFFFF">'.$v['ml'].'</td>
      </tr>';
			$i++;
		}
	}
	$html .= '</table>';
	die($html);
}
$sums = intval($_GET['sums']);
$uname = htmlspecialchars(iconv('utf-8','gbk',$_GET['uname']));
$pname = htmlspecialchars(iconv('utf-8','gbk',$_GET['pname']));
if($sums < 1 || empty($uname) || empty($pname)){
	die('a');//��д����
}

$ucheck = $_pm['mysql'] -> getOneRecord("SELECT id FROM player WHERE nickname = '$uname' and password != '00000000000000000000000000000000'");
if(empty($ucheck)){
	die('b');//�û�����д����ȷ
}
if($ucheck['id'] == $_SESSION['id']){
	die('e');
}
$pcheck = $_pm['mysql'] -> getOneRecord("SELECT userbag.id as bid,name,effect FROM props,userbag WHERE userbag.pid = props.id AND name = '$pname' AND varyname = 17 AND sums >= $sums AND uid = {$_SESSION['id']}");
if(empty($pcheck)){
	die('c');//���������Ʋ���
}

$_pm['mysql']->query("UPDATE userbag SET sums = sums - $sums WHERE id = {$pcheck['bid']} AND uid = {$_SESSION['id']} AND sums >= $sums");

$result = mysql_affected_rows($_pm['mysql'] -> getConn());
if($result != 1){
	die('d');//��������
}

$arr = explode(':',$pcheck['effect']);
$num = $arr[1] * $sums;


$ar = $_pm['mysql'] -> getOneRecord("SELECT uid FROM ml WHERE uid = {$_SESSION['id']} AND tid = {$ucheck['id']}");
if(empty($ar)){
	$_pm['mysql'] -> query("insert into `ml` (uid,sml,tid) values ({$_SESSION['id']},$num,{$ucheck['id']})");
}else{
	$_pm['mysql'] -> query("update ml set sml = sml + $num WHERE uid = {$_SESSION['id']} AND tid = {$ucheck['id']}");
}

if($arr[0] == 'ml'){
	$f = $_pm['mysql'] -> getOneRecord("SELECT uid,ml FROM player_ext WHERE uid = {$ucheck['id']}");
	if(empty($f)){
		$_pm['mysql'] -> query("REPLACE INTO player_ext(uid,bbshow,ml) VALUES ({$ucheck['id']},5,{$num})");
	}else{
		$_pm['mysql'] -> query("UPDATE player_ext SET ml = ml + {$num} WHERE uid = {$ucheck['id']}");
	}
}
echo $num;
?>