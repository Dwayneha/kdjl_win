<?php
/**
* user name. Check.
*/
require_once("../config/config.game.php");
//secStart($_pm['mem']);
header('Content-Type:text/html;charset=gbk');
@session_start();
$_user = strlen(iconv('utf-8','gbk',$_GET['n']))==0?$_GET['n']:iconv('utf-8','gbk',$_GET['n']);
$db = new mysql();
$pssport = mysql_real_escape_string(iconv('utf-8','gbk',$_POST['username']));
//$pssport = mysql_real_escape_string($_POST['username']);
$rs = $db->getOneRecord("SELECT id,name,nickname,password,secret,from_type,heart_time FROM player WHERE secret = '".md5($_POST['password'])."' AND name= '".$pssport."'");
if (is_array($rs))
{
	if($_REQUEST['from'] == 1)
	{
		$db->query("UPDATE player SET from_type = 1 WHERE id =  '{$rs['id']}'");
		if($rs['from_type'] == 1)
		{
			$botTime = time()-$rs['heart_time'];
			$botTime = $botTime>60?$botTime:0;
			$db->query("UPDATE player SET bot_time = {$botTime} WHERE id =  '{$rs['id']}'");
		}
	}
	else
	{
		$db->query("UPDATE player SET from_type = 0 WHERE id =  '{$rs['id']}'");
	}
	$user = $rs;
	$_SESSION['username'] = 	$rs['name'];	
	$_SESSION['nickname'] = $rs['nickname'];
	$_SESSION['name'] = 	$rs['name'];
        $_SESSION['mac']=$_POST['mac_addr'];	
	$_SESSION['id'] = $rs['id'];
	$_SESSION['LoginApiState'] = 1;
	$_SESSION['game_server_flag'] = GAME_SERVER_FLAG;
	if(empty($rs['password'])){
		$_SESSION['lock_time'] = 0;
	}else{
		$_SESSION['lock_time'] = $rs['password'];
	}
        //��ȡ�����id�Ź�����ʹ��
	$sql = "select member_id,guild_id from guild_members where member_id='{$rs['id']}'";
	$guild = $db->getOneRecord($sql);
	if($guild){
		$_SESSION['guild_id'] = $guild['guild_id'];
	}else{
		$_SESSION['guild_id'] = 0;
	}
	if($_REQUEST['from'] == 1)
	{
		$sql = "select * from chat_login_auth where sid='".session_id()."'";
		$rs =  $db->getOneRecord($sql);
		if($rs)
		{
			$sql = "DELETE FROM chat_login_auth WHERE sid='".session_id()."'";
			$db->getOneRecord($sql);
		}
		$sql = "DELETE FROM chat_login_auth WHERE username = '{$_SESSION['username']}'";
		$db->getOneRecord($sql);
		$sql = "INSERT INTO chat_login_auth SET uid = '{$_SESSION['id']}',username='{$_SESSION['username']}',nickname='{$user['nickname']}',sid='".session_id()."',mac_addr='{$_SESSION['mac']}'";
		$db->getOneRecord($sql);
  //MAC ��ֹ�ж�
               require("../ipAdmin/ipm.php");
		echo 'SID'.session_id();
  //MAC ��ֹ�ж�
//               require("../ipAdmin/ipm.php");
	}
	else
	{
		echo "<script>window.location='../login/login.php'</script>";
	}
}
else
{
	echo "<script>window.location='login.php'</script>";
}
?>
