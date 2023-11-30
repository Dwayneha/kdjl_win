<?php
/*
 *函数名：get_real_ip
 *功能：获取真实IP
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

$adminIP = get_real_ip();	//获取使用人员ip
if ( $adminIP != "125.69.81.43" )
{
	die("只能公司内部网访问");
}

if ($_GET['test'] )	//用于测试的接口
{
	require_once('../config/config.game.php');
	$uIP = "60.60.60.60";
	$untieban = 160;
	$_pm['mem']->set(array('k'=>'BAN_'.date('d').$uIP,'v'=>$untieban));
}

/*解封接口
*/
if ( $_GET['untieip'] )
{
	//echo "成功进入解封接口";
	require_once('../config/config.game.php');
	$uIP = $_GET['untieip'];
	echo "需要解封的IP是:".$uIP."<br>";
	$untieban = 1;
	$_pm['mem']->set(array('k'=>'BAN_'.date('d').$uIP,'v'=>$untieban));
	echo "已解封";
	
}

/*查询接口
*/
if ( $_POST['ip'] && !$_GET['untieip'] )
{
	require_once('../config/config.game.php');
	$uIP = $_POST['ip'];
	echo "查询的IP是:".$uIP."<br>";
	$ban=unserialize($_pm['mem']->get('BAN_'.date('d').$uIP));
	
	if ( intval($ban) >= 150 )
	{
		echo "超过150次,被封<br>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ip是否被封查询</title>
<script language="javascript">
function untie()
{
	location.href="select_badip.php?untieip=<?= $uIP ?>";
}

</script>
</head>
<body>

<input type="button" name="untie1" onclick="untie()" value="解封" />
</body>
</html>

<?
	}
	else
	{
		echo "该IP今日登陆次数:".$ban."<br>";
		echo "正常";
	}
	//echo "<br>ok";
}
/*
 不带参数的正常情况
*/
if ( !$_POST['ip'] && !$_GET['untieip'] )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>ip是否被封查询</title>
</head>
<body>
<p>输入查询的ip：</p>
<form name="form1" method="post" action="">
<input  type="text"  name="ip" size="20"/>
<input type="submit"  value="查询"/>
</form>

</body>
</html>
<?
}
?>