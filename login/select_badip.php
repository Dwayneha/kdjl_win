<?php
/*
 *��������get_real_ip
 *���ܣ���ȡ��ʵIP
*/
function get_real_ip(){
	$ip=false;

	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) { 
			array_unshift($ips, $ip); $ip = FALSE; 
		}
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

$adminIP = get_real_ip();	//��ȡʹ����Աip
if ( $adminIP != "125.69.81.43" )
{
	die("ֻ�ܹ�˾�ڲ�������");
}

if ($_GET['test'] )	//���ڲ��ԵĽӿ�
{
	require_once('../config/config.game.php');
	$uIP = "60.60.60.60";
	$untieban = 160;
	$_pm['mem']->set(array('k'=>'BAN_'.date('d').$uIP,'v'=>$untieban));
}

/*���ӿ�
*/
if ( $_GET['untieip'] )
{
	//echo "�ɹ�������ӿ�";
	require_once('../config/config.game.php');
	$uIP = $_GET['untieip'];
	echo "��Ҫ����IP��:".$uIP."<br>";
	$untieban = 1;
	$_pm['mem']->set(array('k'=>'BAN_'.date('d').$uIP,'v'=>$untieban));
	echo "�ѽ��";
	
}

/*��ѯ�ӿ�
*/
if ( $_POST['ip'] && !$_GET['untieip'] )
{
	require_once('../config/config.game.php');
	$uIP = $_POST['ip'];
	echo "��ѯ��IP��:".$uIP."<br>";
	$ban=unserialize($_pm['mem']->get('BAN_'.date('d').$uIP));
	
	if ( intval($ban) >= 150 )
	{
		echo "����150��,����<br>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ip�Ƿ񱻷��ѯ</title>
<script language="javascript">
function untie()
{
	location.href="select_badip.php?untieip=<?= $uIP ?>";
}

</script>
</head>
<body>

<input type="button" name="untie1" onclick="untie()" value="���" />
</body>
</html>

<?
	}
	else
	{
		echo "��IP���յ�½����:".$ban."<br>";
		echo "����";
	}
	//echo "<br>ok";
}
/*
 �����������������
*/
if ( !$_POST['ip'] && !$_GET['untieip'] )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ip�Ƿ񱻷��ѯ</title>
</head>
<body>
<p>�����ѯ��ip��</p>
<form name="form1" method="post" action="">
<input  type="text"  name="ip" size="20"/>
<input type="submit"  value="��ѯ"/>
</form>

</body>
</html>
<?
}
?>