<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>正在跳转……</title>
</head>
<style>
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td {padding:0; margin:0; outline:none} 
table {border-collapse:collapse; border-spacing:0} 
fieldset,img {border:0} 
address,caption,cite,code,dfn,em,strong,th,var {font-weight:normal; font-style:normal} 
h1,h2,h3,h4,h5,h6 {font-weight:normal; font-size:100%} 
body {font:12px/normal "宋体"; color:#0076ba; overflow-x:hidden;}
a,a:visited {color:#004c6d; text-decoration:none}
a:hover {color:#004c6d; text-decoration:none}
.main {width:806px;margin-left:auto;margin-right:auto}
.title {height:43px; background:url(../images/51_new/title.gif)}
.title h2 {float:right; width:127px; height:43px; background:url(../images/51_new/logo.jpg)}
.title h2 a {display:block; height:43px; text-indent:-9999px; overflow:hidden}
.title p {width:238px; height:43px; background:url(../images/51_new/title.jpg)}
.content { position:relative}
.content .bg img {display:block}
ul.list {position:absolute; left:280px; top:187px; width:450px}
ul.list li {float:left; width:138px; height:33px; overflow:hidden; margin:10px 12px 0 0; display:inline; background:url(../images/51_new/li.gif)}
ul.list li a {display:block; height:33px; font-weight:bold; cursor:pointer}
ul.list li span {float:left; margin:11px 0 0 13px; display:inline}
ul.list li em {float:right; width:30px; height:14px; margin:9px 9px 0 0; display:inline; text-align:center; overflow:hidden; line-height:15px; color:#36b6ed; background:url(../images/51_new/em.gif)}
.im {position:absolute; width:157px; height:26px; left:446px; top:549px; }
.im_con { position:relative; height:26px; background:url(../images/51_new/im_title.gif) no-repeat}
.im_con h2 {padding-left:20px; height:26px; text-decoration:underline; line-height:29px; font-weight:bold; overflow:hidden; cursor:pointer}
.im_list {display:none; position:absolute; bottom:28px; width:157px; background:#000}
.im_list .t {height:23px; background:url(../images/51_new/im_t.gif)}
.im_list .t a {float:right; width:16px; height:15px; margin:4px 6px 0 0; display:inline; background:url(../images/51_new/add.gif); text-indent:-9999px; overflow:hidden}
.im_list .b {height:6px; overflow:hidden; background:url(../images/51_new/im_b.gif)}
#ScroLeft li {width:127px; height:26px; margin-top:2px}
#ScroLeft li a {display:block; width:125px; height:24px; padding:1px; line-height:26px; overflow:hidden}
#ScroLeft li a:hover {padding:0; background:#e8f9ff; border:1px solid #d4edff}
#ScroLeft li img {float:left; margin:2px 7px 0 3px; display:inline}
#Scroll{width:157px; height:140px; padding:5px 0; background:url(../images/51_new/im_c.gif)}
#ScroLeft{float:left; height:100%; margin-left:9px; display:inline; overflow:hidden}
#ScroRight{position:relative; float:right; margin-right:5px; display:inline; height:100%; width:7px; background:url(../images/51_new/Scro.gif) center top;overflow:hidden}
#ScroLine{position:absolute; z-index:1; top:0px; left:0px; width:7px; height:19px; background:url(../images/51_new/Scro_on.gif); overflow:hidden; cursor:pointer}
</style>

<body<?php if(!isset($_GET['ticket'])||empty($_GET['ticket'])){?> onLoad="logonIns();"<?php } ?>>
<?php if(isset($_GET['ticket'])&&!empty($_GET['ticket'])){?>

</body>
</html>
<?php } ?>
<script type="text/javascript" src="http://ibw.sdo.com/flash/js/webwidget.js"> 
</script>

<script language="javascript">
<?php if(isset($_GET['ticket'])&&!empty($_GET['ticket'])){?>
<?php 
$_SESSION['reurl']='cas=1&ticket='.$_GET['ticket'];
?>
if(typeof(ibw)!="undefined"){
	ibw.appid=608;
	ibw.color="230";//圈圈皮肤默认颜色
	ibw.brightness="0.86999";//圈圈皮肤默认亮度
	ibw.saturation="0.76";//圈圈皮肤默认饱和度
	ibw.barMode=1;//圈圈在网页默认显示的模式(竖向:1 横向:2) 
	ibw.barDisplay="none";//圈圈在网页默认显示的状态，（打开："block"；关闭："none") 
	ibw.needLogout=false;// 设定圈圈是否需要注销功能（true(默认)：需要； false：不需要）
	
	ibw.barTop=30; ibw.barRight=30;//圈圈在网页默认显示的位置

}

ct=0;
var str="no";
if(typeof(ibw_tool)!="undefined"){
	newWin(1,1);
	str="ok";
}

function jump(){
	window.location="http://<?php echo $_SERVER['HTTP_HOST'] ?>/login/login.php?cas=1&ticket=<?php echo $_GET['ticket'] ;?>";
	ct++;
	document.getElementById('jumping').innerHTML+=ct;
}
document.write("<h1><font color='#00ff00'>"+str+"</font></h1>Loading...<div id='jumping'></div><br/><a href='http://<?php echo $_SERVER['HTTP_HOST'] ?>/login/login.php?cas=1&ticket=<?php echo $_GET['ticket'] ;?>'>如果没有自动浏览器跳转，请手动点这里。</a>");
jump();
setInterval("jump()",2000);

function newWin(userareaid, userserverid) {
	
	ibw_tool.setCookieByDays("IBW_ServerId", userserverid,0); //游戏区编号，areaid= 200908（联调测试配置的区编号）
	ibw_tool.setCookieByDays("IBW_AreaId",userareaid,0); //服务器编号  serverid=200808(联调测试配置的服编号)
}
<?php 
die("</script>");
}else{
?>
var cookie = document.cookie
var reUrl = "http://<?php echo $_SERVER['HTTP_HOST'] ?>/login/shengdaLogin.php";//登陆成功后重定向的url
var urlencode = UrlEncode(reUrl);
var url = "https://cas.sdo.com/cas/login?gateway=true&service="+urlencode;
location.replace (url);		
var logonNum = 0;
document.cookie="logonNum="+logonNum;	
			   
<?php } ?>
</script>	
</body>
</html>

<script type="text/javascript" src="http://ibw.sdo.com/flash/js/webwidget.js"> 
</script>


<script type="text/javascript">
ibw.appid=608;
ibw.color="230";//圈圈皮肤默认颜色
ibw.brightness="0.86999";//圈圈皮肤默认亮度
ibw.saturation="0.76";//圈圈皮肤默认饱和度
ibw.barMode=1;//圈圈在网页默认显示的模式(竖向:1 横向:2) 
ibw.barDisplay="none";//圈圈在网页默认显示的状态，（打开："block"；关闭："none") 
ibw.needLogout=false;// 设定圈圈是否需要注销功能（true(默认)：需要； false：不需要）

ibw.barTop=30; ibw.barRight=30;//圈圈在网页默认显示的位置


//var ibwLoginInterval = setInterval(openIBWLoginWindow,100);
//调用次数
var callIBWLoginTimes = 0;
//打开IBW登陆窗口
function openIBWLoginWindow()
{
	callIBWLoginTimes ++;
	//超过调用次数或已经完成用户激活判断
	if(callIBWLoginTimes>30 || ibw.checkUserActiveComplete){
		clearInterval(ibwLoginInterval);
	}
	if(ibw.checkUserActiveComplete){
		ibw_public.openLoginWindow()
	}
}

var oneNum = 0;
function newWin(userareaid, userserverid) {
	//alert(userName, userareaid, userserverid);
	
	ibw_tool.setCookieByDays("IBW_ServerId", userareaid,0); //游戏区编号，areaid= 200908（联调测试配置的区编号）
	ibw_tool.setCookieByDays("IBW_AreaId",userserverid,0); //服务器编号  serverid=200808(联调测试配置的服编号)
	//window.opener=null; 
	//window.close();
	//window.open("/hero/entry.jsp?userName=" + userName, "newwindow", "height=600, width=1000, top=100, left=200 ,toolbar=no , menubar=no, scrollbars=no, resizable=no, location=no, status=no");
}

function ppkRead(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
   		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

//ibw会更新该cookie的值  域是ibw.sdo.com，键名为IBW_UserIsLogin
function loginTest(passNum){
    if(ppkRead("logonNum") == 1){
		var cookie = document.cookie
		//var reUrl = "http://www.dayinxiong.sdo.com:8080/hero/IBW/testIBW.jsp";//登陆成功后重定向的url
		var reUrl = "http://<?php echo $_SERVER['HTTP_HOST'] ?>/login/shengdaLogin.php";//登陆成功后重定向的url
		//var reUrl = "http://pm1.sdo.com/login/login.php";//登陆成功后重定向的url
		//alert(reUrl);
		//var reUrl = "http://localhost:8080/hero/IBW/testIBW.jsp";//登陆成功后重定向的url
		var urlencode = UrlEncode(reUrl);
		var url = "https://cas.sdo.com/cas/login?gateway=true&service="+urlencode;
		location.replace (url);
		//location.replace ("http://test.cas.sdo.com/cas/login?gateway=true&service="+urlencode);
		
		
		var logonNum = 0;
		document.cookie="logonNum="+logonNum;	
			       
	}
}

function logonIns() { 
	 var logonNum = 1;
	 document.cookie="logonNum="+logonNum;   
} 


function UrlEncode(str) { 
		return transform(str); 
	} 
    
	function transform(s) { 
		var hex=''    
		var i,j,t 		    
		j=0 
		for (i=0; i<s.length; i++) { 
			t = hexfromdec( s.charCodeAt(i) ); 
			if (t=='25') { 
				t=''; 
			} 
			hex += '%' + t; 
		} 
		return hex; 
	} 
	    
	function hexfromdec(num) { 
		if (num > 65535) { return ("err!") } 
		first = Math.round(num/4096 - .5); 
		temp1 = num - first * 4096; 
		second = Math.round(temp1/256 -.5); 
		temp2 = temp1 - second * 256; 
		third = Math.round(temp2/16 - .5); 
		fourth = temp2 - third * 16; 
		return (""+getletter(third)+getletter(fourth)); 
	} 
	    
	function getletter(num) { 
		if (num < 10) { 
			return num; 
		} 
		else { 
			if (num == 10) { return "A" } 
			if (num == 11) { return "B" } 
			if (num == 12) { return "C" } 
			if (num == 13) { return "D" } 
			if (num == 14) { return "E" } 
			if (num == 15) { return "F" } 
		} 
	} 
	function reLogin() {
		location.reload();
	}

</script>
	
		

<?php 
die();
?>	
<div class="main" style="width:806px;margin-left:auto;margin-right:auto">
<div class="title"><h2><a href="http://www.webgame.com.cn/" title="叶网科技-网页游戏平台" target="_blank">页网科技</a></h2><p></p></div>
<div class="content">
<ul class="list">

	<li><a href="<?php if(isset($_GET['ticket'])&&!empty($_GET['ticket'])){?>http://kdjl01.game.qidian.com/login/login.php?cas=1&ticket=<?php echo $_GET['ticket'] ;}else{echo 'javascript:ibw_public.openLoginWindow();void(0)';}?>" <?php if(isset($_GET['ticket'])&&!empty($_GET['ticket'])){echo 'onclick="newWin(1,1)";void(0)';}?>><span style="color:#FF0000">一区[樱花]</span><em>正常</em></a></li>

</ul>
<div class="bg"><img src="../images/51_new/main_01.jpg" /><img src="../images/51_new/main_02.jpg" /><img src="../images/51_new/main_03.jpg" /><img src="../images/51_new/main_04.jpg" /></div>
</div>
</div>
