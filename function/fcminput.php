<?php 
session_start();
header('Content-Type:text/html;charset=gbk');

//----------------------------------------------------------
//loginGate.php��serverGate.php,fcminput.php;���涼���������,����һ��
$fcmflag=false;
$partnerDomain=strtolower(substr($_SERVER['HTTP_HOST'],strpos($_SERVER['HTTP_HOST'],'.')+1));
if(
	$partnerDomain=='webgame.com.cn'||
	$partnerDomain=='qq496.cn'||
	$partnerDomain=='my4399.com'
){
	$fcmflag=true;
}
switch($partnerDomain)
{
	case 'webgame.com.cn':
		$fcmSysPath='';
		break;
	case 'qq496.cn':
	case 'my4399.com':
		$fcmSysPath='4399/';
		break;
	default:
		$fcmSysPath='';
		break;
}
if($_SERVER['HTTP_HOST']=='pmtest.webgame.com.cn') $fcmSysPath='4399/';//���Ե�ʱ��,pmtest����4399�Ĵ���
//----------------------------------------------------------

$key='*)(OJI(*77786*(**(8';
		
$urlFCMGame='http://61.160.192.12/'.$fcmSysPath.'queryId.php?username='.$_SESSION['username'].'&card_no='.$_REQUEST['card_no'].'&host='.$_SERVER['HTTP_HOST'].'&sn='.md5($_SERVER['HTTP_HOST'].$_SESSION['username'].date("Ymd").$key.$_REQUEST['card_no']);
function curlSN($url,$port=80){
	$post = 1;
	$returntransfer = 1;
	$header = 0;
	$nobody = 0;
	$followlocation = 1;
	
	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
						CURLOPT_HEADER => $header,
						CURLOPT_NOBODY => $nobody,
						CURLOPT_PORT => $port,
						CURLOPT_POST => $post,
						CURLOPT_POSTFIELDS => $request,
						CURLOPT_RETURNTRANSFER => $returntransfer,
						CURLOPT_FOLLOWLOCATION => $followlocation,
						CURLOPT_COOKIEJAR => $cookie_jar,
						CURLOPT_COOKIEFILE => $cookie_jar,
						CURLOPT_REFERER => $url
						);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
$rs=curlSN($urlFCMGame);

if($rs!='OK')
{
	die('<script language="javascript">alert("����ʧ�ܣ����������Ϣ����ȷ('.$rs.')��");window.close();</script>');
}else{
	die('<script language="javascript">alert("�����ɹ�����ر���������µ�½�������������Ϣ��Ч��");window.close();</script>');
}
?>