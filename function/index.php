<?php
require_once('../config/config.game.php');
require_once('../login/qidian_config.php');
//require_once((dirname(__FILE__)).'/kernel/dbSession.v1.php');
if($_SESSION['id'] == "")
{
	die('<script type="text/javascript">window.location="login/login.php"</script>');
}
secStart($_pm['mem']);

define("WEL","db_welcome1");
$cmd = unserialize($_pm['mem'] -> get(WEL));

$giftword = str_replace("\r\n",'!@#$^',$cmd['swfemotion']);

if(isset($_SESSION['ghpstr'])){
	if(!empty($_SESSION['ghpstr'])){
		$user	 = $_pm['user']->getUserById($_SESSION['id']);
		$bag	= $_pm['user']->getUserBagById($_SESSION['id']);
		$numarr = explode("\r\n",$_SESSION['ghpstr']);
		$arr = explode(',',$numarr[0]);
		$task = new task();
		if(is_array($arr)){
			foreach($arr as $v){
				$inarr = explode(':',$v);
				if(count($inarr) != 2){
					continue;
				}
				$givecheck = $task->saveGetPropsMore($inarr[0],$inarr[1]);
				$parr = $_pm['mysql'] -> getOneRecord("SELECT name FROM props WHERE id = {$inarr[0]}");
				$str .= '�����Ʒ��'.$parr['name'].'&nbsp;'.$inarr[1].' ����';
			}
		}
		$pnote = 'card='.$_REQUEST['cardid'].'&pass='.$_REQUEST['pwd'].'&Ӧ�ý�����'.$numarr[0].'----ʵ�ʣ�'.$str.'-----'.$checkflag;
		$_pm['mysql'] -> query("insert into gamelog (ptime,seller,buyer,pnote,vary) values (".time().",{$_SESSION['id']},{$_SESSION['id']},'$pnote',91)");
		$_SESSION['ghpstr'] = '';
		$_SESSION['ghflag'] = '';
	}
}

require_once('socketChat/config.chat.php');

