<?php 
require_once('../config/config.game.php');
if(!isset($db)){
	$db = &$_pm['mysql'];
}
if(!isset($app_key))
{
	$app_key = '39d4d8d96e3d64d98f4e5eebc9ab890a'; //输入应用公钥
	$secret_key = '4220a08009eff4115151728a885a44e9'; //输入应用私钥
}
$db->query('alter table yb  add confirmed char(1) NULL default "0",
							add confirm_time int(10) NULL default 0,
							add confirm_result varchar(10) NULL default ""
							;');
$sql = '
					select 
						date_format(from_unixtime(yb.paytime),"%Y-%m-%d %H:%i:%s") ptime,
						yb.getyb,yb.orderid ,player.name,yb.payname,yb.Id yid,yb.sn_platform
					from 
						yb,player
					where 
						yb.user_id=player.id and
						date_format(from_unixtime(yb.paytime),"%Y%m%d")="'.date("Ymd").'"
						and paytime>0 and confirmed="0" and user_id='.$_SESSION['id'].'
					';
$tl = $_pm['mysql'] -> getRecords($sql);
if(count($tl)>0){
	$webgameCanceledOrderId = array();	
	foreach($tl as $rs){
		if(strpos($rs['payname'],'51币')!==false||$rs['sn_platform']=='51CoinPay'){
			$order_id = $rs['orderid'];
			$res = check_order($app_key, $secret_key, $order_id, 'xml');
			if ($res->error==0 && $res->success==1) {
				//echo __FILE__.'->'.__LINE__."<hr>";
	
				$sql = 'update yb set confirmed="1",confirm_time=unix_timestamp(),confirm_result="OK" where id='.$rs['yid'];
				$db->query($sql);
			} elseif ($res->error==0 && $res->success==0) {
				//echo __FILE__.'->'.__LINE__."<hr>";
				$sql = 'update yb set confirmed="1",confirm_time=unix_timestamp(),confirm_result="FAILED" where id='.$rs['yid'];
				$db->query($sql);
				$_pm['mysql']->query("START TRANSACTION");
				$_pm['mysql']->query("SET AUTOCOMMIT = 0");
				$user = $db->getOneRecord('select yb from player where id='.$_SESSION['id']);
				if($user)
				{
					$userLeftYb = $user['yb']-$rs['getyb'];
					if($userLeftYb>=0)
					{
						$sqlLog = '
							insert 
								into 
							gamelog
								(ptime,	seller,	buyer,	pnote,	vary) 
							values
								(unix_timestamp(),"'.$_SESSION['username'].'","51CoinCheckError","回收用户['. $rs['getyb'].']个元宝,用户现有['.$user['yb'].']个元宝，扣除后剩余['.$userLeftYb.']个元宝。",14)
						';
						$db->query($sqlLog);
					}
					else
					{
						$sqlLog = '
							insert 
								into 
							gamelog
								(ptime,	seller,	buyer,	pnote,	vary) 
							values
								(unix_timestamp(),"'.$_SESSION['username'].'","51CoinCheckError","回收用户['. $rs['getyb'].']个元宝,用户现有['.$user['yb'].']个元宝，扣除后剩余0个元宝，还有['.abs($userLeftYb).']个元宝未找回。",14)
						';
						$db->query($sqlLog);
						$userLeftYb = 0;
					}
					echo mysql_error();
					$sqlUU = 'update player set yb='.abs(intval($userLeftYb)).' where id='.$_SESSION['id'].' limit 1';
					$db->query($sqlUU);
				}
				if($_SESSION['username']=="leinchu"){
					//echo $sqlLog;
				}
				$_pm['mysql']->query("COMMIT");
				$webgameCanceledOrderId[]=$order_id;
				//	echo '$order_id='.$order_id.'<hr>';
			} else {
				//echo __FILE__.'->'.__LINE__."<hr>";
			}
		}
	}
}
function check_order($app_key, $secret_key, $order_id, $format='xml', $connect_timeout=2, $timeout=5) {
	$sig = md5($app_key.$order_id.$secret_key);
//    echo 'check url:<br />';
	$url = "http://apps.51.com/payment/check.php?app_key={$app_key}&order_id={$order_id}&sig={$sig}&format={$format}";
//    echo $url;
//    echo "\n";

	if (function_exists('curl_init')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$response = curl_exec($ch);
		curl_close($ch);
	} else {
		$default = ini_get('default_socket_timeout');
		ini_set('default_socket_timeout', $connect_timeout+$timeout);
		$response = @file_get_contents($url);
		ini_set('default_socket_timeout', $default);
	}

	$res = $format=='json' ? @json_decode($response) : @simplexml_load_string($response);


	if (!is_object($res)) {
		$res = new stdClass;
		$res->error = 1;
		$res->errormsg = strlen($response) ? $response : 'timeout';
	} else {
		$res->error = 0;
	}
	return $res;
}
?>