<?php
require_once("../config/config.game.php");
if($_SERVER['REMOTE_ADDR'] != '171.216.222.197')
{
	//die();
}
header('Content-Type:text/html;charset=gbk');
$word = '';
if($_POST)
{
	foreach($_POST as $key =>$info)
	{
		$_POST[$key] = urldecode($info);
		$word .= "{$key}={$_POST[$key]}&";
		
		$_POST[$key] = iconv("utf-8",'gbk',$_POST[$key]);
		inject_check($_POST[$key]);
	}
}
log_result($word);

//����3�δ���Ͳ����ٴβ�����ÿ����������
$userInfo = userCheck($_POST['passport']);
$pb = $_pm['mysql']->getOneRecord("SELECT * FROM PasswordProtection WHERE player_id = '{$userInfo['id']}'");
$days = date('z');
if($pb){
	if($days == $pb['startTime']){//ͬһ��
		if($pb['count'] >= 3){
			die("�ܱ���ÿ��ֻ�ܳ���3�Σ�");
		}
		
	}else{//��ͬ�죬��ʼ��һ��
		$_pm['mysql']->query("UPDATE PasswordProtection SET startTime = '".$days."',count=0 WHERE player_id = '{$userInfo['id']}'");
	}
}




if($_POST['passport'] && !isset($_POST['an']) && !isset($_POST['qu']) && !isset($_POST['anS']) )
{
	$passport = $_POST['passport'];
	$user = userCheck($passport);
	$mb = $_pm['mysql']->getOneRecord("SELECT * FROM PasswordProtection WHERE player_id = '{$user['id']}'");
	if(!$mb)
	{
		die("OK1");
	}
	else
	{
		die("OK2|".$mb['question']);
	}
	
}
else if($_POST['passport'] && isset($_POST['an']) && isset($_POST['qu']) && isset($_POST['pass']) )
{
	$passport = $_POST['passport'];
	$an = $_POST['an'];
	$qu = $_POST['qu'];
	$pass = $_POST['pass'];
	$user = userCheck($passport);
	if(md5($pass) != $user['secret'])
	{
		$_pm['mysql']->query("UPDATE PasswordProtection SET count=count+1 WHERE player_id = '{$user['id']}'");
		die("�������");
	}
	$mb = $_pm['mysql']->getOneRecord("SELECT * FROM PasswordProtection WHERE player_id = '{$user['id']}'");
	if(!$mb)
	{
		$_pm['mysql']->query(" INSERT INTO PasswordProtection SET player_id = '{$user['id']}',question = '{$qu}',answer='{$an}'");
		die("OK���óɹ�");
	}
	else
	{
		die("���û��Ѿ�����");
	}
}
else if($_POST['passport'] && $_POST['anS'] && !isset($_POST['newPass']))
{
	$passport = $_POST['passport'];
	$anS = $_POST['anS'];
	$user = userCheck($passport);
	
	$mb = $_pm['mysql']->getOneRecord("SELECT * FROM PasswordProtection WHERE player_id = '{$user['id']}'");
	if(!$mb)
	{
		die("���û�δ�����ܱ�");
	}
	if($mb['answer'] == $anS)
	{
		die("OK");
	}
	else
	{
		$_pm['mysql']->query("UPDATE PasswordProtection SET count=count+1 WHERE player_id = '{$user['id']}'");
		die("�ܱ��𰸲���ȷ");
	}
	
}
else if($_POST['passport'] && $_POST['anS'] && isset($_POST['newPass']))
{
	$passport = $_POST['passport'];
	$anS = $_POST['anS'];
	$newPass = $_POST['newPass'];
	$user = userCheck($passport);
	
	
	$mb = $_pm['mysql']->getOneRecord("SELECT * FROM PasswordProtection WHERE player_id = '{$user['id']}'");
	if(!$mb)
	{
		die("���û�δ�����ܱ�");
	}
	if($mb['answer'] == $anS)
	{
		$_pm['mysql']->query("UPDATE player SET secret = '".md5($newPass)."' WHERE id = '{$user['id']}'");
		die("OK");
	}
	else
	{
		$_pm['mysql']->query("UPDATE PasswordProtection SET count=count+1 WHERE player_id = '{$user['id']}'");
		die("�ܱ��𰸲���ȷ");
	}
}

/**��־��Ϣ,��֧�������صĲ�����¼����
 * ��ע��������Ƿ�ͨfopen����
 */
function  log_result($word) {
	return true;
    $fp = fopen(date("Ymd").".txt","a");
    flock($fp, LOCK_EX) ;
	$time = date("Y-m-d H:i:s",time());
    fwrite($fp,"time:{$time}argv:".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

function userCheck($passport)
{
	global $_pm;
	$user  =  $_pm['mysql']->getOneRecord("SELECT id,secret FROM player WHERE name = '{$passport}'");
	if(!$user)
	{
		die("�޴��û�");
	}
	return $user;
}
function inject_check($Sql_Str) {//�Զ�����Sql��ע����䡣
    $check=preg_match('/select|insert|\ |update|UPDATE|delete|DELETE|\'|\\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i',$Sql_Str);
    if ($check) {
        die("�зǷ��ַ�");
    }else{
        return $Sql_Str;
    }
}
?>