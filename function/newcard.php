<?php
header('Content-Type:text/html;charset=GB2312');
require_once('../config/config.game.php');
//secStart($_pm['mem']);

$user	 = $_pm['user']->getUserById($_SESSION['id']);
$bag	= $_pm['user']->getUserBagById($_SESSION['id']);
function socketData($host,$url,$flag=false){
	$fp = @fsockopen($host, 80, $errno, $errstr, 30);
	if (!$fp) {
		return false;
	} else {
		$out = "GET /".$url." HTTP/1.1\r\n";
		$out .= "Host: ".$host."\r\n";
		$out .= "Connection: Close\r\n\r\n";
	
		fwrite($fp, $out);
		$rtn = "";
		while (!feof($fp)) {
			$rtn.= fgets($fp, 128);
		}
		fclose($fp);
	}	
	$rtn=explode("\r\n\r\n",$rtn,2);
	return $rtn[1];
}

$apiDomain='card.webgame.com.cn';
$apiFile='/api.php';
$key='&)67&*(&*()sdadfJ';
//http://�ӿڵ�ַ?server=����&card=����&pass=����&account=ͨ��֤�˺�&role=��ɫId&time=����ʱ��&sign=md5ǩ��
$domain=$_SERVER['HTTP_HOST'];
//$account=$_REQUEST['username'];
$account=$_SESSION['username'];
$role=$_SESSION['id'];
$time=time();
$code = $_GET['id'];
$regcheck = $_pm['mysql'] -> getOneRecord("SELECT id FROM player WHERE name = '{$_SESSION['username']}' AND password != '00000000000000000000000000000000'");
if(!empty($regcheck['id'])){
	//����ʹ�ù��Ῠ
	$cflag = md5($_GET['cardid'].$_GET['pwd'].$key);
	$checkurl = '/apit.php?server='.$domain.'&card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&sign='.$cflag;
	$res = curl('http://'.$apiDomain.$checkurl);//echo 'http://'.$apiDomain.$checkurl;exit;
	$y=explode("|",$res,2);
	if($y[0]!=='10'){
		die($y[1]);
	}else{
		if($y[1] == 3){
			die('��ʹ�õĿ����Ͳ���ȷ��');
		}
	}
}else{
	//ֻ��ʹ�ù��Ῠ
	$cflag = md5($_GET['cardid'].$_GET['pwd'].$key);
	$checkurl = '/apit.php?server='.$domain.'&card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&sign='.$cflag;
	//echo $checkurl;exit;
	$res = curl('http://'.$apiDomain.$checkurl);
	$y=explode("|",$res,2);
	if($y[0]!=='10'){
		die($y[1]);
	}else{
		if($y[1] != 3){
			die('�˿���Ҫע���ʹ�ã�');
		}
	}
	$_SESSION['ghflag'] = 1;
}
if(isset($_REQUEST['cardid'])){
	if($role <= 0){
		$role = 123;
	}
	$sign=md5($domain.$_REQUEST['cardid'].$_REQUEST['pwd'].$account.$role.$time.$key);
	$url=$apiFile.'?server='.$domain.'&card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&account='.$account.'&role='.$role.'&time='.$time.'&sign='.$sign;
	if($code != '0'){
		$url .= '&code='.$code;
	}
	//echo $apiDomain.$url;exit;
	//echo '<br>����ֵ��=><br>'.socketData($apiDomain,$url).'<br>';
	//$res = socketData($apiDomain,$url);echo __FILE__.":".__LINE__."<br>";echo $res;exit;
	//echo 'http://'.$apiDomain.$url;exit;
	$res = curl('http://'.$apiDomain.$url);
	$x=explode("|",$res,2);
	if($x[0]!=='10')
	die($x[1]);//ʧ��
	$str = '';
	if($_SESSION['ghflag'] != 1){
		$numarr = explode("\r\n",$x[1]);
		$arr = explode(',',$numarr[0]);
		
		$task = new task();
		if(is_array($arr)){
			foreach($arr as $v){
				$inarr = explode(':',$v);
				if(count($inarr) != 2){
					continue;
				}
				$givecheck = $task->saveGetPropsMore($inarr[0],$inarr[1]);
				if($givecheck === true){
					$parr = $_pm['mysql'] -> getOneRecord("SELECT name FROM props WHERE id = {$inarr[0]}");
					$str .= '�����Ʒ��'.$parr['name'].'&nbsp;'.$inarr[1].' ����';
				}else{
					$checkflag = '���ڱ������ˣ������½����������';
				}
			}
		}
		$pnote = 'card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&Ӧ�ý�����'.$numarr[0].'----ʵ�ʣ�'.$str.'-----'.$checkflag;
		$_pm['mysql'] -> query("insert into gamelog (ptime,seller,buyer,pnote,vary) values (".time().",{$_SESSION['id']},{$_SESSION['id']},'$pnote',91)");
		if(!empty($str)){
			 $str=substr($str,0,-2);
		}
		if(isset($checkflag) && !empty($checkflag)){
			$str .= ','.$checkflag;
		}
	}else{
		$_SESSION['ghpstr'] = $x[1];
	}
	if(empty($str)){
		$str = '������˼����������̫��ù�ˣ�û�еõ��κν�����';
	}//echo $_SERVER['HTTP_REFERER']
	echo $str;
}
?>