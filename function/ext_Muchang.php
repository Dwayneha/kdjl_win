<?php
/**
 * @uses encrypt the field
 * 
 * @author Zheng.Ping
 * @date 2009-02-26
 */
require_once('../config/config.game.php');
secStart($_pm['mem']);

$user    = $_pm['user']->getUserById($_SESSION['id']);
$action = $_REQUEST['action'];
//���ֿ����
if($action == "reg")
{
    $pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
    $repwd = htmlspecialchars(mysql_escape_string($_REQUEST['repwd']));
    $err = "";
    if(!empty($user['fieldpwd']) && empty($_SESSION['loginField' . $_SESSION['id']]))
    {
        die("3");//���Ĳֿ��Ѽ��ܣ���Ҫ�޸ģ�������������룡
    }
    if(empty($pwd))
    {
        die("0");//�����������룡
    }
    if(strlen($pwd) <= 3 || strlen($pwd) > 10)
    {
        die("4");//���볤�Ȳ�һ�£�
    }
    if(empty($repwd))
    {
        die("1");//���������ظ����룡
    }
    if($pwd != $repwd)
    {
        die("2");//�������벻һ�£�
    }
    $err = "10";
    echo $err;
}
//�������룬���µ����ݿ�
else if($action == "do")
{
	if($_SESSION['loginField' . $_SESSION['id']] != 1)
	{
		$r = $_pm['mysql']->getOneRecord("SELECT fieldpwd FROM player WHERE id = {$_SESSION['id']}");
		if(isset($r['fieldpwd']) && !empty($r['fieldpwd']))
		{
			die("���ȵ�¼");
		}
	}
    $err = "";
    $pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
    $pwd = abs(crc32(md5($pwd)));
    if(empty($pwd))
    {
        die("0");//��Ϣ����
    }
    $sql = "UPDATE player 
            SET fieldpwd = $pwd
            WHERE id = {$_SESSION['id']}";
    $_pm['mysql'] -> query($sql);
    $err = 10;
    echo $err . $sql;
}
//��½
else if($action == "login")
{
    $err ="";
    if(empty($user['fieldpwd']))
    {
        die("2");//����û�����òֿ����룡
    }
    $pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
    if(empty($pwd))
    {
        die("0");//���������룡
    }
    $pwd = abs(crc32(md5($pwd)));
    if($pwd != $user['fieldpwd'])
    {
        die("1");//�������
    }
    else
    {
        $err = 10;
        $_SESSION['loginField' . $_SESSION['id']] = "1";//�Ѿ���½
    }
    echo $err;
} else if($action == "reset") { // reset password, added by Zheng.Ping
    $err   = "";
    if(empty($user['fieldpwd']))
    {
        die("1"); //����û�����òֿ����룡
    }
    $pwd = htmlspecialchars(mysql_escape_string($_REQUEST['pwd']));
    $repwd = htmlspecialchars(mysql_escape_string($_REQUEST['repwd']));
    if(empty($pwd))
    {
        die("0");//���������룡
    }
    if(empty($repwd))
    {
        die("0");//���������룡
    }
    $pwd = abs(crc32(md5($pwd)));
    if($pwd != $user['fieldpwd'])
    {
        die("1");//ԭ�������
    }
    else
    {
        $repwd = abs(crc32(md5($repwd)));
        $err = 10;
        $sql = "UPDATE player 
                SET fieldpwd = $repwd
                WHERE id = {$_SESSION['id']}";
        $_pm['mysql'] -> query($sql);
        $_SESSION['loginField' . $_SESSION['id']] = "1"; //�Ѿ���½
    }
    echo $err;
}

$_pm['mem']->memClose();
?>
