<?php
session_start();
if(!isset($_SESSION['username'])||strlen($_SESSION['username'])<3){
	echo '<script language="javascript">alert("���ȵ�½!");window.location="http://www.51.com";</script>';exit();
}
if(isset($_SESSION['51_session'])){
	foreach($_SESSION['51_session'] as $k=>$v){	
		$_POST[$k]=$v;	
	}
}else{
	echo '<script language="javascript">alert("��½ʧЧ!");window.location="http://www.51.com";</script>';exit();
}
/*if($_SESSION['username'] != 'xlj1983cn' && $_SESSION['username'] != 'zengtest16' && $_SESSION['username'] != 'antoni01' && $_SESSION['username'] != 'jianyaoli' && $_SESSION['username'] != 'tanwei200763' && $_SESSION['username'] != 'daniel1983cn')
{
	die("֧����ʱ�رգ�");
}*/
//$_POST['51_sig_time'] += 40000;//echo $_POST['51_sig_time']."<br />".time();exit;



require_once('../config/config.game.php');
include_once("../sdk/appinclude.php");
$m	= $_pm['mem'];
$db = &$_pm['mysql'];
$u	= $_pm['user'];
secStart($m);
$user	= $u->getUserById($_SESSION['id']);
$bags    = $u->getUserBagById($_SESSION['id']);
$props = unserialize($m->get(MEM_PROPS_KEY));
if($user===FALSE)
{
	die("��Ϣ����");
}

if(isset($_GET['num'])&&intval($_GET['num'])>0){	
	$price = intval($_GET['num']);
	$paytype = intval($_GET['paytype']);
	if($paytype!==0&&$paytype!==1)
	{
		die('alert("֧����ʽ����")');
	}
	if($price>5000)
	{
		die('alert("ÿ����๺��5000��Ԫ����")');
	}
	$host = str_replace("PM51","",strtoupper(substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.'))));
	$msg = "�ڴ�����".$host."��Ԫ��".$price."����";
	/* ������Ϣ */
	$ordid=$_SESSION['id'].date("mdHis");
	$_SESSION['buyyb_info'][$ordid]=array($price,$paytype);
	//if($paytype==1){
	$db->query("CREATE TABLE if not exists `yb` (
	  `Id` int(11) NOT NULL auto_increment,
	  `payname` varchar(60) default '0',
	  `paytime` varchar(14) default '0',
	  `getyb` int(11) unsigned default '0',
	  `orderid` varchar(25) default '0',
	  `sn_platform` varchar(25) default '',
	  `user_id` int(11) default '0',
	  PRIMARY KEY  (`Id`)
	) TYPE=MyISAM; 
	");
	$db->query("insert into yb set payname='51".($paytype==0?'��':'����')."����Ԫ��(User:".$_SESSION['name'].")',paytime='0',getyb=".$price.",orderid='".$ordid."',user_id=".$_SESSION['id']);
	//}
	$order = 
	array(
		//'environment'=>"production",
		//'version'=>"2.0",
		//'user'=>$_SESSION['name'],
		'order_id'          =>$ordid,
		'order_price'       =>$paytype==0?$price/10:$price*10,
		'order_num'         =>$price,
		'pay_sandbox'       =>1,
		'pay_code'          =>1,
		'pay_shipping'      =>'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod_yb.php',
		'order_msg'=>$msg,
		'order_callback_url'=> 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod_yb.php',//?order_code=0
		'order_check_url'   => 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod_yb.php', // ֧���ɹ���Ķ���֪ͨ��ַ����� 200 �ֽڡ�
		'order_cancel_url'  => 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod_yb.php?cancel=1'
	);
	
	//print_r($order);exit;
	/* ��create_post_stringǩ��������� */
	$OpenApp_51->api_client->set_encoding("GBK");
	$post = $OpenApp_51->api_client->create_post_string('51_pay', $order); // ��ע��$post�ַ������Ȳ�Ҫ����1024
	echo 
	'
	<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
	<span style="font-size:12px">��ת�У����Եȣ�����ˢ��ҳ�桭</span>
	<form method="post" id="buyform" action="'.($paytype==0?'http://apps.51.com/payment/pay.php':'http://apps.51.com/paybank.php').'">
	 <input type="hidden" value="'.str_replace(array('"',"\r","\n"),"",$post).'" name="51_pay" id="51_pay"/> 	 
	</form> 
	<script language="javascript">	
		  document.getElementById("buyform").submit();
	</script>
	';
	die();
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<script language="javascript">
var paytype = 0;
function $(id){return document.getElementById(id);}
function buy(){
	var  price = parseInt($("num").value);
	$("num").value = price;
	if(price<1){alert("������Ҫ����һ��Ԫ����");return;}
	if(price>5000){alert("��๺��5000��Ԫ����");return;}

	if(confirm("ȷ��Ҫ������")){
		$('buyform').action = "pay_Mod_yb.php?num="+price+"&paytype="+paytype+"&rd="+Math.random();
		$("btnbuy").disabled = true;
		document.getElementById("buyform").submit();
	}
}
function calc(){
	var  price = parseInt($("num").value);
	$('51bi').innerHTML = price/10;
	setTimeout("calc()",100)
}
function payType(id){
	paytype = id;
	var obj = $("pay"+id);
	for(var i=0;i<2;i++)
	{
		 $("pay"+i).style.backgroundColor = "#c0c0c0";
		 $("pay"+i).style.color = "#000000";
		 $("pay"+i).style.fontWeight = '';
	}
	obj.style.backgroundColor = "#006600";
	obj.style.color = "#ffffff";
	obj.style.fontWeight = 'bold';
}
</script>
<style>
td,div,body{
font-size:12px;}
</style>
<body onLoad="calc();" leftmargin="40" topmargin="40" bgcolor="#FFFFFF">
<table width="400" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="height:24px; overflow:hidden">
    <span id="pay0" style=" font-weight:bold; color:#ffffff ;border:1px solid #006633; background-color:#006600; width:120px; cursor:pointer; height:24px; font-size:12px ;padding:4px" 
    onClick="payType(0);$('buyform').action='http://apps.51.com/payment/pay.php';$('mtype').innerHTML='��51��';">ʹ��51��</span><span id="pay1" style="border:1px solid #006633;  cursor:pointer;background-color:#c0c0c0; width:120px; height:21px; font-size:12px; padding:4px" 
    onClick="payType(1);$('buyform').action='http://apps.51.com/paybank.php';$('mtype').innerHTML='Ԫ�����';">ʹ����������</span>
    </td>
  </tr>
  <tr>
    <td style="padding-left:4px;padding-top:6px; background-color:#006600">
    ����<input type="text" id="num" value="1" size="4" maxlength="4" />��Ԫ��,��������<span id="51bi" style="color:#FF0000">10</span><span id="mtype">��51��</span>��
    </td>
  </tr>
  <tr id="pt0">
    <td style="padding-left:4px;padding-top:6px; background-color:#006600">
<form method="post" x="http://apps.51.com/sandbox_pay.php" action="http://apps.51.com/payment/pay.php" id="buyform" target="_blank">
 <input type="hidden" value="" name="51_pay" id="51_pay"/> 
 <input type="button" value="ȷ������" onClick="buy()" id="btnbuy"/>
</form>    
    </td>
  </tr>

</table>



</body>