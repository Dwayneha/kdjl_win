<?php
require_once "sdk/appinclude.php";
require_once"config/config.game.php";
if($_SESSION['username']=="leinchu"){
	echo $_SERVER['HTTP_REFERER'];
}
/*===================================================== 
 Copyright (C) Stone��hykwolf��ʧ��ĳ���<stone@webgame.com.cn>
 Modify : Stone
 URL : http://www.webgame.com.cn
===================================================== */
###### Fix PHPSESSID ###########

######ƽ̨���ܣ����ܽӿں�����
  require_once("login/lib/passport.php");

######ƽ̨�ӿ�ͨ�ýӿں�����
  require_once("login/lib/nusoap.php");
 
########ƽ̨����ϷԼ������Կ��
  $key="7sl+kb9adDAc7gLuv31MeEFPBMJZdRZyAx9eEmXSTui4423hgGfXF1pyM"; 

########ƽ̨������֤����ִ�######
###### -1:�Ƿ����� -2:ͨ��֤������#### 
###### -999:û��ͨ��֤ 10:��֤ͨ��####

try
{
	$curruser = $OpenApp_51->api_client->users_getLoggedInUser();
}
catch (Exception $e)
{
	echo $e->getMessage();
	exit;
}
$user = $OpenApp_51->require_login();
$r_username = $curruser[0]["uid"];
$txt = $r_username.",".time().",10";
$str = "";
foreach($_GET as $k=>$v){
	$str .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
}
$txt .= ','.$str;
$_51sessionStr = $str;
$data = passport_encrypt($txt,$key);


if($r_username == "tanwei200763"||$r_username == "wf95686140"||$r_username == "mayier318" || $r_username == 'akg4rzxy' || $r_username == 'stoneh2' || $r_username == 'mesopodamia')
{
	$href = "javascript:goto('http://pm519.webgame.com.cn/login/login.php');";
}
else
{
	$href = "#";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>51ѡ��ҳ��</title>
</head>
<style type="text/css">
body{
margin:0px;
padding:0px;
font:Arial, Helvetica, sans-serif;
font-size:12px;
}
img{
border:0px;
}
.service a{
         font-size:12px;
		 font-weight:bold;
		 color:#000000;
		 text-decoration:none;
		 }
.service a:hover{
         font-size:12px;
		 font-weight:bold;
		 color:#ff0000;
		 text-decoration:none;
		 }
.STYLE1 {color: #33CC00}
.STYLE2 {color: #FF0000}
</style>
<body>
	<form method="post" id="form1" target="_blank">
	<?php echo $_51sessionStr; ?>
	</form>

<table width="680" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="680" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td background="../images/51/pm_02.jpg" width="463" height="46"></td>
        <td width="217"><img src="../images/51/pm_03.jpg" width="217" height="46" border="0" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td background="../images/51/pm_05.jpg" width="680" height="172"></td>
  </tr>
  <tr>
    <td>
	<script language="javascript">	
	function goto(url){
		var obj =document.getElementById('form1');
		obj.action = url;
		obj.submit();
	}
	</script>
	<table width="680" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td background="../images/51/pm_06.jpg" width="234" height="297">;</td>
        <td width="446" valign="top" background="../images/51/pm_07.jpg"><table width="446" height="97" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="67" rowspan="2"></td>
            <td width="379" height="48"></td>
          </tr>
          <tr>
            <td><table width="310" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="3" align="center">
                
                <table><tr>
                <td  width="146" height="31" align="center" background="../images/51/service.jpg" bgcolor="#FFFFFF" class="service"><a href="javascript:goto('http://pm518.webgame.com.cn/login/login.php');">����[Ծ��]<span class="STYLE2"> (��)<img src="new.gif" border="0"> </span></a>               
                </td>
                <td width="18" align="center" class="service">&nbsp;</td>
                 <td  width="146" height="31" align="center" background="../images/51/service.jpg" bgcolor="#FFFFFF" class="service"><a href="javascript:goto('http://pm519.webgame.com.cn/login/login.php');">����[ǧ��]<span class="STYLE2">(��)<img src="new.gif" border="0" /></span></a></td>
                </tr>
                </table>
				</td>
              </tr>
              <tr>
                <td width="146" height="10"></td>
                <td width="18"></td>
                <td width="146"></td>
              </tr>
              <tr>
                <td width="146" height="31" align="center" background="../images/51/service.jpg" class="service">
				
                <a href="javascript:goto('http://pm517.webgame.com.cn/login/login.php');">����[ֿ��]<span class="STYLE1"> (����) </span></a>
                </td>
                <td width="18" align="center" class="service">&nbsp;</td>
                <td width="146" align="center" class="service"></a></td>
              </tr>
			  
			  <tr>
                <td width="146" height="10"></td>
                <td width="18"></td>
			    <td width="146"></td>
			  </tr>
			  <tr>
                <td width="146" height="31" align="center" background="../images/51/service.jpg" class="service">
				
                <a href="javascript:goto('http://pm516.webgame.com.cn/login/login.php');">����[�绪]</span><span class="STYLE1"> (����)</span></a>
                </td>
                <td width="18" align="center" class="service">&nbsp;</td>
			    <td width="146" align="center" background="../images/51/service.jpg" class="service"><a href="javascript:goto('http://pm515.webgame.com.cn/login/login.php');">����[ǣ��]<span class="STYLE1"></span><span class="STYLE2"> (�Ƽ�)</span></a></td>
			  </tr>
			  <tr>
                <td width="146" height="10"></td>
                <td width="18"></td>
			    <td width="146"></td>
			  </tr>
			 
			  <tr>
                <td width="146" height="31" align="center" background="../images/51/service.jpg" bgcolor="#D6D6D6" class="service">
				<a href="javascript:goto('http://pm514.webgame.com.cn/login/login.php');">����[ҹѩ]<span class="STYLE1"> (����)</span></a></td>
                <td width="18" align="center" class="service">&nbsp;</td>
			    <td width="146" align="center" background="../images/51/service.jpg" class="service"><a href="javascript:goto('http://pm513.webgame.com.cn/login/login.php');">����[����]<span class="STYLE1"> (</span><span class="STYLE1">����</span><span class="STYLE1">)</span></a></td>
			  </tr>
			  <tr>
                <td width="146" height="10"></td>
                <td width="18"></td>
			    <td width="146"></td>
			  </tr>
			  
			   <tr>
                <td width="146" height="31" align="center" background="../images/51/service.jpg" class="service">
				<a href="javascript:goto('http://pm512.webgame.com.cn/login/login.php');">����[����]<span class="STYLE1"> (</span><span class="STYLE1">����</span><span class="STYLE1">)</span></a></td>
                <td width="18" align="center" class="service">&nbsp;</td>
			    <td width="146" height="31" align="center" background="../images/51/service.jpg" class="service"><a href="javascript:goto('http://pm511.webgame.com.cn/login/login.php');">һ��[�̻�]<span class="STYLE1"> (</span><span class="STYLE1">����</span><span class="STYLE1">) </span></a></td>
			   </tr>
			  <tr>
                <td width="146" height="10"></td>
                <td width="18"></td>
			    <td width="146"></td>
			  </tr>
			  
			  
            </table>
			
			
			
			
			
			</td>
          </tr>
        </table>
          <table width="446" height="37" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="67" height="35"></td>
              <td width="378">&nbsp;</td>
            </tr>
          </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
