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
$db = $_pm['mysql'];
$u	= $_pm['user'];
secStart($m);
$user	= $u->getUserById($_SESSION['id']);
$bags    = $u->getUserBagById($_SESSION['id']);
$props = unserialize($m->get(MEM_PROPS_KEY));
$taskword= taskcheck($user['task'],3);
if($user===FALSE)
{
	die("��Ϣ����");
}

require_once(dirname(__FILE__).'/51_check_pay.php');

if(isset($_GET['num'])&&intval($_GET['num'])>0){	
	$price = intval($_GET['num']);
	$paytype = intval($_GET['paytype']);
	if($paytype!==0&&$paytype!==1)
	{
		die('alert("֧����ʽ����")');
	}
	if($paytype==1)
	{
		$houzhui = '_bank';
	}else{
		$houzhui = '_51coin';
	}
	$houzhui = "";
	if($price>5000)
	{
		die('alert("ÿ����๺��5000��Ԫ����")');
	}
	$host = str_replace("PM51","",strtoupper(substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.'))));
	$msg = "�ڴ�����".$host."��Ԫ��".$price."����";
	/* ������Ϣ */
	
	$ordid=$host.substr("000000000000".$_SESSION['id'],-8).substr(time(),-9);
	
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
	$db->query("insert into yb set payname='51".($paytype==0?'��':'����')."����Ԫ��(User:".$_SESSION['username'].")',paytime='0',getyb=".$price.",orderid='".$ordid."',user_id=".$_SESSION['id']);
	//}
	$order = 
	array(
		'order_id'          => $ordid,
		'order_price'       => $paytype==0?$price/10:$price*10,
		'order_num'         => $price,
		'pay_shipping'      => 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod'.$houzhui.'.php',
		'order_msg'=>$msg,
		'order_callback_url'=> 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod'.$houzhui.'.php',//?order_code=0
		'order_check_url'   => 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod'.$houzhui.'.php', // ֧���ɹ���Ķ���֪ͨ��ַ����� 200 �ֽڡ�
		'order_cancel_url'  => 'http://'.$_SERVER['HTTP_HOST'].'/function/success_Mod'.$houzhui.'.php?cancel=1'
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<script language="javascript" src="../javascript/prototype.js"></script>
<title></title>
<style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 3px;
	background-color: #033E00;
	margin-right: 0px;
	margin-bottom: 0px;
	font-size: 12px;
}
a {
	font-size: 12px;
}
-->
</style>
<script language="javascript">
function   killErrors(){  
	return   true;
}     
window.onerror =  killErrors; 

var paytype = 0;
function $(id){return document.getElementById(id);}
function buy(){
	var  price = parseInt($("num").value);
	$("num").value = price;
	if(price<1){alert("������Ҫ����һ��Ԫ����");return;}
	if(price>5000){alert("��๺��5000��Ԫ����");return;}

	//if(confirm("ȷ��Ҫ������")){
		$('buyform').action = "pay_Mod.php?num="+price+"&paytype="+paytype+"&rd="+Math.random();
		$("btnbuy").disabled = true;
		document.getElementById("buyform").submit();
	//}
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
		 $("pay"+i).style.backgroundColor = "#FFFFFF";
		 $("pay"+i).style.color = "#BF7D1A";
		 $("pay"+i).style.fontWeight = '';
	}
	obj.style.backgroundColor = "#FFFFFF";
	obj.style.color = "red";
	obj.style.fontWeight = 'bold';
}

</script>
<body onLoad="calc();">
<table width="778" height="311" border="0" cellpadding="0" cellspacing="0" bgcolor="F3EDC9">
  <tr>
    <td width="140" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/ui/shop/gm01.gif" width="140" height="149"></td>
      </tr>
      <tr>
        <td height="176" align="left" valign="top" background="../images/ui/shop/gm02.gif">
			<div style="width:95%;padding:3px;color:#7a4f27; line-height:1.7;font-size:12px;margin-left:3px;height:150px; overflow:auto;scrollbar-face-color:#DFDABC;scrollbar-highlight-color:#ffffff;scrollbar-3dlight-color:#DFDABC;scrollbar-shadow-color:#ffffff;scrollbar-darkshadow-color:#DFDABC;scrollbar-track-color:#DFDABC;scrollbar-arrow-color:#ffffff;">
				<?=$taskword?>
			</div>
		</td>
      </tr>
    </table></td>
    <td valign="top" >
		<div id="tag1" style="position:absolute;left:250px;top:15px;z-index:1; color:#F1EABA;font-weight:bold">
          <table width="100" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="12"><img src="../images/ui/shop/gm07.gif" width="12" height="25" /></td>
              <td align="right" valign="middle" background="../images/ui/shop/gm08.gif">&nbsp;</td>
              <td width="12"><img src="../images/ui/shop/gm06.gif" width="12" height="25" /></td>
            </tr>
          </table>
		</div>
		
		<div id="tag2" style="position:absolute;left:141px;top:15px;z-index:2;color:#F1EABA;font-weight:bold">
          <table width="100" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="12"><img src="../images/ui/shop/gm07.gif" width="12" height="25" /></td>
              <td align="left" valign="middle" background="../images/ui/shop/gm08.gif" onclick="window.parent.$('gw').src='./function/Shopsm_Mod.php?op=cmp';" style="cursor:pointer" >�����̵�</td>
              <td width="12"><img src="../images/ui/shop/gm06.gif" width="12" height="25" /></td>
            </tr>
          </table>
		</div>
		
		
		<div id="tag3" style="position:absolute;left:195px;top:15px;z-index:3; color:#F1EABA;font-weight:bold">
          <table width="100" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="12"><img src="../images/ui/shop/gm03.gif" width="12" height="25" /></td>
              <td align="center" valign="middle" background="../images/ui/shop/gm05.gif" style="color:#3F3706"><b>��ֵԪ��</b></td>
              <td width="12"><img src="../images/ui/shop/gm04.gif" width="12" height="25" /></td>
            </tr>
          </table>
	  </div>
	 
		
	  <div style="width:635px; height:290px; background-color:#DFD496; position:absolute; top: 38px; left: 143px;">
<div style="width:595px; height:275px; position:absolute; left: 10px;">
				<img src="../images/ui/shop/gm09.gif" /><br/>
			<div style='position:absolute; text-align:left; font-size:12px; line-height:1.5; width:593px; height:235px; color:#BF7D1A; border:1px solid #C6B764; background-color:#fff; top: 28px;'>
			
			<table width="594" height="139" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="height:24px; overflow:hidden">
    <span id="pay0" style=" font-weight:bold; color:red ;border:0px; background-color:#FFFFFF; width:120px; cursor:pointer; height:24px; font-size:12px ;padding:4px" 
    onClick="payType(0);$('buyform').action='http://apps.51.com/payment/pay.php';$('mtype').innerHTML='��51��';">ʹ��51��</span>
	
	<span id="pay1" style="border:0px;cursor:pointer;background-color:#ffffff; width:120px; height:21px; font-size:12px; padding:4px" 
    onClick="payType(1);$('buyform').action='http://apps.51.com/paybank.php';$('mtype').innerHTML='Ԫ�����';">ʹ����������</span>    </td>
  </tr>
  <tr>
    <td style="padding-left:4px;padding-top:6px;">
    ����<input type="text" id="num" value="10" size="4" maxlength="4" />��Ԫ��,��������<span id="51bi" style="color:#FF0000">10</span><span id="mtype">��51��</span>��    </td>
  </tr>
  <tr id="pt0">
    <td style="padding-left:4px;padding-top:6px;">
<form method="post" x="http://apps.51.com/sandbox_pay.php" action="http://apps.51.com/payment/pay.php" id="buyform" target="_blank">
 <input type="hidden" value="" name="51_pay" id="51_pay"/> 
 <input style="background-image:url(../images/ui/shop/gm13.gif);border:0px;width:39px;height:15px;color:#2F291D;cursor:hand;" type="button" value="��ֵ" onClick="buy()" id="btnbuy"/>
</form>    </td>
  </tr>
</table>
			
		    <table width="100%" height="83" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><span tag="span">&nbsp;&nbsp;�������֧�������������κ����⣬�뼰ʱ��ͷ���ϵ��<br />
&nbsp;&nbsp;�ͷ��绰��021-61631151<br />
                </span></td>
              </tr>
            </table>
		  </div>
		  </div>
		
		<!-- self -->
		<!-- self end-->
        <!-- left option.-->
        <!-- left option end.-->
        </div>
		</td>
  </tr>
</table>

<div style="position:absolute; left: 6px; top: 308px; width: 44px;">
	<input name="button" type="button" style="background-image:url(../images/ui/shop/gm13.gif);border:0px;width:39px;height:15px;color:#2F291D;cursor:hand;"  onclick="window.parent.$('gw').src='./function/City_Mod.php';" value="�뿪"/>
</div>

</body>