if(!isset($_SESSION['nicknamegb']))
{
	$_SESSION['nicknamegb'] = $_SESSION['nickname'];
}
session_write_close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
<?=$cmd['title']?>
</title>
<link href="css/global.css" rel="stylesheet" type="text/css" />
<script src="js/global.js" type="text/javascript"></script>
<style type="text/css">
.nav{width:459px; height:294px;color:#630;}
.nav_01{width:459px; height:37px; float:left;}
.nav_02{width:419px; height:279px; background-image:url(../new_images/ui/cjjzbg03.gif); float:left; padding:15px 0 0 40px; } 
.tt1 {
	border: 1px solid #960;background-color:#F3E4B2;
}
.tt2 {
	border: 1px solid #960; height:50px; background-color:#F3E4B2; width:300px
}
</style>
</head>
<script language="javascript">
var myUid=<?php echo $_SESSION['id']+0; ?>;
var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
var CURSOR_HAND = 'c:pointer';
var friends={};
var blacks={};

var username="<?php echo $_SESSION["username"] ?>";

function   killErrors(){  
	return   true;
}     
window.onerror =  killErrors; 
var loudSpeaksMsg = {};
var displayedMsgId = 0;
function loadads()
{
	var host = window.location.host;
	var hostarr = host.split(".");
	var div = document.getElementById("adbottomleft");
	var div1 = document.getElementById("adbottomright");
	if(hostarr[1] == "webgame")
	{
		var firstword = hostarr[0].substr(2,2);
		if(firstword != "51")//����51��webgame
		{
			div.style.display="block";
			div1.style.display="block";
		}
		else
		{
			div.style.display="none";
			div1.style.display="none";
		}
	}
	else
	{
		div.style.display="none";
		div1.style.display="none";
	}
}
	
	function   killErrors(){  
		return   true;
	}     
	window.onerror =  killErrors; 
	var loudSpeaksMsg = {};
	var displayedMsgId = 0;
	
	

  String.prototype.trim = function() {
    return this.replace(/(^\s*)|(\s*$)/g, "");
  }

  function searchPocketBaike() {
    var searchKey = $("baike_input").value.trim();
    
    if (searchKey) {
        var url = 'function/search_knol.php?key=' + searchKey;
        //window.open(url, '', 'location=no,status=no');
        window.open(url, '', 'top=0,left=0,status=no,location=no,toolbar=yes,scrollbars=yes,menubar=no');
    } else {
      alert('��������Ч��Ϣ!');
      $("baike_input").value = searchKey;
      $("baike_input").focus();
    }
  }

  function showBugBox() {
      $("bugReportBox").innerHTML = '<input type="text" id="knol_bug_text" size=50 /><input type="button" value="����" />';
      $("bugReportBox").style.cursor = 'auto';
  }
	function cmotion()
	{
		var imgserver = "..//";
		try{
			 $('cmdiv').style.display='';
		}catch(e){
			var sp = document.createElement('DIV'); //hp font
				sp.style.cssText='position:absolute;left:380px;top:470px;z-index:100;font-size:0.8em;width:300px;border:1px solid #ccc;padding:5px;line-height:1.5;background-color:#fff;filter:alpha(opacity=85);';
				sp.id='cmdiv';
				document.body.appendChild(sp);
		}
		$('cmdiv').onclick=function(){$('cmdiv').style.display='none';};
		var allm = '';
		for(var i=1;i <= 36;i++)
		{
			allm = allm+"<img src='"+imgserver+"images/ui/motion/"+i+".gif' onclick=\"sendm('("+i+")')\"/> ";
		}
		
		$('cmdiv').innerHTML = allm+"<span onclick=$('cmdiv').style.display='none' style=cursor:pointer;>&nbsp;&nbsp;<u><b><font color=red>��</font></b></u></span>";
	}
	function sendm(m)
	{
		$('cmsg').value+=m;
	}
	function speakLoud(){
		var msg = loudSpeaksMsg;
		try{
			var flag = false;
			for(m in msg){
				flag=true;
				//if(parseInt(m)>displayedMsgId){
				document.getElementById("loudspeaker").style.display = 'block';	
				document.getElementById("loudspeaker").innerHTML = msg[m];
				displayedMsgId = m;
				//}
			}
			//speakLoudTimeout = setTimeout('speakLoud()',5000);	
			return;
			if(!flag){
				document.getElementById("loudspeaker").style.display = 'none';	
			}
		}catch(e){	}
		displayedMsgId = 0;
		//speakLoudTimeout = setTimeout('speakLoud()',5000);
	}
				
</script>
<script language=javascript src='./javascript/prototype.js'></script>
<script language=javascript src='./javascript/check.class.js'></script>
<script language=javascript src='./javascript/wz_dragdrop.js'></script>
<script language="JavaScript">
function dealUserList(str)
{
	refreshFAndB();
	var strs = str.split("|");
	for(var s=0;s<strs.length;s++)
	{
		updateForBOl(strs[s],1);
	}
	return true;
}
function refreshFAndB()
{
	var fstr=thisMovie("socketChatswf").getFriendList();
	var bstr=thisMovie("socketChatswf").getBlackList();
	var strs = [];
	var str = fstr;	
	
	if(str.length>0)
	{
		strs = str.split("|");
		for(var i=0;i<strs.length;i++)
		{
			if(strs[i].length>0)
			{
				friends[strs[i].replace(/[\$\`]/g,'')] = 0;				
			}
		}
		updateFriendListHTML();		
	}
	
	str = bstr;	
	if(str.length>0)
	{
		strs = str.split("|");
		for(var i=0;i<strs.length;i++)
		{
			if(strs[i].length>0)
			{
				blacks[strs[i].replace(/[\$\`]/g,'')] = 0;
			}
		}
	}
}

function updateFriendListHTML()
{
	var obj = $('frienlistDiv');
	obj.innerHTML='';
	var tmp = '<ul style="list-style:none; line-height:21px">';
	for(name in friends)
	{
		color='#909090';
		if(friends[name]==1)
		{
			color='#009900';
		}
		tmp+='<li style="cursor:pointer; color:'+color+'" onclick="$(\'cmsg\').value=\'//'+name+' \'">'+name+'</span></li>';		
	}
	tmp+='</ul>';
	obj.innerHTML=tmp;
}

function updateForBOl(name,sts)
{	
	if(typeof(friends[name])!='undefined')
	{		
		friends[name] = sts;
		updateFriendListHTML();	
	}
	
	if(typeof(blacks[name])!='undefined')
	{
		blacks[name] = sts;
	}
}
var msgflag = 0
function AsCallBack(str)
{
	//alert(str)
	//recvMsg('CT|'+str);
	if(str=='updateYouTeam')
	{
		recvMsg('SM|�����Ա״̬�����ı䣡');
		if(document.getElementById('gw').contentWindow.document.getElementById('creatUTeam'))
		{
			if(document.getElementById('gw').contentWindow.location.href.indexOf('?')>-1){
				document.getElementById('gw').contentWindow.location=document.getElementById('gw').contentWindow.location.href+'&rd='+Math.random();
			}else{
				document.getElementById('gw').contentWindow.location=document.getElementById('gw').contentWindow.location.href+'?rd='+Math.random();
			}
		}else{
			document.getElementById('gw').contentWindow.updateMyTeamInfo();
		}
	}else if(str=='uareKicked'){
		document.getElementById('gw').contentWindow.location.reload();
		recvMsg('SM|�����ӳ��߳����飡');
	}else if(str=='disbandTeam'){
		document.getElementById('gw').contentWindow.location.reload();
		recvMsg('SM|�����ɢ��');
	}else if(str.substr(0,13)=='returnVillege'){
		document.getElementById('gw').contentWindow.location='/function/Team_Mod.php?n='+str.replace('returnVillege','');
	}else if(str=='getTeamFightMod'){
		document.getElementById('gw').contentWindow.getTeamFightMod();
	}else if(str.substr(0,16)=='getTeamFightGate'){
		document.getElementById('gw').contentWindow.getTeamFightGate(str.substr(16));	
	}else if(str=='information-->'){
		alert('aaa');
		msgflag = 1;
		change_type();
	}
	//$('cmsg').value=str;
}
function change_type(){
	if($('ab').className=='tools2'){
		$('ab').className='tools';
	}else{
		$('ab').className='tools2';
	}
	if(msgflag == 1){
		window.setTimeout("change_type()",500);
	}else{
		$('ab').className='tools';
	}
}
</script>
<script language="JavaScript">

function IsNotTTFighting()
{
	var obj = document.getElementById("gw").contentWindow;
	var o1=obj.document.getElementById('FFig');
	var o2=obj.document.getElementById('i_table');
	var o3=obj.document.getElementById('i_table4');
	if(
		typeof(o1)=='object'&&typeof(o2)=='object'&&typeof(o3)=='object'
		&&
		o1!=null&&o2!=null&&o3!=null
	)
	{
		if(confirm('����������ħ����ս��������ǿ���˳����ܽ��д˲�����\n��ȷ��Ҫǿ���˳�����ħ��ô��\nˢ��������ᱻǿ���˳���\n����ǿ���˳����벻Ҫˢ���������'))
		{
			parent.unitySubmit('Model_TeamRoom','forceto_exit_team');
			return true;
		}
		return false;
	}
	
	o1=obj.document.getElementById('pet_4');
	o2=obj.document.getElementById('pet_7');
	o3=obj.document.getElementById('i_table4');
	if(
		typeof(o1)=='object'&&typeof(o2)=='object'&&typeof(o3)=='object'
		&&
		o1!=null&&o2!=null&&o3!=null
	)
	{
		if(confirm('����������ħ������ӣ�����ǿ���˳����ܽ��д˲�����\n��ȷ��Ҫǿ���˳�����ħ��ô��\nˢ��������ᱻǿ���˳���\n����ǿ���˳����벻Ҫˢ���������'))
		{
			parent.unitySubmit('Model_TeamRoom','forceto_exit_team');
			return true;
		}
		return false;
	}
	return true;
}




var jsReady = false;

function isReady() {
	return jsReady;
}

var tmR;
var reloadFlag = true;
if(navigator.appVersion.toString().indexOf('MSIE 7')!=-1)
{
	//document.write('<script type="text/javascript" src="/javascript/objectswap.js"><\/script>');
}
function pageInit() {
	try{
		window.clearTimeout(tmR);
	}catch(e){}
	jsReady = true;
	tmR = window.setTimeout('doReload()',15000);
	recvMsg("SM|��Ҫ����������......");
}
var reconnectTimes=0;
function doReload()
{
	if(!reloadFlag&&arguments.length==0) return;
	reconnectTimes++;
	try{
		if(reconnectTimes<=3){
			recvMsg("CT|�����������ӷ�����...");
		}else{
			recvMsg("CT|�����������ӷ�����...����ʱ���޷����ӣ�������ˢ��ҳ�档����Ͽ�ʱ,����޷�����ս��!");
		}
	}catch(e){
	}
	
	try{
		thisMovie("socketChatswf").reconnectSocket();
	}catch(e){
	}
	reloadFlag = true;
	setTimeout("doReload()",30000);
	return true;
}

function ftest()
{
	var o=document.getElementById('chatflash');
	o.style.width="600px";
	o.style.height="210px";
	o.style.top="600px";
	o=document.getElementById('swfdbgdiv');
	o.style.display='block';
}

function whenConnect()
{
	reloadFlag = false;
	window.clearTimeout(tmR);
	reconnectTimes=0;
	if($('chatDiv').innerHTML==''||$('chatDiv').innerHTML.indexOf('����')==-1)
	{
		$('chatDiv').innerHTML='<font color="#006600">��ӭ����ڴ�����!</font><br/>';
	}
}

<?php
$str=$_SESSION['id'].$_SESSION['username'].intval($_SESSION['password']).intval($_SESSION['vip']);
?>

function getSetting()
{
	return "<?php echo (isset($server_ip)?$server_ip:$_SERVER['HTTP_HOST'])
	."|".$_COOKIE[ini_get("session.name")]."|".$socket_port."|30|".
	$_SESSION['id']."|".$_SESSION['username']."|".intval($_SESSION['password'])."|".intval($_SESSION['vip']).'|'.$_SESSION['nickname'].'|'.md5($str);
	?>";
}

function getName()
{
	//<?php echo $_SESSION['nickname']."\r\n"; ?>
	return "<?php echo $_SESSION['nicknamegb']; ?>";
}


function thisMovie(movieName) {
	if (navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
}

function sendToActionScript(value) {	
	thisMovie("socketChatswf").sendToActionScript(value);
}

function setFWP(value) {	
	thisMovie("socketChatswf").setWP(value);
}

function setFGC(value) {	
	thisMovie("socketChatswf").setGC(value);
}

function setHWP(value,u)
{
	$('cbwp').checked = value;
	if($('cbwp').checked)
	{
		recvMsg("SM|<font color='#ff0000'>��ʾ��ֹͣ��������·��ġ��ܡ���ǰ�Ŀ�Ĺ�ȥ�����ɣ�</font>");
	}
	$('cbwp').parentNode.title="��ǰ�������Ķ���Ϊ:"+u+".";	
}

function setUseSocketRefreshNotice(){
	SOCKET_TEAM_STATE = 1;
	parent.SOCKET_TEAM_STATE = 1;
	top.SOCKET_TEAM_STATE = 1;
	window.parent.SOCKET_TEAM_STATE = 1;
}

function getTimeStr()
{
	var date = new Date();
	var str = date.getHours();
	str += ":"+date.getMinutes();
	str += ":"+date.getSeconds();
	return str;
}

function AsCallDebug(str) {
	try{
		document.getElementById('cmsg').value='AsCallDebug say: '+str;
	}catch(e)
	{
		alert("AsCallDebug 0 ="+e);
	}
}

function callJS(str)
{
	eval(str);
}

function callGMCommand(str)
{
	var opt = {
    		 method: 'get',
    		 onSuccess: function(t) {
				 if(t.responseText == 'NOBROADCAST'){
					 Alert('��û�����ȵ��ߣ�');
				 }
				 if(t.responseText == 'TOOFAST'){
					 Alert('��˵���ٶ�̫�죡');
				 }
				 if(t.responseText.indexOf('DATATOOLONG')==0){
					 Alert('����̫����');
				 }
				 if(t.responseText.indexOf('nabaweihu')==0){
					 Alert('���ȹ�������ά���У�');
				 }
    		 },
    		 on404: function(t) {
    		 },
    		 onFailure: function(t) {
    		 },
    		 asynchronous:true        
		}

	var ajax=new Ajax.Request('/function/chatGate.php?msg='+decodeURI(str), opt);
	return true;
}

function substrx(str,len)//��ȡ����,Ӣ������
{
    var rtn="";
    var l=0;
    for(var i=0;l<len&&i<str.length;i++)
    {
        if(str.substr(i,1).match(/^\w$/))
        {
            l+=0.5;
        }else{
            l++;
        }
        rtn+=str.substr(i,1);
    }
    return rtn;
}

// JavaScript helper required to detect Flash Player PlugIn version information
function GetSwfVer(){
	// NS/Opera version >= 3 check for Flash plugin in plugin array
	var flashVer = -1;
	
	if (navigator.plugins != null && navigator.plugins.length > 0) {
		if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
			var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
			var descArray = flashDescription.split(" ");
			var tempArrayMajor = descArray[2].split(".");			
			var versionMajor = tempArrayMajor[0];
			var versionMinor = tempArrayMajor[1];
			var versionRevision = descArray[3];
			if (versionRevision == "") {
				versionRevision = descArray[4];
			}
			if (versionRevision[0] == "d") {
				versionRevision = versionRevision.substring(1);
			} else if (versionRevision[0] == "r") {
				versionRevision = versionRevision.substring(1);
				if (versionRevision.indexOf("d") > 0) {
					versionRevision = versionRevision.substring(0, versionRevision.indexOf("d"));
				}
			}
			var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
		}
	}
	// MSN/WebTV 2.6 supports Flash 4
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
	// WebTV 2.5 supports Flash 3
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
	// older WebTV supports Flash 2
	else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
	else if ( isIE && isWin && !isOpera ) {
		flashVer = ControlVersion();
	}	
	return flashVer;
}
function ControlVersion()
{
	var version;
	var axo;
	var e;
	// NOTE : new ActiveXObject(strFoo) throws an exception if strFoo isn't in the registry
	try {
		// version will be set for 7.X or greater players
		axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		version = axo.GetVariable("$version");
	} catch (e) {
	}
	if (!version)
	{
		try {
			// version will be set for 6.X players only
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
			
			// installed player is some revision of 6.0
			// GetVariable("$version") crashes for versions 6.0.22 through 6.0.29,
			// so we have to be careful. 
			
			// default to the first public version
			version = "WIN 6,0,21,0";
			// throws if AllowScripAccess does not exist (introduced in 6.0r47)		
			axo.AllowScriptAccess = "always";
			// safe to call for 6.0r47 or greater
			version = axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 4.X or 5.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version = axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 3.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version = "WIN 3,0,18,0";
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 2.X player
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			version = "WIN 2,0,0,11";
		} catch (e) {
			version = -1;
		}
	}
	
	return version;
}
function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
{
	versionStr = GetSwfVer();
	if (versionStr == -1 ) {
		return false;
	} else if (versionStr != 0) {
		if(isIE && isWin && !isOpera) {
			// Given "WIN 2,0,0,11"
			tempArray         = versionStr.split(" "); 	// ["WIN", "2,0,0,11"]
			tempString        = tempArray[1];			// "2,0,0,11"
			versionArray      = tempString.split(",");	// ['2', '0', '0', '11']
		} else {
			versionArray      = versionStr.split(".");
		}
		var versionMajor      = versionArray[0];
		var versionMinor      = versionArray[1];
		var versionRevision   = versionArray[2];
        	// is the major.revision >= requested major.revision AND the minor version >= requested minor
		if (versionMajor > parseFloat(reqMajorVer)) {
			return true;
		} else if (versionMajor == parseFloat(reqMajorVer)) {
			if (versionMinor > parseFloat(reqMinorVer))
				return true;
			else if (versionMinor == parseFloat(reqMinorVer)) {
				if (versionRevision >= parseFloat(reqRevision))
					return true;
			}
		}
		return false;

	}
}
var requiredMajorVersion = 10;
// ���� Flash �Ĵΰ汾��
var requiredMinorVersion = 0;
// ���� Flash �İ汾��
var requiredRevision = 2;

var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
if(!hasRightVersion) {
	var alternateContent = ' ����flash player�汾����,����������Ϸ��\r\n '
		+ '����ȥ��ȡflash player���°汾ô? ';
	if(confirm(alternateContent)){
		window.location= 'http://www.adobe.com/go/getflashplayer/';	
	}
}
jsReady = true;

window.onbeforeunload = function(){ 
	return checkTF();
}
function checkTF(){
	if(typeof($('gw').contentWindow.teamLeader)!='undefined' && $('gw').contentWindow.teamLeader>0){
		if(confirm("���������ս��,�˳�����������������Ա����Ԥ�ϵ�״��,ȷ���뿪��?\n���ֲ��������ʱ�����ɢ����֮��������ӣ�"))
		{
			if(arguments[0])
				return true;
			else	
				return;
		}else{
			return "���������ս��,�˳�����������������Ա����Ԥ�ϵ�״��,ȷ���뿪��?\n���ֲ��������ʱ�����ɢ����֮��������ӣ�";
		}
	}
	if(arguments[0])
		return true;
	else	
		return;
}
</script>
<body onkeydown="KeyDown(event);" onLoad="loadads()">
<div id="new_guide_div" style="position:absolute;top:0;left:0;z-index:29998;width:1000px;height:620px; display:none"></div>
<div id="sysstatmsgs" style="position:absolute;width:344px;height:18px;z-index:10002;left: 340px;top: 548px;	opacity: 0.6; filter : progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=60,finishOpacity=100); display:none"> </div>
<div id="chatflash"  style="display:none1; z-index:0; left:515px; top:500px; position:absolute; width:1px; height:1px; overflow:hidden" >
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
		id="socketChatswf" width="690" height="216"
		codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
    <param name="movie" value="socketChat/socketchat.swf" />
    <param name="quality" value="high" />
    <param name="bgcolor" value="#CCE9A9" />
    <param name="wmode" value="Opaque" />
    <param name='allowScriptAccess' value ='always' />
    <embed src="socketChat/socketchat.swf" quality="high" bgcolor="#CCE9A9"
			width="690" height="216" name="socketChatswf" align="middle"
			play="true" loop="false" quality="high" allowScriptAccess="sameDomain"
			type="application/x-shockwave-flash" wmode="Opaque"
			pluginspage="http://www.macromedia.com/go/getflashplayer">
    </embed>
  </object>
</div>
<iframe id="iframechat" width="600" height="215" scrolling="no" src="/socketChat/chatS.php" style="display:none; z-index:3;left:400px;top:600px; position:absolute;" class="wgframe"></iframe>
<?php
//ʢ��IBW��ʾ��ʼ
$www=explode('.',$_SERVER['HTTP_HOST']);
$website='';
for($i=1;$i<count($www);$i++)
{
	$website.=$www[$i].'.';
}
switch ($website)
{
	case 'game.qidian.com.':
	
?>
<script type="text/javascript" src="http://ibw.sdo.com/flash/js/webwidget.js"> 
</script>
<script type="text/javascript">
ibw.appid=608;
ibw.color="230";//ȦȦƤ��Ĭ����ɫ
ibw.brightness="0.86999";//ȦȦƤ��Ĭ������
ibw.saturation="0.76";//ȦȦƤ��Ĭ�ϱ��Ͷ�
ibw.barMode=1;//ȦȦ����ҳĬ����ʾ��ģʽ(����:1 ����:2) 
ibw.barDisplay="none";//ȦȦ����ҳĬ����ʾ��״̬�����򿪣�"block"���رգ�"none") 
ibw.needLogout=false;// �趨ȦȦ�Ƿ���Ҫע�����ܣ�true(Ĭ��)����Ҫ�� false������Ҫ��
ibw.barTop=30; ibw.barRight=30;//ȦȦ����ҳĬ����ʾ��λ��
</script>
<?php	
break;
}
?>
<script type="text/javascript" src="./javascript/wz_tooltip.js"></script>
<iframe id="iframestat" width="600" height="200" src="/function/onlineStat.php" style="display:none" class="wgframe"></iframe>
<script language=javascript src='./function/onlineGate.php'></script>
<div class="backbg" id="light"></div>
<div id="baginfo" style="margin-left:760px; margin-top:100px;z-index:20; position:absolute; display:none"></div>
<div id="lgn" class='st' style="filter:alpha(opacity=0);"></div>
<div id="page">
  <div id="main">
    <div class="main_t clearfix">
      <!-- ��ർ����ť ��ʼ -->
      <div style="background-image:../new_images/index/index_left.jpg" class="side l">
        <div style="padding-top:33px">
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="197" height="337">
            <param name="movie" value="../new_images/index/menu_new.swf">
            <param name="quality" value="high">
            <param name='allowScriptAccess' value ='always' />
            <param name="wmode" value="transparent">
            <embed  allowScriptAccess='always'  src="../new_images/index/menu_new.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="197" height="337" wmode="transparent"></embed>
          </object>
        </div>
      </div>
      <!-- ��ർ����ť ���� -->
      <div class="content r">
        <!-- ���Ͻǹ����� ��ʼ -->
        <div class="tools" id="ab"></div>
        <div class="tools_btn"><a onclick="ShowBox('Tools','1','3')">����</a><a onclick="ShowBox('Tools','2','3')">��Ϣ</a><a onclick="ShowBox('Tools','3','3')">����</a><a class="t4" onClick="addBookmark();">�ղ�</a></div>
        <!-- ���Ͻǹ����� ���� -->
        <!-- ��Ϸ������ ��ʼ -->
        <div class="gamebox">
          <iframe id="gw" name="gamewindow" src="function/Welcome_Mod.php" style="width:788px; height:319px;" frameborder="0" scrolling="no" allowTransparency="true"></iframe>
        </div>
        <!-- ��Ϸ������ ���� -->
      </div>
    </div>
    <div class="main_b clearfix">
      <div class="chat l">
        <!-- ������� ��ʼ -->
        <div class="ol">������ң�<span id="onlinec"></span></div>
        <!-- ������� ���� -->
        <div class="chat_box">
          <!-- ����� ��ʼ -->
          <div class="chat_cont" id="chatDiv">�����У����Ժ򡭡� </div>
          <div id="help_chat_info" style="z-index:100;position:absolute;top:100px;display:block; left:250px"></div>
          <div id="bbshow" style="z-index:100; position:absolute; display:block; left:235px; width:300px; top: -27px;"></div>
          <!-- ����� ���� -->
          <!-- ���칤���� ��ʼ -->
          <div class="chat_tool">
            <input id="cmsg" class="inp" value="" type="text">
            <div class="select_lt" id ="select_lt"> <span>����</span>
              <ul class="hidden">
                <li class="i">���͵�</li>
                <li><a href="javascript:;" target="_self" title="����" name="0">����</a></li>
                <li><a href="javascript:;" target="_self" title="˽��" name="1">˽��</a></li>
                <li><a href="javascript:;" target="_self" title="����" name="2">����</a></li>
                <li><a href="javascript:;" target="_self" title="������" name="3">������</a></li>
              </ul>
            </div>
            <div class="select_pd" id ="select_pd"> <span>ȫ��</span>
              <ul class="hidden">
                <li class="i">��ʾƵ��</li>
                <li onclick="showSpecialMsg('')"><a href="javascript:;" target="_self" title="ȫ��" name="">ȫ��</a></li>
                <li onclick="showSpecialMsg('WP')"><a href="javascript:;" target="_self" title="˽��" name="WP">˽��</a></li>
                <li onclick="showSpecialMsg('SG')"><a href="javascript:;" target="_self" title="���" name="SG">���</a></li>
                <li onclick="showSpecialMsg('GC')"><a href="javascript:;" target="_self" title="����" name="GC">����</a></li>
              </ul>
            </div>
            <div class="select_ys" id ="select_ys" style="display:none"> <span>��</span>
              <ul class="hidden">
                <li><a href="javascript:;" target="_self" title="��ɫ" name="">��</a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="!"><font color="#ff3399">��</font></a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="#"><font color="#33cc00">��</font></a></li>
                <li><a href="javascript:;" target="_self" title="��ɫ" name="!!"><font color="#0000ff">��</font></a></li>
              </ul>
            </div>
            <input name="type" type="hidden" id="tknew" />
            <input name="type" type="hidden" id="tklist" />
            <input name="type" type="hidden" id="ccolor" />
            <script type="text/javascript">
							initSelect("select_lt", "tknew");
              initSelect("select_pd", "tklist");
              initSelect("select_ys", "ccolor");
            </script>
            <img style="" onclick="cmotion()" src="../images/ui/motion/3.gif">
            <input id="snd" class="but" value=" " onclick="chatH.sendMsg();" type="button">
            <img title="��Ӻ��ѻ��������" style="cursor: pointer;" src="../images/friends.gif" onclick="if($('frienlist').style.display=='none'){$('frienlist').style.display='block';}else{$('frienlist').style.display='none';}" class="add"> </div>
          <!-- ���칤���� ���� -->
        </div>
      </div>
      <div class="tip r">
        <!-- �ڴ��ٿ����� ��ʼ -->
        <div class="wiki">
          <form>
            <input type="text" id="baike_input" class="inp" />
            <input type="button" class="btn" value=" "  onClick="searchPocketBaike();return false;" />
          </form>
        </div>
        <!-- �ڴ��ٿ����� ���� -->
        <iframe name="gamewindow" src="<?=$cmd['iframe']?>" style="width:253px; height:94px; overflow:hidden;" frameborder="0" scrolling="no" allowTransparency="true"></iframe>
        <!-- ���Ż���� ��ʼ -->
        <!--ul>
          	
            <li><span><a href="#">�鿴</a></span><a href="#">��һ��һ����ȫ�������</a></li>
            <li><span><a href="#">�鿴</a></span><a href="#">�������У���������֮�ã�</a></li>
            <li><span><a href="#">�鿴</a></span><a href="#">�ٱ���ζ����ǧ���</a></li>
            
          </ul-->
        <!-- ���Ż���� ���� -->
        <div class="link">
          <ul>
            <!-- �������� ��ʼ -->
            <li><a  onclik="('help').style.display='block';void(0)">����</a></li>
            <li><a href="<?=$cmd['guanwang']?>" target="_blank">����</a></li>
            <li><a onclik="<?=$cmd['pay']?>;void(0);">��ֵ</a></li>
            <li><a href="<?=$cmd['kefu']?>" target="_blank">�ͷ�</a></li>
            <li><a href="<?=$cmd['discuss']?>" target="_blank">��̳</a></li>
            <li><a href="<?=$cmd['exit']?>">�˳�</a></li>
            <!-- �������� ���� -->
          </ul>
        </div>
      </div>
    </div>
    <!-- �ײ����� ��ʼ -->
    <?=$cmd['linkatbottom']?>
    <!--div class="footer"><a href="#">���ְ���</a><a href="#">����ϳ�</a><a href="#">׷������</a><a href="#">����һ��</a><a href="#">���߻��</a><a href="#">17173�ٿ�</a><a href="#">���������ύ</a></div-->
    <!-- �ײ����� ���� -->
  </div>
</div>
<div id="helpwin" style="width:400px;height:0px;overflow:hidden; position:absolute;top:0px;left:420px;z-index:1000; display:none;text-align:left;">
  <table width="326" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="23" background="../images/ui/help/kj02.gif" style="display:block"><span id="showmybagusedcells" style="font-size:12px; font-weight:bold; color:#ffffff;"></span></td>
    </tr>
    <tr>
      <td  height="300" valign="top"><div id="helpwincet" style="font-size:12px;scrollbar-face-color:#4EB5B4;scrollbar-highlight-color:#ffffff;scrollbar-3dlight-color:#4EB5B4;scrollbar-shadow-color:#ffffff;scrollbar-darkshadow-color:#4EB5B4;scrollbar-track-color:#4EB5B4;scrollbar-arrow-color:#ffffff; overflow:auto;width:317px;height:290px;padding:5px;line-height:1.7;color:#087f95; display:block; background-color:#F1F8DD;position:absolute;z-index:5;"></div></td>
    </tr>
    <tr>
      <td height="20" background="../images/ui/help/kj03.gif" align="center" style="filter:alpha(opacity=90);"><input type="button" value="ʹ��" style="background-image:url(../images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;"  onclick="Used();" id="helpcmd"/>
        onclick="Used();" onclick="Reset();"
        <input type="button" value="����" style="background-image:url(../images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;"  onclick="Reset();" id="helpcmd2"/>
        <input type="button" value="����" style="background-image:url(../images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;display:none;"  onclick="" id="helpcmd3"/>
        <input type="button" value="�ر�" style="background-image:url(../images/ui/help/kj04.gif);border:0px;width:62px;height:20px;color:#2F291D;cursor:pointer;display:none;"   id="helpcmd1"/>
      </td>
    </tr>
    <tr>
      <td style="background-image:url(../images/ui/help/kj01.gif); height:17px; display:block; filter:alpha(opacity=90);" align="center"></td>
    </tr>
  </table>
  <div id="help_win_info"></div>
</div>
<!--������-->
<div id=adbottomleft style="left:20px;top:635px;width=460px;height=110px;position:absolute;">
  <?=$cmd['adbottomleft']?>
</div>
<div id=adbottomright style="left:522px;top:635px;width=460px;height=110px;position:absolute;">
  <?=$cmd['adbottomright']?>
</div>
<!--�ұ߹��-->
<div id=adright style="left: 1007px; width: 120px; height: 430px; top:0px; position:absolute">
</script>
</div>
<div id=ads style="left:1007px;top:440px;width=120px;height=193px;position:absolute;">
  <?=$cmd['ad_top']?>
</div>
<div id='systips' style="position:absolute;width:246px; z-index:2;left:400px;top:390px;font-size:12px;color:#ffffff;height:142px; border:0px;background:url(../images/ui/main/boxk.gif);filter:alpha(opacity=60); -moz-opacity:0.6;display:none; padding:10px;z-index:10000"></div>
<!--����-->
<div id="help" style="position:absolute; left:701px; top:348px; width:282px; height:209px; z-index:10;padding-top:3px;display:none">
  <table width="286" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="14"><img src="../images/help/bbz01.gif" width="14" height="29"></td>
            <td background="../images/help/bbz02.gif"><b><font color="green" style="font-size:12px;">�ڴ��������ְ���ϵͳ1.1(���ڿ��϶�)</font></b></td>
            <td width="31"><img src="../images/help/bbz03.gif" width="31" height="29"></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td height="174" valign="top" align="left" background="../images/help/bbz04.gif" style="font-size:12px;padding:10px;padding-top:0px;line-height:1.7;"><a href="javascript:helpsys('desc');void(0);" style='color:#1c4ec1'>���</a> <a href="javascript:helpsys('city');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('shop');void(0);" style='color:#1c4ec1'>�̵�</a> <a href="javascript:helpsys('gpc');void(0);" style='color:#1c4ec1'>���</a> <a href="javascript:helpsys('skill');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('chat');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('task');void(0);" style='color:#1c4ec1'>����</a> <a href="javascript:helpsys('bag');void(0);" style='color:#1c4ec1'>װ��</a> <a href="javascript:$('help').style.display='none';void(0);" style='color:#1c4ec1'>�ر�</a> <span id='helptarget' style="color:#333333;"><br/>
        ���ڴ����顷�Ǹ�����ȡ�ڴ�����ϵ����Ϸ�ľ������иı�ĳ�����������ҳ��Ϸ,��������,��ʹ���ϰ��ʱ��,��ֻҪ����ҳ���ܺ��Լ��İ��ĳ������Ķȹ�һ�죡.<br/>
        <a href="<?=$cmd['help']?>" target="_blank"><img src="../images/help/help.gif" 
		border=0></a> <font color=green>�رպ�TAB��(��ĸQ��ߵļ�)�����ٴδ򿪣�</font> </span> </td>
    </tr>
    <tr>
      <td height="12"><img src="../images/help/bbz05.gif" width="286" height="12"></td>
    </tr>
  </table>
</div>
<div class="box_task_show_123" id="taskmsg">
  <!--div class="box_text_top">
  ��ǰ���ܵ�������Ϣ��<br/>
  ���ܵص㣺[�ƹ�] ������<br/>
  ���ܶԻ���<br/>
  ��������֪��ʲô����ճǵ����ܣ�<br/>
  �����ɣ�����(�ƽ������)��(��ľЫ)��(�ľ�з)��(Ѫ����)��(ˮ��ʥ�׳��10ֻ)����ȥ(�����̵�)��Ů���˽����֮�ǵ����ܰɣ�<br/> 
  </div>
  
  <div class="task_win">
    <img src="../new_images/index/ms_task_line1.jpg" border="0" /><br/>
    <br/>
    <br/>
    <br/>
  </div>
  
  <div class="task_over">
    <img src="../new_images/index/ms_task_line2.jpg" border="0" /><br/>
    <br/>
    <br/>
    <br/>
  </div>
  
  <div id="taskbtn1"></div>
  <div id="taskbtn2"></div>
  <div id="taskbtn3" onClick="CloseLogin()"></div>
  <div id="colsetask" onClick="CloseLogin()"></div-->
</div>
<!--div class="taskinfo" id="tasktip" style="display:none">
<!--iframe style="position: absolute; z-index: -1; width: 100%; height: 100%; top: 0;
left: 0; scrolling: no;" frameborder="0" src="aa.html" allowtransparency="true"></iframe-->
<!--div class="tasktop">
    <div class="close" onClick="CloseLogin()"></div>
    </div>
    <div class="taskmid" id="taskmsg">    </div>
	      <div class="tip02" id="do_task">
	    ��ʾ��Ϣ    </div>
    <div class="taskbot">    </div>
</div-->
<div width="680" id="swfdbgdiv" height="240" style="display:none;left:0px;top:600px; z-index:3; position:absolute" class="wgframe">
  <textarea id="swfdbg" cols="60" rows="36">
</textarea>
</div>
<!-- ���� ��ʼ -->
<div class="box_pack" id="Box_Tools_1">
  <div class="box_cont"  id="bags">
    <!--div class="close_btn" onclick="ShowBox('Tools','1','3')"></div>
      	<div class="i_pack">��ǰ�����ռ䣺10/30</div>
        <div class="pack_title">
        	<ul class="list l1"><li><p class="p1">ͼ��</p><p class="p2">��Ʒ����</p><p class="p3">����</p><p class="p4">����</p></li></ul>
        </div-->
    <!--div class="pack_cont">
          <ul class="list l1">
          	
            <li><a href=""><p class="p1"><img src="images/temp/ico.png" /></p><p class="p2">��Ʒ����</p><p class="p3">����</p><p class="p4">20</p></a></li>
            <li><a href=""><p class="p1"><img src="images/temp/ico.png" /></p><p class="p2">��Ʒ����</p><p class="p3">����</p><p class="p4">20</p></a></li>
          </ul>
        </div>
        <div class="pac_btn">
        	<input type="button" class="ico_btn" value="ʹ��" onclick="Used();"/>
          <input type="button" class="ico_btn" value="����ֿ�" onclick="putBagProps2Depot();"/>
          <input type="button" class="ico_btn" value="����" onclick="dropBagProps();"/>
          <input type="button" class="ico_btn" value="�ر�" onclick="ShowBox('Tools','1','3')" />
        </div-->
  </div>
</div>
<!-- ���� ���� -->
<!-- ��Ϣ ��ʼ -->
<div class="box_msg" id="Box_Tools_2">
  <div class="box_cont">
    <div class="close_btn" onclick="ShowBox('Tools','2','3')"></div>
    <div class="msg_cont">
      <ul class="list" id='infos'>
      </ul>
    </div>
  </div>
</div>
<!-- ��Ϣ ���� -->
<!-- ���� ��ʼ -->
<div class="box_task" id="Box_Tools_3">
  <div class="box_cont">
    <div class="close_btn" onclick="ShowBox('Tools','3','3')"></div>
    <div class="i_task" id="activity_show"> </div>
    <div class="task_nav">
      <h2>�����б�</h2>
      <div class="task_list" id="task_title_list"> </div>
      <div class="task_pages"><a onclick="getTaskAll_second(1);">��һҳ</a><span id="sort">1/2</span><a onclick="getTaskAll_second(2);">��һҳ</a></div>
    </div>
    <div class="task_cont">
      <h2><a onclick="getTaskAll();">�鿴��ǰ����</a></h2>
      <div class="task_cont_list" id="task_every_list"></div>
    </div>
  </div>
</div>
<!-- ���� ���� -->
<span id="activity_show_every" style="width:100px; height:50px; position:absolute; left:171px; top:71px;z-Index:12000;display:none;background-color:#e1cea1;border:1px solid #b18033;color:#6c4200;line-height:22px;padding-left:10px;"></span>
<div id="frienlist" style="position:absolute;width:108px; padding:6px; height:155px;z-index:1;left: 1007px;top: 436px; overflow:hidden; overflow-y:auto; background-color:#007575; display:none">
  <div style="cursor:pointer; color:#0000CC; font-weight:bold" onclick="if(IsNotTTFighting()){blacklist();}"> ���Ѻͺ���������
    <hr>
  </div>
  <div id="frienlistDiv" style="width:100%"></div>
</div>
<div class="nav" id="create_guild" style="display:none; z-index:100; position:absolute; left: 339px; top: 82px;">
  <div class="nav_01"><img src="../new_images/ui/cjjzbg01.gif" width="388" height="37" /><img src="../new_images/ui/cjjzbg02.gif" width="71" height="37" style="cursor:pointer" onclick="$('create_guild').style.display='none'" /></div>
  <div class="nav_02">
    <table width="380" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="62" height="24">��������</td>
        <td width="318"><input  name="name" type="text" id="name" class="tt1"/>
          �޺���2-8��</td>
      </tr>
      <tr>
        <td>�������</td>
        <td><textarea name="info" cols="50" rows="4" class="tt2" id="info"></textarea></td>
      </tr>
      <tr>
        <td height="24">&nbsp;</td>
        <td>��200����������</td>
      </tr>
    </table>
    <table width="390" border="0" cellspacing="0" cellpadding="0" style="margin-top:20px; color:#3D5C00; line-height:20px;">
      <tr>
        <td width="84" height="24" valign="top">���崴��������</td>
        <td width="296">1.ӵ��vip��               2.��δ������������<br />
          3.δ�����������������    4.����ӵ��10��vip����</td>
      </tr>
      <tr>
        <td height="50" valign="top">����ϵͳ˵����</td>
        <td>�����Աÿ�����ȡ����������ȼ�Խ�߻�ø���Խ��<br />
          ������Ա����ͨ����������ս��������㡣<br />
          ���������ͨ�������̵깺��������߻�á�</td>
      </tr>
    </table>
    <table width="390" border="0" cellspacing="0" cellpadding="0" style="margin-top:15px;">
      <tr>
        <td valign="top" style="padding-left:145px;"><img src="../new_images/ui/cjjz02.jpg" width="95" height="28" style="cursor:pointer" onclick="$('gw').contentWindow.create_ajax()" /></td>
      </tr>
    </table>
  </div>
</div>
<div id="guide_click" style="border:1px solid #ff0000;z-index:30000;position:absolute"></div>
<div id="guide_girl" style="border:1px solid #ff0000;z-index:30000;position:absolute;left:356px;top:145px; width:492px; height:308px; background-image:url(../new_images/ui/guide_girl.gif); background-repeat:no-repeat; display:">
  <div id="guide_text" style="display:none;width:200px;border:1px solid #ff0000;left:158px;top:104px;z-index:30000;position:absolute; cursor:pointer;"> <span style="cursor:pointer;" onclick="$('gw').src='/function/City_Mod.php';doguide();">��ӭ�����ڴ��������磬���Ǿ���ʹ�ߡ�С�ܡ������������һ����Ϥһ�¿ڴ��ɣ�����÷��Ľ���Ŷ������������</span>  </div>
  <div id="guide_a" style="width:72px;border:1px solid #ff0000;left:417px; height:66px;top:154px;z-index:30000;position:absolute; cursor:pointer;" onclick="do_over()"></div>
  <div id="guide_next" style=";width:58px;border:1px solid #ff0000;left:394px; height:49px;top:227px;z-index:30000;position:absolute; cursor:pointer;"></div>
</div>
</body>
</html>
<script type="text/javascript">
SET_DHTML(CURSOR_HELP, RESIZABLE, SCROLL, "help");
SET_DHTML(CURSOR_HAND, RESIZABLE, SCROLL, "frienlist");
$("frienlist").style.left='1007px';
$("frienlist").style.top='436px';
</script>
</center>
<script language="javascript" src='javascript/index.js'></script>
<script language="javascript" src="javascript/msg.js"></script>
<!--script type="text/javascript" src="http://w.webgame.com.cn/script/core/pv_stat.js?ids=73,74">
</script-->
<!--script language="javascript" src="javascript/socket.class.js"></script-->
<script type="text/javascript">
pageInit();
//document.title = '�ڴ������ '+  num[window.location.host.substr(0,window.location.host.indexOf('.'))]+'�� �´�½1.0';
function addBookmark() {	
	//window.external.AddFavorite(document.location.href,document.title);
	if (document.all){   
       window.external.addFavorite(document.URL,document.title);   
    }else if (window.sidebar){   
       window.sidebar.addPanel(document.title, document.URL, "");   
    } 
}

function msgtipsfun(){
	$('friendlist').src='function/friendlist.php';
}



function blacklist()
{
	document.getElementById('gw').src='./function/User_Mod.php?type=list';
	/*$('ta').style.zIndex=2;
	$('tb').style.zIndex=3;
	$('tc').style.zIndex=1;*/
}
var dBody = null;
function getBody(){
	if(!dBody)dBody=(document.compatMode&&document.compatMode.indexOf('CSS')>-1)?document.documentElement:document.body;return dBody;
}
function getScrollX(){
	return window.pageXOffset||window.scrollX||getBody().scrollLeft||0;
}
function getScrollY(){
	return window.pageYOffset||window.scrollY||getBody().scrollTop||0;
}
function OpenLogin(op,tid,n){
	var x=document.documentElement.clientWidth/2+getScrollX()-446/2;
	var y=document.documentElement.clientHeight/2+getScrollY()-264/2;
	var sWidth=document.body.scrollWidth;
	var sHeight=document.body.scrollHeight;
	var xHeight=document.documentElement.clientHeight;
	if (sHeight > xHeight)
	{
		var SSS = sHeight
	}
	else
	{
		var SSS = xHeight
	}
	var opt = {
     	method: 'get',
		onSuccess: function(t) {
					//$('do_task').innerHTML = "";
			 		if(t.responseText!='') $('taskmsg').innerHTML = t.responseText;
    		 	},
     	asynchronous:true        
	}
	var ajax=new Ajax.Request('../function/getTaskinfo.php?op='+op+'&n='+n+'&t='+tid, opt);
	var sHH = document.documentElement.clientHeight;
	document.getElementById('light').style.display='block'; 
	document.getElementById('light').style.width=sWidth+"px";
	document.getElementById('light').style.height=SSS+"px";
	document.getElementById('taskmsg').style.display='block';
	document.getElementById('taskmsg').style.display='block';	
	document.getElementById('taskmsg').style.top=20+"px";
	document.getElementById('taskmsg').style.left=x+"px";
}
function CloseLogin(){
	document.getElementById('light').style.display='none'; 	
	document.getElementById('taskmsg').style.display='none'; 
}
</script>
